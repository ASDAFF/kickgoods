<?php

namespace garpun_advertising;

define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);
define('NO_AGENT_CHECK', true);

class FileCreator {

    static public $filePath;
    private $iblockId;
    private $filePathOb;
    static public $domain;
    private $sections;
    private $properties;
    private $offsets;
    private $scu;
    private $userTypes = Array();
    private $algId;
private $hashFile;
    static function getHttpAddress($algId) {
        return str_replace(Array("#ALGORITM_ID#", $_SERVER["DOCUMENT_ROOT"]), Array($algId, FileCreator::$domain), FileCreator::$filePath["ready"]);
    }

    function __construct($algId, $iblockId, $sections, $domain, $offsets, $properties, $hashFile) {
        if (!(
                \CModule::IncludeModule("catalog") || \CModule::IncludeModule("sale")
                )) {
            return false;
        }
        
        $this->filePathOb=self::$filePath;
        $this->algId = $algId;
        $this->hashFile=$hashFile; 
        
      
        $arAlgoritm = \garpun_advertising\AlgoritmTable::GetById($algId)->fetch();
        

        
        $this->filePathOb["tmp"] = str_replace(Array("#ALGORITM_ID#", "#HASH#"), Array($algId, $hashFile), FileCreator::$filePath["tmp"]);
        $this->filePathOb["ready"] = str_replace(Array("#ALGORITM_ID#", "#HASH#"), Array($algId, $hashFile), FileCreator::$filePath["ready"]);

       
        //\CEventLog::Log("INFO", "__construct", "garpun.advertising", false, print_r(FileCreator::$filePath, true));
        $this->iblockId = $iblockId;
        $this->sections = is_array($sections) ? $sections : unserialize($sections);
        $this->offsets = is_array($offsets) ? $offsets : unserialize($offsets);
        $f = \CCatalog::GetByID($this->iblockId);
        $this->scu = ($f["OFFERS_IBLOCK_ID"]) ? $f["OFFERS_IBLOCK_ID"] : false;
        $this->properties = is_array($properties) ? $properties : unserialize($properties);

        if (!empty($this->properties)) {
            $types = \CIBlockProperty::GetList();
            while ($type = $types->Fetch()) {
                if (!empty($type["USER_TYPE"])) {
                    $this->userTypes[$type["USER_TYPE"]] = \CIBlockProperty::GetUserType($type["USER_TYPE"]);
                }
            }
        }
    }

    static function init($filePath, $domain) {
        // var_dump($domain);
        FileCreator::$filePath = is_array($filePath) ? $filePath : unserialize($filePath);
        FileCreator::$domain = $domain;
    }

