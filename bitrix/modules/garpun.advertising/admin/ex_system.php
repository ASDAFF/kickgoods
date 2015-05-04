<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
CJSCore::Init(array("jquery"));
?><script type="text/javascript" src="/bitrix/js/garpun.advertising/admin.js"></script>
<script type ="text/css" src="/bitrix/js/garpun.advertising/admin.css" ></script>
<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . "/menu.php");

$APPLICATION->SetTitle(GetMessage("GARPUN_ADVERTISING_EXT_LIST"));

\Bitrix\Main\Loader::IncludeModule("garpun.advertising");
if (check_bitrix_sessid() && isset($_REQUEST["action"], $_REQUEST["engine"])) {
    if ($_REQUEST["action"] == "DELETE") {
        //var_dump("DELETE");
        $credentialActivity = garpun_advertising\External\Messenger::exec(Array("credentials", $_REQUEST["engine"]), Array(), "DELETE");
        LocalRedirect($APPLICATION->GetCurPageParam("", Array("action", "engine", "sessid")));
    } elseif ($_REQUEST["action"] == "ADD") {
        $url = garpun_advertising\External\Messenger::prepareUrl("credentials/gate", Array("engine" => $_REQUEST["engine"], "callback" => ((strlen($_SERVER["HTTPS"]) > 0) ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . $APPLICATION->GetCurPageParam("", Array("action", "engine", "sessid"))), true);

        LocalRedirect($url);
    }
}


$sTableID = 'tbl_fi4le';
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, false);
if (\garpun_advertising\External\ExternalApi::notNeedPay()) {
    $arHeaders = array(
        array(
            'id' => 'ICON',
            'content' => Loc::getMessage("GARPUN_ADVERTISING_EXT_LIST_COLUMN_STATE"),
            'sort' => 'ICON',
            'default' => true
        ), array(
            'id' => 'NAME',
            'content' => Loc::getMessage("GARPUN_ADVERTISING_EXT_LIST_COLUMN_NAME"),
            'sort' => 'NAME',
            'default' => true
        ),
        array(
            'id' => 'ACTION',
            'content' => Loc::getMessage("GARPUN_ADVERTISING_EXT_LIST_COLUMN_ACTION"),
            'sort' => 'ACTION',
            'default' => true
        ),
    );




    $lAdmin->AddHeaders($arHeaders);



    if (!in_array($by, $lAdmin->GetVisibleHeaderColumns(), true)) {
        $by = 'ICON';
    }

    $rsData = Array();
    $externalCreditnalsList = \garpun_advertising\External\ExternalApi::getReference("credentials");
    if (!is_array($externalCreditnalsList)) {
        $errors[] = $externalCreditnalsList;
    } else {
        $externalCreditnalsList = \garpun_advertising\keyArrayCreator($externalCreditnalsList, "engine");
        foreach (Array("yandex", "google") as $engine) {

            $list = $externalCreditnalsList[$engine];
            $rsData[$list["engine"]] = Array(
                "NAME" => Loc::getMessage("GARPUN_ADVERTISING_EXT_LIST_" . $list["engine"]),
                "ENGINE" => $list["engine"],
                "ACTION" => $list["status"] ? 1 : 2,
            );
        }

        $creditnalsActive = false;
        foreach ($rsData as $k => $arRes) {

            $row = $lAdmin->AddRow($k, $arRes);
            if ($arRes["ACTION"] == "1") {
                $row->AddField("ICON", "<div style='background:#00FF00;height:10px;width:10px;' ></div>");
                $url = $APPLICATION->GetCurPageParam("engine={$arRes["ENGINE"]}&action=DELETE&" . bitrix_sessid_get());
                $creditnalsActive = true;
                $row->AddField("ACTION", "<a href=\"$url\">" . Loc::getMessage("GARPUN_ADVERTISING_EXT_LIST_COLUMN_OFF") . "</a>");
            } else {
                $url = $APPLICATION->GetCurPageParam("engine={$arRes["ENGINE"]}&action=ADD&" . bitrix_sessid_get());
                $row->AddField("ICON", "<div style='background:#FF0000;height:10px;width:10px;' ></div>");
                $row->AddField("ACTION", "<a href=\"$url\">" . Loc::getMessage("GARPUN_ADVERTISING_EXT_LIST_COLUMN_ON") . "</a>");
            }
            // deny group operations (hide checkboxes)
            $row->pList->bCanBeEdited = false;
        }
    }
}
// view
if ($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
    echo BeginNote();
    echo GetMessage("GARPUN_ADVERTISING_EX_SISTEM_NOTE");
    echo EndNote();
    echo Loc::getMessage("GARPUN_ADVERTISING_LOGO_FILE", \garpun_advertising\External\ExternalApi::prepareHeaderUrl(\garpun_advertising\Save::getUser("EXTERNAL_ADHANDS_ID")));
}


// menu
if ($creditnalsActive) {
    $projects = \garpun_advertising\Save::getUsersProjectList();
    $project = current($projects);
    $aMenu = array(
        array(
            "TEXT" => GetMessage('GARPUN_ADVERTISING_ROWS_ADD_NEW_BUTTON'),
            "TITLE" => GetMessage('GARPUN_ADVERTISING_ROWS_ADD_NEW_BUTTON'),
            "LINK" => "garpun.advertising_algoritm_edit.php?action=new&PROJECT_ID={$project["ID"]}&lang=" . LANGUAGE_ID,
            "ICON" => "btn_new",
        ),
    );
}


$lAdmin->AddAdminContextMenu($aMenu, false, false);
$lAdmin->CheckListMode();
if (!empty($errors)) {
    if (!is_array($errors)) {
        $errors = Array($errors);
    }
    CAdminMessage::ShowMessage(join("\n", $errors));
}

if (\garpun_advertising\External\ExternalApi::notNeedPay(true)) {

    $lAdmin->Display();
}

if ($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
} else {
    include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/garpun.advertising/util/gtm.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
}

