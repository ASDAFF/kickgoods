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

$sTableID = "CWD_SMS_History_List";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

// Get items list
$rsData = CWD_SMS_History::GetList(array($by=>$order));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("rub_nav")));

// Add headers
$lAdmin->AddHeaders(array(
  array(
	  "id" => "ID",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_ID"),
    "sort" => "id",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"RECEIVER",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_RECEIVER"),
    "sort" => "receiver",
		"align" => "right",
    "default" => true,
  ),
  array(
	  "id" =>"SENDER",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_SENDER"),
    "sort" => "sender",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"MESSAGE",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_MESSAGE"),
    "sort" => "message",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"EVENT",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_EVENT"),
    "sort" => "event",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"PROVIDER",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_PROVIDER"),
    "sort" => "provider",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"DATETIME",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_DATETIME"),
    "sort" => "datetime",
		"align" => "left",
    "default" => true,
  ),
));

$arSmsProviders = CWD_SMS_Provider::GetList();
// Build items list
while ($arRes = $rsData->NavNext(true, "f_")) {
  $row = &$lAdmin->AddRow($f_ID, $arRes); 
	// ID
	$row->AddViewField("ID", $f_ID);
  // RECEIVER
  $row->AddViewField("RECEIVER", $f_RECEIVER);
  // SENDER
  $row->AddViewField("SENDER", $f_SENDER);
  // MESSAGE
  $row->AddViewField("MESSAGE", $f_MESSAGE);
	// EVENT
	$row->AddViewField("EVENT", $f_EVENT);
	// PROVIDER
	$strProvider = $f_PROVIDER;
	foreach($arSmsProviders as $arProvider) {
		if ($f_PROVIDER=='log') {
			$strProvider = '[ Log ]';
		} elseif ($f_PROVIDER==$arProvider['CODE']) {
			$strProvider = $arProvider['NAME'];
		}
	}
	$row->AddViewField("PROVIDER", $strProvider);
	// DATETIME
	$row->AddViewField("DATETIME", $f_DATETIME);
}

// List Footer
$lAdmin->AddFooter(
  array(
    array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
    array("counter"=>true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
  )
);

$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);

// Start output
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("WEBDEBUG_SMS_PAGE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!webdebug_sms_demo_expired()) {
	webdebug_sms_show_demo();
}

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>