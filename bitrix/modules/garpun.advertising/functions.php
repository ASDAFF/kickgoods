<?

namespace garpun_advertising;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

function getDomainName() {
    global $SERVER_NAME;
    $domainSourse = \COption::GetOptionString("garpun.advertising", "file_domain_source");
    if ($domainSourse == "main_module") {
        $domain = \COption::GetOptionString("main", "server_name", $SERVER_NAME);
    } else {
        $domain = $_SERVER["HTTP_HOST"];
    }
    $domain = "http://" . preg_replace("/(^[\w\d\.]+)(:80)(\D+.*)?$/", "$1$3", $domain);
    return $domain;
}

function updateIblocks($linkIDIblock, $algId, $iblockId, $vids, $properties_id) {
    if (!$iblockId) {
        return \Bitrix\Main\Localization\Loc::GetMessage("GARPUN_ADVERTISING_IBLOCK_ISSET");
    }
    if (!is_array($vids) || empty($vids)) {
        return \Bitrix\Main\Localization\Loc::GetMessage("GARPUN_ADVERTISING_CATALOG_ISSET");
    }
    if (count($properties_id) == 1 && $properties_id[0] === "") {
        $properties_id = Array();
    }

    $a = \garpun_advertising\PropertyTable::getList(Array("filter" => Array("ID_ALGORITM" => $algId)));

    while ($id = $a->Fetch()) {
        $del = \garpun_advertising\PropertyTable::delete($id["ID"]);
        if (!$del->isSuccess()) {
            $errors = $a->getErrorMessages();
            break;
        }
    }



    if (empty($errors))
        if ($linkIDIblock) {
            $a = \garpun_advertising\IblockTable::delete($linkIDIblock);
            if (!$a->isSuccess())
                $errors = $a->getErrorMessages();
        }
    /* ------------------------------ */

//добавляем инфоблоки    
    if (empty($errors)) {
        $fieldsIblock = Array(
            "ID_ALGORITM" => $algId,
            "ID_IBLOCK" => $iblockId,
        );

        $resultIblock = \garpun_advertising\IblockTable::add($fieldsIblock);

        if ($resultIblock->isSuccess()) {
            $iblockID = $resultIblock->getID();
            // var_dump("IBLOCK_ID",$iblockID);
        } else {
            $errors = $resultIblock->getErrorMessages();
        }

//добавляем секции инфоблока   
        if (empty($errors)) {
            foreach ($vids as $section) {
                $fieldsSection = Array(
                    "ID_SECTION" => $section,
                    "ID_IBLOCK" => $iblockID,
                );

                $resultSection = \garpun_advertising\SectionTable::add($fieldsSection);

                if (!$resultSection->isSuccess()) {
                    $errors = $resultSection->getErrorMessages();
                }
            }
        }

        //добавляем свойства инфоблока   
        if (empty($errors)) {
            foreach ($properties_id as $id) {
                if (intval($id) > 0) {
                    $fieldsSection = Array(
                        "ID_PROPERTY" => $id,
                        "ID_ALGORITM" => $algId,
                    );

                    $resultProperty = \garpun_advertising\PropertyTable::add($fieldsSection);

                    if (!$resultProperty->isSuccess()) {
                        $errors = $resultProperty->getErrorMessages();
                        break;
                    }
                }
            }
        }
    }
    //var_dump($errors);die();
    if (empty($errors)) {
        return true;
    } else {
        return $errors;
    }
}

function startFileCreator($algId) {
    \garpun_advertising\authAgentUser();

    $arAlgoritm = \garpun_advertising\AlgoritmTable::GetById($algId)->fetch();
    if ($arAlgoritm) {
        if (intval($arAlgoritm["AGENT2_ID"])) {
            \Cagent::delete($arAlgoritm["AGENT2_ID"]);
            \garpun_advertising\AlgoritmTable::Update($algId, Array("AGENT2_ID" => 0));
            $hashOldFile=$arAlgoritm["TMP_HASH"];
            $fco = new \garpun_advertising\FileCreator($algId, Array(), Array(), false, Array(), Array(), $hashOldFile);
    $fco->deleteTmpFile();
         
        }
    } else {
        return false;
    }



    $properties = Array();

    $PropertyO = \garpun_advertising\PropertyTable::getList(Array("select" => Array("ID_PROPERTY"), "filter" => Array("ID_ALGORITM" => $algId)));



    while ($el = $PropertyO->Fetch()) {
        $properties[] = $el["ID_PROPERTY"];
    }

    $algO = \garpun_advertising\IblockTable::getList(Array("select" => Array("ID_IBLOCK", "SECTION.ID_SECTION",), "filter" => Array("ID_ALGORITM" => $algId)));

    $iblockId = false;
    $sections = Array();



    while ($el = $algO->Fetch()) {
        if (!$iblockId) {
            $iblockId = $el["ID_IBLOCK"];
        }
        $sections[] = $el["GARPUN_ADVERTISING_IBLOCK_SECTION_ID_SECTION"];
    }

    $offsets = Array("IBLOCK" => Array("LIMIT" => \COption::GetOptionString("garpun.advertising", "limit_iblock"), "OFFSET" => 0));

    $hashFile = date("Y-m-d_H-i-s");
    $fc = new \garpun_advertising\FileCreator($algId, $iblockId, $sections, false, $offsets, $properties, $hashFile);
    $fc->deleteTmpFile();
    $fc->addHeaderFile();

    $agent2Id = \CAgent::AddAgent($fc->ceventReturnString(), "garpun.advertising", "Y", 5);

    \garpun_advertising\AlgoritmTable::Update($algId, Array("AGENT2_ID" => $agent2Id,"TMP_HASH"=>  $hashFile));


    \garpun_advertising\logoutAgentUser();
    ob_clean();
    return "\garpun_advertising\startFileCreator($algId);";
}

