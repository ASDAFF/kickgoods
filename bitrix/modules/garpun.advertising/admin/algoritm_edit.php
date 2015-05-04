<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
CAjax::Init();
?><script type="text/javascript" src="/bitrix/js/garpun.advertising/admin.js"></script>

<style type="text/css">
    @import url("/bitrix/js/garpun.advertising/admin.css");       
</style>
<?
CJSCore::Init(array("jquery"));
/**/
$note = Array();
if (isset($_REQUEST["note"])) {
    $note[] = $_REQUEST["note"];
}

$stateUpdate = "N";
if (isset($_POST["STATE"]) && is_array($_POST["STATE"])) {
    $stateUpdateArr = $_REQUEST["STATE"];
    current($stateUpdateArr);
    $stateUpdate = key($stateUpdateArr);
}
/**/

/* -------- Обработка Get массива ------------ */
$property_products = Array();
$property_scu = Array();
if (isset($_REQUEST["PROPERTIES_PRODUCTS"]) && is_array($_REQUEST["PROPERTIES_PRODUCTS"])) {
    $property_products = $_REQUEST["PROPERTIES_PRODUCTS"];
}

if (isset($_REQUEST["PROPERTIES_SCU"]) && is_array($_REQUEST["PROPERTIES_SCU"])) {
    $property_scu = $_REQUEST["PROPERTIES_SCU"];
}

/**/
$fields_algoritm = Array(
    "STATE_OLD" => isset($_POST["STATE_OLD"]) ? $_POST["STATE_OLD"] : $stateUpdate,
    "ID" => $_REQUEST["ID"],
    "ACTION" => $_REQUEST["action"],
    "STATE" => $stateUpdate,
    "NAME" => $_POST["NAME"],
    "TYPE" => $_POST["TYPE"],
    "PATH" => $_POST["PATH"],
    "IBLOCK" => Array("ID_IBLOCK" => $_POST["IBLOCK_ID"]),
    "PROPERTY" => array_merge($property_scu, $property_products),
    "SECTION" => $_POST["SECTION"],
    "TYPE_ALGORITM" => $_POST["TYPE_ALGORITM"],
    "GEO" => $_POST["GEO"],
    "PROJECT_ID" => isset($_POST["PROJECT_ID"]) ? $_POST["PROJECT_ID"] : $_GET["PROJECT_ID"],
    "ENGINE_SETTING" => $_POST["ENGINE_SETTING"],
    "UPDATE_TIME" => (isset($_POST["UPDATE_TIME"])) ? $_POST["UPDATE_TIME"] : "weekly"
);
$state;
include (__DIR__ . "/../util/state.php");

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . "/menu.php");
if ($fields_algoritm["ACTION"] == "edit") {
    $APPLICATION->SetTitle(Loc::GetMessage("GARPUN_ADVERTISING_FILE_TITLE_REDACTION"));
} else {
    $APPLICATION->SetTitle(Loc::GetMessage("GARPUN_ADVERTISING_FILE_TITLE_NEW"));
}
\Bitrix\Main\Loader::IncludeModule("garpun.advertising");
\Bitrix\Main\Loader::IncludeModule("iblock");

// save or applay
use Bitrix\Main\Type as FieldType;

//$stateUpdate = ($stateUpdate) ? $stateUpdate : $row["STATE"];

