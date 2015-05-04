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

global $DB;
global $APPLICATION;

$APPLICATION->SetTitle(GetMessage("WEBDEBUG_SMS_SEND_PAGE_TITLE"));

$aTabs = array(
	array("DIV" => "tab_balance", "TAB" => GetMessage("WEBDEBUG_SMS_TAB_SEND_NAME"), "ICON" => "webdebug_sms_balance", "TITLE" => GetMessage("WEBDEBUG_SMS_TAB_SEND_DESC")),
);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!webdebug_sms_demo_expired()) {
	webdebug_sms_show_demo();
}

// Top context menu
$arContextMenu[] = array(
	"TEXT"	=> GetMessage("WEBDEBUG_SMS_CONTEXT_BALANCE_NAME"),
	"LINK"	=> "webdebug_sms_balance.php?lang=".LANGUAGE_ID,
	"ICON"	=> "btn_refresh",
	"TITLE"	=> GetMessage("WEBDEBUG_SMS_CONTEXT_BALANCE_DESC"),
);
$ContextMenu = new CAdminContextMenu($arContextMenu);
$ContextMenu->Show();

$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>" id="webdebug-sms-table">
	<?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	?>
	<tr>
		<td>
			<?include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webdebug.sms/include/sms_send.php");?>
		</td>
	</tr>
	<?
	$tabControl->End();
	?>
</form>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>