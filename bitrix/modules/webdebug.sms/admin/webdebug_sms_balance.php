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

$APPLICATION->SetTitle(GetMessage("WEBDEBUG_SMS_BALANCE_PAGE_TITLE"));

$aTabs = array(
	array("DIV" => "tab_balance", "TAB" => GetMessage("WEBDEBUG_SMS_TAB_BALANCE_NAME"), "ICON" => "webdebug_sms_balance", "TITLE" => GetMessage("WEBDEBUG_SMS_TAB_BALANCE_DESC")),
);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!webdebug_sms_demo_expired()) {
	webdebug_sms_show_demo();
}

// Top context menu
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<tr>
	<td>
		<p class="standart-text">
			<?
				$Balance = CWD_SMS_Provider::GetBalance();
				if ($Balance) {
					print GetMessage("WEBDEBUG_SMS_BALANCE_TITLE")." <strong>".$Balance."</strong>.";
				} else {
					printf (GetMessage("WEBDEBUG_SMS_BALANCE_EMPTY"), "lang=".LANGUAGE_ID."&mid=webdebug.sms&back_url_settings=".urlencode($_SERVER["REQUEST_URI"]));
				}
				$URL = CWD_SMS_Provider::GetPayURL();
				if ($URL!==false) {
					printf (GetMessage("WEBDEBUG_SMS_BALANCE_PAY"), $URL);
				}
			?>
		</p>
	</td>
</tr>
<?
$tabControl->End();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>