/* ---------- SUBMIT ------------ */
if ($fields_algoritm["ID"] && $fields_algoritm["ACTION"] == "reloadXML" && check_bitrix_sessid()) {

    /**/
    $algId = $_REQUEST["ID"];
    $j = CAgent::RemoveAgent("\garpun_advertising\startFileCreator($algId);", "garpun.advertising");
    $agentId = CAgent::AddAgent("\garpun_advertising\startFileCreator($algId);", "garpun.advertising", "Y");
    if ($agentId) {
        $algoritm_o = \garpun_advertising\AlgoritmTable::Update($algId, Array("AGENT_ID" => $agentId));
        $query = http_build_query(Array("action" => "edit"));

        LocalRedirect($APPLICATION->GetCurPageParam($query, Array("action", "sessid")));
    }



    /**/
}if (isset($_REQUEST["notsave"])) {
    LocalRedirect("/bitrix/admin/garpun.advertising_algoritm_list.php?lang=" . LANG);
} elseif (isset($_REQUEST["action"], $_REQUEST["ID"]) &&
        (isset($_REQUEST["save"]) || isset($_REQUEST["apply"]) || $stateUpdate
        ) && check_bitrix_sessid()) {
    $fields = Array();

    if (!empty($_REQUEST["NAME"])) {
        $fields["NAME"] = $fields_algoritm["NAME"];
    }
    if (!empty($_REQUEST["TYPE"])) {
        $fields["TYPE"] = $fields_algoritm["TYPE"];
    }
    $fields["TIME"] = new FieldType\DateTime();

    if ($fields_algoritm["STATE"]) {
        $fields["STATE"] = $fields_algoritm["STATE"];
    }

    if (!is_array($fields_algoritm["GEO"]) || empty($fields_algoritm["GEO"])) {

        $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_GEO_SETTING");
    }


    if (!is_array($fields_algoritm["ENGINE_SETTING"]) || empty($fields_algoritm["ENGINE_SETTING"])) {
        $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_ENGINE_SETTING");
    } elseif (is_array($fields_algoritm["ENGINE_SETTING"])) {

        $engineSettingsError = false;
        foreach ($fields_algoritm["ENGINE_SETTING"] as $k => $value) {
            $value["PRICE"] = str_replace(",", ".", $value["PRICE"]);
            $fields_algoritm["ENGINE_SETTING"][$k]["PRICE"] = $value["PRICE"];
            if (empty($value["PRICE"]) && !empty($value["ID"])) {
                $engineSettingsError = true;
                //break;
            }
            if (!empty($value["ID"]) && !floatval($value["PRICE"])) {
                $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_ENGINE_SETTING_PRICE_BAD");
            }
        }
        if ($engineSettingsError) {
            $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_ENGINE_SETTING_PRICE");
        }
    }





    if ($_REQUEST["action"] == "new" && empty($errors)) {
        $deleteAlgoritmIfError = true;
        $fields["PROJECT_ID"] = $fields_algoritm["PROJECT_ID"];
        $fields["DATE_CREATE"] = new FieldType\DateTime();
        $algId = false;

        if ($fields["TYPE"] == "Y") {
            $fields["PATH"] = $fields_algoritm["PATH"];
            if ($fields["PATH"]) {
                $fields["PATH"] = str_replace('http://', '', $fields["PATH"]);
                $fields["PATH"] = str_replace('https://', '', $fields["PATH"]);
                $fields["PATH"] = 'http://' . $fields["PATH"];
                $Headers = @get_headers($fields["PATH"]);
                if (!preg_match("|200|", $Headers[0])) {
                    $fields["PATH"] = str_replace('http://', 'https://', $fields["PATH"]);
                    $Headers = @get_headers($fields["PATH"]);
                    if (!preg_match("|200|", $Headers[0])) {
                        $fields["PATH"] = str_replace('https://', '', $fields["PATH"]);
                        $fields["PATH"] = 'http://' . $_SERVER['HTTP_HOST'] . $fields["PATH"];
                        $Headers = @get_headers($fields["PATH"]);
                        if (!preg_match("|200|", $Headers[0])) {
                            $fields["PATH"] = str_replace('http://', 'https://', $fields["PATH"]);
                            $Headers = @get_headers($fields["PATH"]);
                            if (!preg_match("|200|", $Headers[0])) {
                                $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_404_PATH");
                            }
                        }
                    }
                }
            } else {
                $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_404_PATH");
            }
            $fields_algoritm["PATH"] = $fields["PATH"];
        }

        if (empty($errors)) {
            $k = \garpun_advertising\AlgoritmTable::add($fields);
            if ($k->isSuccess()) {
                $algId = $k->getId();

                if ($fields["TYPE"] == "C") {
                    $fields["PATH"] = $fields_algoritm["PATH"] = \garpun_advertising\FileCreator::getHttpAddress($algId);


                    $k = \garpun_advertising\AlgoritmTable::Update($algId, Array("PATH" => $fields["PATH"]));
                }
            } else {


                $errors[] = $k->getErrorMessages();
            }
        }
        if (empty($errors) && $fields["TYPE"] == "C") {
            if (isset($_REQUEST["IBLOCK_ID"])) {

                if (intval($_REQUEST["IBLOCK_ID"]) && isset($_REQUEST["SECTION"]) && is_array($_REQUEST["SECTION"])) {

                    if (array_sum($fields_algoritm["PROPERTY"]) > 0) {

                        $resIblock = \garpun_advertising\updateIblocks(false, $algId, $fields_algoritm["IBLOCK"]["ID_IBLOCK"], $fields_algoritm["SECTION"], $fields_algoritm["PROPERTY"]);
                        if ($resIblock !== true) {
                            $errors[] = $resIblock;
                        }
                    } else {
                        $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_NO_PROPERTY");
                    }
                } else {
                    $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_NO_SECTIONS");
                }
            } else {
                $errors[] = Loc::getMessage("GARPUN_ADVERTISING_FILE_ERROR_NO_INFOBLOCK");
            }
        }
        /**/

        /* -- // создание алгоритма удаленно  -- */
        if (empty($errors)) {
            $projects = \garpun_advertising\Save::getUsersProjectList();
            $fields_algoritm["EXTERNAL_PROJECT_ID"] = $projects[$fields_algoritm["PROJECT_ID"]]["EXTERNAL_ID"];

            $externalAlgoritmAnswer = \garpun_advertising\External\ExternalApi::addAlgoritm($fields_algoritm);
            //var_dump($externalAlgoritmAnswer);
            if ($externalAlgoritmAnswer->isSuccess()) {
                $external_id = $externalAlgoritmAnswer->getAnswer("id");
                $algoritm_o = \garpun_advertising\AlgoritmTable::Update($algId, Array("EXTERNAL_ID" => $external_id));
                if (!$algoritm_o->isSuccess()) {
                    $errors[] = $algoritm_o->getErrorMessages();
                }
            } else {
                $errors[] = $externalAlgoritmAnswer->getErrorString();
            }
        }
        /* -- // создание дампа для   -- */
        if (empty($errors)) {

            $external_project = garpun_advertising\Save::getUsersProjectList(true);
            $external_project_id = $external_project["EXTERNAL_ID"];

            if ($fields_algoritm["TYPE"] == "Y") {
                $dumpAnswer = \garpun_advertising\External\ExternalApi::createDumpAndStart($algId, $external_id, $external_project_id);
                if ($dumpAnswer !== true) {
                    $errors[] = $dumpAnswer;
                }
            }
        }
        /* --- */

        if (empty($errors)) {
            if ($fields_algoritm["STATE"] == "D") {
                if (isset($fields_algoritm["TYPE"]) && $fields_algoritm["TYPE"] == "C") {

                    $agentId = CAgent::AddAgent("\garpun_advertising\startFileCreator($algId);", "garpun.advertising", "Y");
                    if ($agentId) {
                        $algoritm_o = \garpun_advertising\AlgoritmTable::Update($algId, Array("AGENT_ID" => $agentId));
                    }
                }
            }

            LocalRedirect($APPLICATION->GetCurPage() . "?ID=" . IntVal($algId) . "&action=edit&PROJECT_ID={$fields_algoritm["PROJECT_ID"]}&lang=" . LANG);
        } else {
            if ($algId) {
                if ($deleteAlgoritmIfError) {
                    $k = \garpun_advertising\AlgoritmTable::delete($algId);
                }
                if (!$k->isSuccess()) {
                    $errors = $k->getErrorMessages();
                }
            }
        }

        /**/
    } elseif ($fields_algoritm["ACTION"] == "edit" && empty($errors)) {

        if ($fields_algoritm["STATE"]) {
            foreach ($stateList[$fields_algoritm["STATE"]] as $k => $f) {
                if ($f["READONLY"] != "N") {
                    unset($fields[$k]);
                }
            }
        }
        $k = \garpun_advertising\AlgoritmTable::update($fields_algoritm["ID"], $fields);

        if ($k->isSuccess()) {



            $row_o = \garpun_advertising\AlgoritmTable::getList(Array(
                        "filter" => Array("ID" => $fields_algoritm["ID"]),
                        "select" => Array("ID", "PROJECT.EXTERNAL_ID", "EXTERNAL_ID")));
            if ($row = $row_o->Fetch()) {
                $fields_algoritm["PROJECT_EXTERNAL_ID"] = $row["GARPUN_ADVERTISING_ALGORITM_PROJECT_EXTERNAL_ID"];
                $fields_algoritm["EXTERNAL_ID"] = $row["EXTERNAL_ID"];
            }


            if (!isset($fields_algoritm["GEO"]) || empty($fields_algoritm["GEO"])) {
                $fields_algoritm["GEO"] = Array();
            }


            $AlgoritmExternalAnswer = \garpun_advertising\External\ExternalApi::updateAlgoritm($fields_algoritm["EXTERNAL_ID"], $fields_algoritm);

            /**/
            if ($AlgoritmExternalAnswer !== true) {
                $errors = $AlgoritmExternalAnswer;
            } else {
                
            }
            if (empty($errors)) {
                if (isset($_REQUEST["save"])) {
                    LocalRedirect("garpun.advertising_algoritm_list.php" . "?lang=" . LANG);
                } elseif (isset($_REQUEST["apply"]) || $stateUpdate) {
                    //
                    if (in_array($stateUpdate, Array("S"))) {
                        LocalRedirect("garpun.advertising_algoritm_list.php" . "?lang=" . LANG);
                    } else {
                        LocalRedirect($APPLICATION->GetCurPageParam("", array("stop_bizproc", "sessid")));
                    }
                }
            }
        } else {
            $errors = $k->getErrorMessages();
        }
    } elseif ($_REQUEST["action"] == 'delete') {
        $res = \garpun_advertising\AlgoritmTable::delete($_REQUEST["ID"]);
        if (!$res->isSuccess()) {
            $errors = $res->getErrorMessages();
        } else {
            LocalRedirect("garpun.advertising_algoritm_list.php" . "?lang=" . LANG);
        }
    }

    if (!empty($errors)) {
        $row = $fields_algoritm;
        $row["STATE"] = $row["STATE_OLD"];
    }
} else {

    $row = Array();
    /* -------- Открытие алгоритма ------------ */
    if ($_REQUEST['action'] == "new") {


        $row = Array(
            "NAME" => GetMessage("GARPUN_ADVERTISING_ALGORITM_NEW_NAME"),
            "PATH" => "",
            "PROJECT_ID" => $fields_algoritm["PROJECT_ID"],
            "GARPUN_ADVERTISING_ALGORITM_PROJECT_NAME" => '',
            "GARPUN_ADVERTISING_ALGORITM_PROJECT_SITE" => '',
            "STATE" => "N",
            "UPDATE_TIME" => "weekly",
            "GEO" => Array("RU"),
        );
    } else {
        /* Алгоритм */
        $row_o = \garpun_advertising\AlgoritmTable::getList(Array(
                    "filter" => Array("ID" => $fields_algoritm["ID"]),
                    "select" => Array("ID", "NAME", "PATH", "TYPE", "STATE", "PROJECT_ID", "PROJECT.EXTERNAL_ID", "EXTERNAL_ID","AGENT2_ID")));
        if (!$row = $row_o->Fetch()) {
            $errors = "Алгоритм не найден";
        } else {
            $fields_algoritm["STATE_OLD"] = $row["STATE_OLD"] = $row["STATE"];

            $lastDump = \garpun_advertising\External\ExternalApi::getLastDump($row["EXTERNAL_ID"]);
            if (is_array($lastDump)) {
                $row["DUMP"] = $lastDump;
                if ($row["STATE"] == "D" && $lastDump["status"] == "done") {
                    $row["STATE"] = "R";
                    $rowUpdate_o = \garpun_advertising\AlgoritmTable::update($row["ID"], Array("STATE" => "S"));
                }
            } else {
                $errors[] = $lastDump;
            }

            $externalAlgoritmData = \garpun_advertising\External\ExternalApi::getReference(Array("projects", $row["GARPUN_ADVERTISING_ALGORITM_PROJECT_EXTERNAL_ID"], "algorithms", $row["EXTERNAL_ID"]));

            if (is_array($externalAlgoritmData)) {
                $row["GEO"] = (is_array($externalAlgoritmData["geo"])) ? $externalAlgoritmData["geo"] : Array($externalAlgoritmData["geo"]);
                $row["TYPE_ALGORITM"] = $externalAlgoritmData["type"];
                $row["UPDATE_TIME"] = $externalAlgoritmData["updateSchedule"];
                //engineSettings
                if (is_array($externalAlgoritmData["engineSettings"])) {
                    foreach ($externalAlgoritmData["engineSettings"] as $engineSetting) {
                        $row["ENGINE_SETTING"][$engineSetting["engine"]] = Array("PRICE" => $engineSetting["defaultClickPrice"], "ID" => $engineSetting["externalAccountId"]);
                    }
                }
            } else {
                $errors[] = $externalAlgoritmData;
            }
        }

        /* �?нфоблок */
        $iblock_o = \garpun_advertising\IblockTable::getList(Array(
                    "filter" => Array("ID_ALGORITM" => $row["ID"]),
                    "select" => Array("ID", "ID_IBLOCK")));
        if ($r = $iblock_o->Fetch()) {
            $row["IBLOCK"] = $r;

            /* Секции */
            $sections_o = \garpun_advertising\SectionTable::getList(Array(
                        "filter" => Array("ID_IBLOCK" => $row["IBLOCK"]["ID"]),
                        "select" => Array("ID", "ID_SECTION")
            ));
            while ($s = $sections_o->Fetch()) {
                $row["SECTION"][$s["ID"]] = $s;
            }
            /**/
        }
        /* Свойства */
        $Property_o = \garpun_advertising\PropertyTable::getList(Array(
                    "filter" => Array("ID_ALGORITM" => $row["ID"]),
                    "select" => Array("ID_PROPERTY")));
        $row["PROPERTY"] = Array();
        while ($r = $Property_o->Fetch()) {
            $row["PROPERTY"][] = $r["ID_PROPERTY"];
        }
    }
}

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("GARPUN_ADVERTISING_I__ALGORITM_TAB"),
        "TITLE" =>
        ($row["STATE"] == "D" ? Loc::getMessage("GARPUN_ADVERTISING_FILE_TITLE_GENERATION", Array("NAME" => $row["NAME"])) :
                (($_REQUEST['action'] == "new") ? Loc::getMessage("GARPUN_ADVERTISING_FILE_TITLE_NEW") : Loc::getMessage("GARPUN_ADVERTISING_FILE_TITLE_REDACTION"))))
);