function authAgentUser() {
    global $USER;
    if (!is_object($USER))
        $USER = new \CUser;
}

function logoutAgentUser() {
    global $USER;
    unset($USER);
}

function continueFileCreator($algId, $iblockId, $sections, $domain, $offsets, $properties, $hashFile) {
    \garpun_advertising\authAgentUser();

    global $USER;


    $fc = new \garpun_advertising\FileCreator($algId, $iblockId, $sections, false, $offsets, $properties, $hashFile);


    $rez = $fc->addGoodsFile();



    if ($rez) {
        \garpun_advertising\logoutAgentUser();
        ob_clean();
        return $fc->ceventReturnString();
    } else {

        $fc->addFooterFile();
        $fc->rename();
        \garpun_advertising\AlgoritmTable::Update($algId, Array("AGENT2_ID" => 0));
        $algoritm_o = \garpun_advertising\AlgoritmTable::GetList(Array("filter" => Array("ID" => $algId),
                    "select" => Array(
                        "ID", "EXTERNAL_ID", "PROJECT.EXTERNAL_ID", "STATE")));

        if ($algInfo = $algoritm_o->Fetch()) {
            if ($algInfo["STATE"] == "D") {
                \garpun_advertising\External\ExternalApi::createDumpAndStart($algInfo["ID"], $algInfo["EXTERNAL_ID"], $algInfo["GARPUN_ADVERTISING_ALGORITM_PROJECT_EXTERNAL_ID"]);
            }
        }
        \garpun_advertising\logoutAgentUser();
        ob_clean();
        return false;
    };
}

function printFormFields($fields) {
    foreach ($fields as $field):
        $field_option = $field[2];
        $val = $field_option["VALUE"];
        $req = false;
        if (isset($field_option["REQUIRED"]) && $field_option["REQUIRED"] == "Y") {
            $req = true;
        }
        ?>

        <div class="garpun_advertising_item">            
            <label for="<? echo htmlspecialcharsbx($field[0]) ?>">
                <?
                if (!$req)
                    echo $field[1];
                else
                    echo "<b>" . $field[1] . "*</b>";
                ?>:
            </label>            
            <? if ($field_option["TYPE"] == "checkbox"): ?>
                <input type="checkbox" id="<? echo htmlspecialcharsbx($field[0]) ?>" name="<? echo htmlspecialcharsbx($field[0]) ?>" value="Y"<? if ($val == "Y") echo" checked"; ?>>			
            <? elseif ($field_option["TYPE"] == "textarea"): ?>
                <textarea rows="<? echo $field_option[1] ?>" cols="<? echo $field_option[2] ?>" name="<? echo htmlspecialcharsbx($field[0]) ?>"><? echo htmlspecialcharsbx($val) ?></textarea>
            <? else: ?>
                <input type="<?= $field_option["TYPE"] ?>"  maxlength="255" value="<? echo htmlspecialcharsbx($val) ?>" name="<? echo htmlspecialcharsbx($field[0]) ?>">
            <? endif ?>
            <?
            if ($field_option["AFTER_TEXT"]) {
                echo $field_option["AFTER_TEXT"];
            }
            ?>
        </div>
        <?
    endforeach;
}

/* ---------------------------------------------------------------------- */

function keyArrayCreator($array, $key, $multi = false) {
    $parseArray = Array();
    if (!$multi) {
        foreach ($array as $value) {
            $parseArray[$value[$key]] = $value;
        }
    } else {
        foreach ($array as $value) {
            $parseArray[$value[$key]][] = $value;
        }
    }
    return $parseArray;
}
