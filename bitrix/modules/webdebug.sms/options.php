<?
$ModuleID = 'webdebug.sms';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');

if(!$USER->IsAdmin()) return;
CModule::IncludeModule($ModuleID);
IncludeModuleLangFile(__FILE__);

if (webdebug_sms_demo_expired()) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	webdebug_sms_show_demo();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
} else {
	webdebug_sms_show_demo();
}

// Get SMS providers
$arProvidersList = CWD_SMS_Provider::GetList();
$arProviderOptions = array();
foreach ($arProvidersList as $arProvider) {
	$arProviderOptions[$arProvider['CLASS']] = $arProvider['NAME'];
}

// Get current provider
$strProvider = CWD_SMS_Provider::GetCurrentProvider();
if ($strProvider!==false) {
	$WD_SMS = new $strProvider();
}

$arAllOptions = Array(
	array("provider", GetMessage("WEBDEBUG_SMS_PROVIDER"), array("TYPE"=>"SELECT"), $arProviderOptions),
	array("use_translit", GetMessage("WEBDEBUG_SMS_USE_TRANSLIT"), array("TYPE"=>"CHECKBOX")),
	array("default_phone", GetMessage("WEBDEBUG_SMS_DEFAULT_PHONE"), array("TYPE"=>"TEXT")),
	array("log_errors", GetMessage("WEBDEBUG_SMS_LOG_ERRORS"), array("TYPE"=>"CHECKBOX")),
);
$aTabs = array(
	array("DIV" => "tab_general", "TAB" => GetMessage("WEBDEBUG_SMS_OPTIONS_PAGE_GENERAL_NAME"), "ICON" => "webdebug_sms_general", "TITLE" => GetMessage("WEBDEBUG_SMS_OPTIONS_PAGE_GENERAL_DESC")),
	array("DIV" => "tab_custom", "TAB" => GetMessage("WEBDEBUG_SMS_OPTIONS_PAGE_CUSTOM_NAME"), "ICON" => "webdebug_sms_custom", "TITLE" => GetMessage("WEBDEBUG_SMS_OPTIONS_PAGE_CUSTOM_DESC")),
	array("DIV" => "tab_send_sms", "TAB" => GetMessage("WEBDEBUG_SMS_OPTIONS_PAGE_SEND_NAME"), "ICON" => "webdebug_sms_send", "TITLE" => GetMessage("WEBDEBUG_SMS_OPTIONS_PAGE_SEND_DESC")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$GLOBALS['WD_SMS_TABCONTROL'] = $tabControl;

// Save
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) {
	define('WEBDEBUG_SMS_SAVING_OPTIONS',true);
	foreach($arAllOptions as $arOption) {
		$OptionName = $arOption[0];
		$OptionDescr = $arOption[1];
		$arOptionType = $arOption[2];
		$OptionValue = $_REQUEST[$OptionName];
		if($arOptionType[0]=="CHECKBOX" && $OptionValue!="Y") {
			$OptionValue="N";
		}
		COption::SetOptionString($ModuleID, $OptionName, $OptionValue, $arOption[1]);
	}
	$WD_SMS->GetTabContent();
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0) {
		LocalRedirect($_REQUEST["back_url_settings"]);
	} else {
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
	}
}

?>

<?if(CModule::IncludeModule($ModuleID)):?>
	<?
	$CurrentSmsClass = false;
	?>
	<?$tabControl->Begin();?>
	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>" id="webdebug-sms-table">
		<?$tabControl->BeginNextTab();?>
			<?foreach($arAllOptions as $arOption):?>
				<?
					$OptionID = $arOption[0];
					$OptionName = $arOption[1];
					$OptionType = $arOption[2];
					$OptionValues = $arOption[3];
					$OptionValue = COption::GetOptionString($ModuleID, $OptionID);
				?>
				<tr>
					<td class="field-name" style="width:40%">
						<?if($OptionType["TYPE"]=="CHECKBOX"):?><label for="<?=htmlspecialchars($OptionID)?>"><?=$OptionName?></label><?else:?><?=$OptionName?><?endif?>:
					</td>
					<td class="field-value">
						<?if($OptionType["TYPE"]=="CHECKBOX"):?>
							<input type="checkbox" id="<?echo htmlspecialchars($OptionID)?>" name="<?echo htmlspecialchars($OptionID)?>" value="Y"<?if($OptionValue=="Y")echo" checked='checked'";?> />
						<?elseif($OptionType["TYPE"]=="TEXT"):?>
							<input type="text" size="<?echo $OptionType["SIZE"]?>" maxlength="255" value="<?echo htmlspecialchars($OptionValue)?>" name="<?echo htmlspecialchars($OptionID)?>" />
						<?elseif($OptionType["TYPE"]=="TEXTAREA"):?>
							<textarea cols="<?echo $OptionType["COLS"]?>" rows="<?echo $OptionType["ROWS"]?>" name="<?echo htmlspecialchars($OptionID)?>"><?echo htmlspecialchars($OptionValue)?></textarea>
						<?elseif($OptionType["TYPE"]=="SELECT"):?>
							<select name="<?=htmlspecialchars($OptionID)?>">
								<?foreach ($OptionValues as $ValueID => $ValueName):?>
									<option value="<?=$ValueID;?>"<?if($OptionValue==$ValueID):?> selected="selected"<?endif?>><?=$ValueName?></option>
								<?endforeach?>
							</select>
						<?endif?>
					</td>
				</tr>
			<?endforeach?>
		<?$tabControl->BeginNextTab();?>
			<tr>
				<td>
					<?
					if (is_object($WD_SMS)) {
						print $WD_SMS->GetTabContent();
					}
					?>
				</td>
			</tr>
		<?$tabControl->BeginNextTab();?>
			<tr>
				<td>
					<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$ModuleID}/include/sms_send.php");?>
				</td>
			</tr>
		<?$tabControl->Buttons();?>
			<input type="submit" name="Update" value="<?=GetMessage("WEBDEBUG_SMS_BUTTON_UPDATE_VALUE")?>" title="<?=GetMessage("WEBDEBUG_SMS_BUTTON_UPDATE_TITLE")?>" class="adm-btn-save" />
			<?if(strlen($_REQUEST["back_url_settings"])>0):?>
				<input type="submit" name="Apply" value="<?=GetMessage("WEBDEBUG_SMS_BUTTON_APPLY_VALUE")?>" title="<?=GetMessage("WEBDEBUG_SMS_BUTTON_APPLY_TITLE")?>" />
			<?endif?>
			<?if(strlen($_REQUEST["back_url_settings"])>0):?>
				<input type="button" name="Cancel" value="<?=GetMessage("WEBDEBUG_SMS_BUTTON_CANCEL_VALUE")?>" title="<?=GetMessage("WEBDEBUG_SMS_BUTTON_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'" />
				<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>" />
			<?endif?>
			<?=bitrix_sessid_post();?>
		<?$tabControl->End();?>
		<img src="http://www.webdebug.ru/_res/<?=$ModuleID;?>/<?=$ModuleID;?>.img" alt="" width="0" height="0" style="visibility:hidden"/>
	</form>
<?else:?>
	<p><?=GetMessage("WEBDEBUG_SMS_ERROR_MODULE_NOT_INCLUDED")?></p>
<?endif?>