$tabControl = new CAdminTabControl("file_edit", $aTabs);



//view

if ($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
    echo Loc::getMessage("GARPUN_ADVERTISING_LOGO_FILE", \garpun_advertising\External\ExternalApi::prepareHeaderUrl(\garpun_advertising\Save::getUser("EXTERNAL_ADHANDS_ID")));
}



$aMenu = array(
    array(
        "TEXT" => GetMessage('GARPUN_ADVERTISING_ALGORITM_EDIT_RETURN_TO_LIST_BUTTON'),
        "TITLE" => GetMessage('GARPUN_ADVERTISING_ALGORITM_EDIT_RETURN_TO_LIST_BUTTON'),
        "LINK" => "garpun.advertising_algoritm_list.php?&lang=" . LANGUAGE_ID,
        "ICON" => "btn_list",
    )
);
/*
if ($row["TYPE"] == "C") {

    $aMenu[] = array(
        "TEXT" => GetMessage('GARPUN_ADVERTISING_ALGORITM_EDIT_RELOAD_YML'),
        "TITLE" => GetMessage('GARPUN_ADVERTISING_ALGORITM_EDIT_RELOAD_YML'),
        "LINK" => $APPLICATION->GetCurPageParam("action=reloadXML&" . bitrix_sessid_get(), array("action")),
        "ICON" => '',
    );
}
*/
$context = new CAdminContextMenu($aMenu);



