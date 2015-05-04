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


if(isset($_REQUEST["AMOUNT"],$_REQUEST["TARIFF_ID"])){
    $user= \garpun_advertising\Save::getUser();
    if($user!==false){
    $url= \garpun_advertising\External\ExternalApi::getPaymentLink($user["EXTERNAL_ADHANDS_ID"], $_REQUEST["TARIFF_ID"], $_REQUEST["AMOUNT"]);
if($url!==false){
    LocalRedirect($url);
}else{
    $errors[]=Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_ERROR_REDIRECT");
}
    
    
    }
}


$sTableID = 'tbl_fi4le';
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, false);

$arHeaders = array(
    array(
        'id' => 'DURATION',
        'content' => Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_DURATION"),
        'sort' => 'DURATION',
        'default' => true
    ), array(
        'id' => 'PRICE',
        'content' => Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_PRICE"),
        'sort' => 'PRICE',
        'default' => true
    ),
    array(
        'id' => 'DISCOUNT',
        'content' => Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_DISCOUNT"),
        'sort' => 'DISCOUNT',
        'default' => true
    ),
    array(
        'id' => 'ACTION',
        'content' => Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_ACTION"),
        'sort' => 'ACTION',
        'default' => true
    ),
);




$lAdmin->AddHeaders($arHeaders);



$rsData = Array();

$rsData[] = Array(
    "ID" => 1,
    "DURATION" => Loc::getMessage("GARPUN_ADVERTISING_PAY_1_MONTH")
    , "PRICE" => "1 000 ".Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_RUB")
    , "DISCOUNT" => ""    
);


$rsData[] = Array(
    "ID" => 2,
    "DURATION" => Loc::getMessage("GARPUN_ADVERTISING_PAY_3_MONTH")
    , "PRICE" => "3 000 ".Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_RUB")
    , "DISCOUNT" => ""
);

$rsData[] = Array(
    "ID" => 3,
    "DURATION" => Loc::getMessage("GARPUN_ADVERTISING_PAY_6_MONTH")
    , "PRICE" => "5 700 ".Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_RUB")
    , "DISCOUNT" => "5% (300 ".Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_RUB")." )"
);
$rsData[] = Array(
    "ID" => 4,
    "DURATION" => Loc::getMessage("GARPUN_ADVERTISING_PAY_12_MONTH")
    , "PRICE" => "10 800 ".Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_RUB")
    , "DISCOUNT" => "10% (1 200 ".Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_RUB").")"
);

$creditnalsActive = false;

$userExternalId = "M";
if ($user = \garpun_advertising\Save::getUser()) {
    if ($user["EXTERNAL_ID"]) {
        $userExternalId = $user["EXTERNAL_ID"];
    }
}
foreach ($rsData as $k => $arRes) {

    $row = $lAdmin->AddRow($k, $arRes);
$price=  preg_replace("/[\D]+/", "", $arRes["PRICE"]);
    $row->AddField("ACTION", "<a href=\"?AMOUNT=$price&TARIFF_ID={$arRes["ID"]}&\" target=\"_blank\">" . 
            Loc::getMessage("GARPUN_ADVERTISING_PAY_LIST_BUY") . 
            "</a>");


    $row->pList->bCanBeEdited = false;
}

// view
if ($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

    echo Loc::getMessage("GARPUN_ADVERTISING_LOGO_FILE", \garpun_advertising\External\ExternalApi::prepareHeaderUrl(\garpun_advertising\Save::getUser("EXTERNAL_ADHANDS_ID")));
}



$lAdmin->AddAdminContextMenu($aMenu, false, false);
$lAdmin->CheckListMode();
if (!empty($errors)) {
    if (!is_array($errors)) {
        $errors = Array($errors);
    }
    CAdminMessage::ShowMessage(join("\n", $errors));
}
$lAdmin->Display();



if ($_REQUEST["mode"] == "list") {
    
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php");
} else {
    include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/garpun.advertising/util/gtm.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
    
}

