<?
$ModuleID = 'webdebug.sms';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
CModule::IncludeModule($ModuleID);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
IncludeModuleLangFile(__FILE__);

if (webdebug_sms_demo_expired()) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	webdebug_sms_show_demo();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$APPLICATION->SetTitle(GetMessage("WEBDEBUG_SMS_INDEX_TITLE"));

if($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!webdebug_sms_demo_expired()) {
	webdebug_sms_show_demo();
}

$adminPage->ShowSectionIndex("webdebug_sms_submenu", $ModuleID);

if($_REQUEST["mode"] == "list")
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>