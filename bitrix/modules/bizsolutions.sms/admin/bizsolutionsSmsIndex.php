<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/include.php");

IncludeModuleLangFile(__FILE__);

$module_id = "bizsolutions.sms";

$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);

if($SMS_RIGHT < "R") 
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

global $SMSBiz;

$APPLICATION->SetTitle(GetMessage("index_title"));

if($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
}
$adminPage->ShowSectionIndex("menu_smsbiz", "bizsolutions.sms");
if($_REQUEST["mode"] == "list") {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}

?>
