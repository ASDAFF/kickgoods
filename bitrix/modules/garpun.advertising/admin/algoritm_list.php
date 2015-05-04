<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
CJSCore::Init(array("jquery"));
?><script type="text/javascript" src="/bitrix/js/garpun.advertising/admin.js"></script>
<script type ="text/css" src="/bitrix/js/garpun.advertising/admin.css" ></script>
<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . "/menu.php");
Loc::loadMessages(__DIR__ . "/ex_system.php");
$APPLICATION->SetTitle(Loc::GetMessage("GARPUN_ADVERTISING_ALGORITM_LIST"));
\Bitrix\Main\Loader::IncludeModule("garpun.advertising");



//external-info
$projects = \garpun_advertising\Save::getUsersProjectList();
$projectsIds = array_keys($projects);
$projectsFirst = current($projects);
$algoritmsExternal = \garpun_advertising\External\ExternalApi::getReference(Array("projects", $projectsFirst["EXTERNAL_ID"], "algorithms"));
if (!is_array($algoritmsExternal)) {
    $errors[] = $algoritmsExternal;
}
$algoritmsExternal = \garpun_advertising\keyArrayCreator($algoritmsExternal, 'id');
$referencesList = Array(
    "ALGORITM_TYPE" => Array(Array("references", "algorithm-types"), "code", false),
    "ENGINE_SETTING" => Array(Array("external-accounts"), "id", false),
    "UPDATE_TIME" => Array(Array("references", "update-schedules"), "code", false),
    "GEO" => Array(Array("references", "geo"), "code", false),
);

$references = \garpun_advertising\External\ExternalApi::getReferencesArray($referencesList);
if (!is_array($references)) {
    $errors[] = $references;
}
$referencesListKeys = array_keys($referencesList);








$sTableID = 'tbl_file';
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$map = \garpun_advertising\AlgoritmTable::getMap();
$filter = Array();
$i = 0;
foreach ($map as $col => $v) {
    if (in_array($v["data_type"], Array("integer", "string", "datetime"))) {
        $filter[] = $col;

        $title = GetMessage("GARPUN_ADVERTISING_ALGORITM_LST_HEADER_" . $col);
        $arHeaders[] = array(
            'id' => $col,
            'content' =>
            (!empty($title)) ?
                    GetMessage("GARPUN_ADVERTISING_ALGORITM_LST_HEADER_" . $col) :
                    $v["title"],
            'sort' => $i++,
            'default' => in_array($col, Array("EXTERNAL_ID", "NAME", "PATH")),
        );
    }
}

foreach ($referencesListKeys as $i => $key) {
    $arHeaders[] = array(
        'id' => $key,
        'content' => Loc::GetMessage("GARPUN_ADVERTISING_ALGORITM_LST_HEADER_" . $key),
        'sort' => 100 + $i,
        'default' => true,
    );
}


$lAdmin->AddHeaders($arHeaders);

if (!in_array($by, $lAdmin->GetVisibleHeaderColumns(), true)) {
    $by = 'ID';
}

// select data



$rsData = \garpun_advertising\AlgoritmTable::getList(array(
            "filter" => Array("PROJECT_ID" => $projectsIds),
            "select" => $filter,
            "order" => array($by => strtoupper($order))
        ));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();



// build list
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));
if (\garpun_advertising\External\ExternalApi::notNeedPay()) {
    if (!\garpun_advertising\External\ExternalRequestResult::$globalError)
        while ($arRes = $rsData->NavNext(true, "f_")) {



            $can_edit = true;

            $arActions = Array();

            $arActions[] = array(
                "ICON" => "edit",
                "TEXT" => GetMessage($can_edit ? "MAIN_ADMIN_MENU_EDIT" : "MAIN_ADMIN_MENU_VIEW"),
                "ACTION" => $lAdmin->ActionRedirect("garpun.advertising_algoritm_edit.php?ID=$f_ID&action=edit&PROJECT_ID=$f_PROJECT_ID"),
                "DEFAULT" => true
            );

            $arRes["ALGORITM_TYPE"] = $references["ALGORITM_TYPE"][$algoritmsExternal[$f_EXTERNAL_ID]["type"]]["title"];
            $arRes["UPDATE_TIME"] = $references["UPDATE_TIME"][$algoritmsExternal[$f_EXTERNAL_ID]["updateSchedule"]]["title"];

            $arRes["GEO"] = Array();
            if (is_array($algoritmsExternal[$f_EXTERNAL_ID]["geo"])) {
                foreach ($algoritmsExternal[$f_EXTERNAL_ID]["geo"] as $code) {
                    $arRes["GEO"][] = $references["GEO"][$code]["title"];
                }
            }
            $arRes["GEO"] = join($arRes["GEO"], "/");

            $arRes["ENGINE_SETTING"] = Array();
            if (is_array($algoritmsExternal[$f_EXTERNAL_ID]["engineSettings"])) {
                foreach ($algoritmsExternal[$f_EXTERNAL_ID]["engineSettings"] as $id) {
                    $arRes["ENGINE_SETTING"][] = Loc::getMessage("GARPUN_ADVERTISING_EXT_LIST_" . $id["engine"]);
                }
            }
            $arRes["ENGINE_SETTING"] = join($arRes["ENGINE_SETTING"], "/");

            $row = $lAdmin->AddRow($f_ID, $arRes);
            if ($f_STATE == "S" && $algoritmsExternal[$f_EXTERNAL_ID]["updateSchedule"] == "never") {
                $state_alg = Loc::getMessage("GARPUN_ADVERTISING_ALGORITM_STATE_S_NEVER");
            } else {
                $state_alg = Loc::getMessage("GARPUN_ADVERTISING_ALGORITM_STATE_" . $f_STATE);
            }


            $row->AddField("STATE", $state_alg);
            Loc::getMessage("GARPUN_ADVERTISING_ALGORITM_TYPE_ALGORITM_" . $f_TYPE);








            $row->AddActions($arActions);

            // deny group operations (hide checkboxes)
            $row->pList->bCanBeEdited = false;
        }
}


// view
if ($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
    echo Loc::getMessage("GARPUN_ADVERTISING_LOGO_FILE", \garpun_advertising\External\ExternalApi::prepareHeaderUrl(\garpun_advertising\Save::getUser("EXTERNAL_ADHANDS_ID")));
}

if (\garpun_advertising\External\ExternalApi::notNeedPay(true)) {
// menu
    $projects = \garpun_advertising\Save::getUsersProjectList();
    $project = current($projects);
    $aMenu = array(
        array(
            "TEXT" => GetMessage('GARPUN_ALGORITM_ROWS_ADD_NEW_BUTTON'),
            "TITLE" => GetMessage('GARPUN_ALGORITM_ROWS_ADD_NEW_BUTTON'),
            "LINK" => "garpun.advertising_algoritm_edit.php?action=new&PROJECT_ID={$project["ID"]}&lang=" . LANGUAGE_ID,
            "ICON" => "btn_new",
            "DISABLED" => "Y",
        ),
    );

    $lAdmin->AddAdminContextMenu($aMenu, false, true);
    $lAdmin->CheckListMode();
    if (!empty($errors)) {
        if (!is_array($errors)) {
            $errors = Array($errors);
        }
        CAdminMessage::ShowMessage(join("\n", $errors));
    }


    $lAdmin->DisplayList();
}

if ($_REQUEST["mode"] == "list") {

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
} else {
    include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/garpun.advertising/util/gtm.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
}