    private function printOffer($of, $el = Array(), $properties = Array()) {

        $id = ($of["ID"]) ? $of["ID"] : $el["ID"];
        $iblockId = ($of["IBLOCK_ID"]) ? $of["IBLOCK_ID"] : $el["IBLOCK_ID"];
        $arItem = \CCatalogProduct::GetByID($id);
        $arPrice = \CPrice::GetBasePrice($id);
        $arPriceType = \CCatalogGroup::GetBaseGroup();


        if (isset($arItem["QUANTITY"]) && $arItem["QUANTITY"] < 1) {
            return "";
        }


        $originalPrice = ($arPrice["CURRENCY"] != "RUB") ? $arPrice["PRICE"] : "&nbsp;";
        $price = round(\CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], "RUB"), 2);

        $xml = "";

        //$xml .= "        <offer id=\"$id\"  type=\"vendor.model\"  available=\"" . (!$catalog["QUANTITY"] && $catalog["QUANTITY_TRACE"] == "Y" ? "false" : "true") . "\">" . PHP_EOL;
        $xml .= "        <offer id=\"$id\"  type=\"vendor.model\" >" . PHP_EOL;
        $xml .= "          <url>" . FileCreator::$domain . "{$of["DETAIL_PAGE_URL"]}" . "</url>" . PHP_EOL;
        $xml .= "          <price>{$price}</price>" . PHP_EOL;
        $xml .= "          <currencyId>RUB</currencyId>" . PHP_EOL;
        $xml .= "          <categoryId>" . ((isset($of["IBLOCK_SECTION_ID"])) ? $of["IBLOCK_SECTION_ID"] : $el["IBLOCK_SECTION_ID"]) . "</categoryId>" . PHP_EOL;
        $xml .= "          <picture>" . FileCreator::$domain . \CFile::GetPath(
                        (isset($of["DETAIL_PICTURE"])) ? $of["DETAIL_PICTURE"] : $el["DETAIL_PICTURE"]
                ) . "</picture>" . PHP_EOL;

        foreach ($properties as $prop) {
            $param = "";
            if (!empty($prop["VALUE"])) {

                $param = $this->getPropertyValue($prop);
            }
            $porperty_print = "          <vendor>" . htmlspecialcharsbx($param, ENT_QUOTES) . "</vendor>" . PHP_EOL;
            $xml .=$porperty_print;
        }


        $xml .= "          <model>" . htmlspecialcharsbx($of["NAME"], ENT_QUOTES) . "</model>" . PHP_EOL;
        $text = $of["PREVIEW_TEXT"];
        if (empty($text)) {
            if (!empty($of["DETAIL_TEXT"])) {
                $text = $of["DETAIL_TEXT"];
            } elseif (!empty($el["PREVIEW_TEXT"])) {
                $text = $el["PREVIEW_TEXT"];
            }if (!empty($el["DETAIL_TEXT"])) {
                $text = $el["DETAIL_TEXT"];
            }
        }
        $text = htmlspecialcharsbx(trim(strip_tags(str_replace("&nbsp;", " ", $text))), ENT_QUOTES);
        $xml .= "          <description>" .
                $text
                . "</description>" . PHP_EOL;






        $xml .= "        </offer>" . PHP_EOL;
        return $xml;
    }

    private function getPropertyValue($arProperty) {
        $value = "";
        if (isset($this->userTypes[$arProperty['USER_TYPE']])) {
            if (!is_array($arProperty["VALUE"])) {
                $arProperty["VALUE"] = Array($arProperty["VALUE"]);
            }
            foreach ($arProperty["VALUE"] as $val) {
                $value[] = call_user_func_array($this->userTypes[$arProperty['USER_TYPE']]["GetAdminListViewHTML"], array(
                    $arProperty,
                    array("VALUE" => $val),
                    array('MODE' => 'SIMPLE_TEXT'),
                ));
                ;
            }
            $value = join($value, ", ");
        } else {
            $type = $arProperty['PROPERTY_TYPE'];
            switch ($type) {

                case 'USER_TYPE':
                    if (!empty($arProperty['VALUE'])) {
                        if (is_array($arProperty['VALUE'])) {
                            $arValues = array();
                            foreach ($arProperty["VALUE"] as $oneValue) {
                                $arValues[] = call_user_func_array($this->userTypes[$arProperty['PROPERTY_TYPE']], array(
                                    $arProperty,
                                    array("VALUE" => $oneValue),
                                    array('MODE' => 'SIMPLE_TEXT'),
                                ));
                            }
                            $value = implode(', ', $arValues);
                        } else {
                            $value = call_user_func_array($this->userTypes[$arProperty['PROPERTY_TYPE']], array(
                                $arProperty,
                                array("VALUE" => $arProperty["VALUE"]),
                                array('MODE' => 'SIMPLE_TEXT'),
                            ));
                        }
                    }
                    break;
                case 'E':
                    if (!empty($arProperty['VALUE'])) {
                        $arCheckValue = array();
                        if (!is_array($arProperty['VALUE'])) {
                            $arProperty['VALUE'] = intval($arProperty['VALUE']);
                            if (0 < $arProperty['VALUE'])
                                $arCheckValue[] = $arProperty['VALUE'];
                        }
                        else {
                            foreach ($arProperty['VALUE'] as &$intValue) {
                                $intValue = intval($intValue);
                                if (0 < $intValue)
                                    $arCheckValue[] = $intValue;
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        if (!empty($arCheckValue)) {
                            $dbRes = \CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arProperties[$PROPERTY]['LINK_IBLOCK_ID'], 'ID' => $arCheckValue), false, false, array('NAME'));
                            while ($arRes = $dbRes->Fetch()) {
                                $value .= ($value ? ', ' : '') . $arRes['NAME'];
                            }
                        }
                    }
                    break;
                case 'G':
                    if (!empty($arProperty['VALUE'])) {
                        $arCheckValue = array();
                        if (!is_array($arProperty['VALUE'])) {
                            $arProperty['VALUE'] = intval($arProperty['VALUE']);
                            if (0 < $arProperty['VALUE'])
                                $arCheckValue[] = $arProperty['VALUE'];
                        }
                        else {
                            foreach ($arProperty['VALUE'] as &$intValue) {
                                $intValue = intval($intValue);
                                if (0 < $intValue)
                                    $arCheckValue[] = $intValue;
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        if (!empty($arCheckValue)) {
                            $dbRes = \CIBlockSection::GetList(array(), array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ID' => $arCheckValue), false, array('NAME'));
                            while ($arRes = $dbRes->Fetch()) {
                                $value .= ($value ? ', ' : '') . $arRes['NAME'];
                            }
                        }
                    }
                    break;
                case 'L':
                    if (!empty($arProperty['VALUE'])) {
                        if (is_array($arProperty['VALUE']))
                            $value .= implode(', ', $arProperty['VALUE']);
                     
                            $value .= $arProperty['VALUE'];
                    }
                    break;
                case "F":
                    if (!is_array($arProperty["VALUE"])) {
                        $arProperty["VALUE"] = Array($arProperty["VALUE"]);
                    }
                    foreach ($arProperty["VALUE"] as $oneValue) {
                        $value[] = FileCreator::$domain . \CFile::GetPath($oneValue);
                    }
                    $value = join($value, ", ");
                    break;
                default:

                    $value = is_array($arProperty['VALUE']) ? implode(', ', $arProperty['VALUE']) : $arProperty['VALUE'];
            }
        }
        $param = $value;
        return $param;
    }

    private function getPropertyArray($iblockId, $elementId) {
        $props = Array();
        $props_o = \CIBlockElement::GetByID($elementId);
        if ($props_e = $props_o->GetNextElement()) {
            foreach ($this->properties as $prop_id) {
                $props[] = $props_e->GetProperty($prop_id);
            }
        }

        return $props;
    }

    public function addHeaderFile() {

        $site = "http://" . $_SERVER["HTTP_HOST"];
        $xml = "<?xml version=\"1.0\" encoding=\"windows-1251\"?>
<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">
<yml_catalog date=\"" . date("Y-m-d H:i") . "\">
  <shop>
    <name>" . \COption::GetOptionString("main", "site_name") . "</name>
    <company>" . \COption::GetOptionString("main", "site_name") . "</company>
    <url>" . FileCreator::$domain . "</url>
    <currencies>
        <currency id=\"RUB\" rate=\"1\" />
    </currencies>
    <categories>
";

        $cSectionsFilter = array("IBLOCK_ID" => $this->iblockId);
        if (array_sum($this->sections) != 0 && array_search(0, $this->sections) === false) {
            $cSectionsFilter["ID"] = $this->sections;
        }
        $cSections = \CIBlockSection::GetList(array("LEFT_MARGIN" => "ASC"), $cSectionsFilter, false, array("ID", "NAME", "IBLOCK_SECTION_ID", "SECTION_PAGE_URL"));
        $arSections = array();
        while ($sec = $cSections->GetNext()) {
            $xml .= "      <category id=\"{$sec["ID"]}\"" . ($sec["IBLOCK_SECTION_ID"] ? " parentId=\"{$sec["IBLOCK_SECTION_ID"]}\"" : "") . ">" .
                    htmlspecialcharsbx(trim(strip_tags(str_replace("&nbsp;", " ", ($sec["NAME"])))), ENT_QUOTES)
                    . "</category>" . PHP_EOL;
        }
        $xml .= "    </categories>" . PHP_EOL;
        $xml .= "    <offers>" . PHP_EOL;
        $this->printToFile($xml);
       
    }

    public function rename() {
        if (file_exists($this->filePathOb["ready"])) {
            unlink($this->filePathOb["ready"]);
        }
        rename($this->filePathOb["tmp"], $this->filePathOb["ready"]);
    }

    private function printToFile($xml) {
        $f = fopen($this->filePathOb["tmp"], 'ab');
        fputs($f, $GLOBALS["APPLICATION"]->ConvertCharset($xml, LANG_CHARSET, "windows-1251"));
        fclose($f);
        
      //  \CEventLog::Log("INFO", "__construct", "garpun.advertising", false, print_r(FileCreator::$filePath["tmp"], true). $this->hashFile .$xml);
    }

    function deleteTmpFile() {
        if (file_exists($this->filePathOb["tmp"])) {
            unlink($this->filePathOb["tmp"]);
        }
    }

    public function ceventReturnString() {

        return "\garpun_advertising\continueFileCreator('" . $this->algId . "', '$this->iblockId', '"
                . serialize($this->sections) . "', 'false', '" . serialize($this->offsets) . "', '" . serialize($this->properties) . "','" . $this->hashFile . "');";
    }

    public function addGoodsFile() {
        $filter = array(
            "IBLOCK_ID" => $this->iblockId,
            "ACTIVE" => "Y",
                //"CATALOG_AVAILABLE"=>"Y",
        );
        if (array_sum($this->sections) != 0 && array_search(0, $this->sections) === false) {

            $filter["SECTION_ID"] = $this->sections;
            $filter["INCLUDE_SUBSECTIONS"] = "Y";
        }

        $selectedFields = array("ID", "NAME", "DETAIL_PAGE_URL", "CODE", "IBLOCK_SECTION_ID", "DETAIL_PICTURE", "PREVIEW_TEXT", "DETAIL_TEXT");


        $off = Array(
            "nPageSize" => $this->offsets["IBLOCK"]["LIMIT"],
            "iNumPage" => $this->offsets["IBLOCK"]["OFFSET"],
        );




        //  die();
        $cElements = \CIBlockElement::GetList(
                        array("ID" => "ASC"), $filter, false, $off, $selectedFields
        );
        \CEventLog::Log("INFO", "addGoodsFile3", "garpun.advertising", false, print_r($off, true) . print_r($filter, true));
        $i = false;


        $cElements->NavStart();
        if ($cElements->NavPageCount < $this->offsets["IBLOCK"]["OFFSET"]) {
            return false;
        }

        while ($el = $cElements->GetNext()) {

            $props = Array();
            $i = true;
            $props = $this->getPropertyArray($this->iblockId, $el["ID"]);
            if ($this->scu) {
                $hasOffer = false;
                $cOffers = \CIBlockElement::GetList(array(), array("IBLOCK_ID" => $this->scu, "ACTIVE" => "Y", "PROPERTY_CML2_LINK" => $el["ID"],), false, false, $selectedFields);
                while ($of = $cOffers->GetNext()) {
                    $hasOffer = true;
                    $xml .=$this->printOffer($of, $el, $props);
                }
                if (!$hasOffer) {
                    $xml .=$this->printOffer($el, false, $props);
                }
            } else {
                $xml .=$this->printOffer($el, false, $props);
            }
        }

        if ($i) {
            $this->offsets["IBLOCK"]["OFFSET"] ++;
            $this->printToFile($xml);
            return true;
        } else {
            return false;
        }
    }

    public function addFooterFile() {
        $xml .= "    </offers>" . PHP_EOL;
        $xml .= "  </shop>" . PHP_EOL;
        $xml .= "</yml_catalog>" . PHP_EOL;
        $this->printToFile($xml);
    }

}