$context->Show();

/* references */
$referencesList = Array(
    "ALGORITM_TYPE" => Array(Array("references", "algorithm-types"), "code", false),
    "ENGINE_SETTING" => Array(Array("external-accounts"), "engine", true),
    "UPDATE_TIME" => Array(Array("references", "update-schedules"), "code", false),
    "GEO" => Array(Array("references", "geo"), "code", false),
    "CREDENTIALS" => Array(Array("credentials"), "engine", false),
);

$references = \garpun_advertising\External\ExternalApi::getReferencesArray($referencesList);

/**/

if (!empty($note)) {
    if (!is_array($note)) {
        $errors = Array($note);
    }
    CAdminMessage::ShowNote(join("\n", $note));
}


if (!empty($errors)) {

    if (!is_array($errors)) {
        $errors = Array($errors);
    }
    CAdminMessage::ShowMessage(join("\n", $errors));
}


$tabControl->Begin();
if (\garpun_advertising\External\ExternalApi::notNeedPay(true)) {
    if (!\garpun_advertising\External\ExternalRequestResult::$globalError) {
        ?>


        <form method="post" name="file_edit_form" class="garpun_advertising_block_algoritm" action="<?= $APPLICATION->GetCurPageParam("", array("stop_bizproc", "sessid")); ?>">

        <?= bitrix_sessid_post() ?>
            <input type="hidden" name="ID" value="<?= $row['ID'] ? $row['ID'] : "0" ?>">
            <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
            <input type="hidden" name="PROJECT_ID" value="<?= $fields_algoritm['PROJECT_ID'] ?>">
            <input type="hidden" name="STATE_OLD" value="<?= $fields_algoritm['STATE_OLD'] ?>">
        <? $tabControl->BeginNextTab(); ?> 
            <?
            include(__DIR__ . "/../util/fields/field.php");



            $disable = false;
            ?></form><?
        }
    }

    $tabControl->End();
    ?>

<script type="text/javascript" src="/bitrix/js/garpun.advertising/prism.js"></script>
<link rel="stylesheet"  type="text/css" href="/bitrix/js/garpun.advertising/prism.css"></link>

<script type="text/javascript">
    $(document).ready(function() {
        $('.garpun_advertising_geo_selector').chosen();

        if (BX('TYPE_VALUE').value == "C") {
            tabControlSource.SelectTab('source_CS');
        }

    });
</script>
<?
if ($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
} else {
    include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/garpun.advertising/util/gtm.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
}

