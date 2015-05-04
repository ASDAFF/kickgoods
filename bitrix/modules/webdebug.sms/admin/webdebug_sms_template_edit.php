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

$arEventList = CWD_SMS_Provider::GetEventsList("event_name");

function WebdebugSelectBoxFromArray($Name, $Values, $Selected="", $Default="", $AddToTag="") {
	?>
	<select name="<?=$Name?>"<?if(trim($AddToTag)!=""):?> <?=trim($AddToTag)?><?endif?>>
		<?if($Default):?><option value="none"><?=$Default?></option><?endif?>
		<?foreach($Values as $Key => $Value):?>
		<option value="<?=$Key?>"<?if($Selected!="" && $Selected!="none" && $Key==$Selected):?> selected="selected"<?endif?>><?=$Value?></option>
		<?endforeach?>
	</select>
	<?
}

global $DB;
global $APPLICATION;

$ID = IntVal($_GET["ID"]);
if ($ID>0) {
	$Mode = "edit";
	$APPLICATION->SetTitle(GetMessage("WEBDEBUG_SMS_APPLICATION_TITLE_EDIT"));
} else {
	$Mode = "add";
	$APPLICATION->SetTitle(GetMessage("WEBDEBUG_SMS_APPLICATION_TITLE_ADD"));
}

$arTabs = array(array("DIV"=>"general", "TAB"=>GetMessage("WEBDEBUG_SMS_TAB_GENERAL_NAME"), "ICON"=>"webdebug-sms-template-tabs-general", "TITLE"=>GetMessage("WEBDEBUG_SMS_TAB_GENERAL_DESC")),);
$tabControl = new CAdminTabControl("WebdebugSMStemplateTabControl", $arTabs);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!webdebug_sms_demo_expired()) {
	webdebug_sms_show_demo();
}

/////////////////////////////////////////////////////////////////////////////////////////////
// Defaults
/////////////////////////////////////////////////////////////////////////////////////////////
$arFields = array(
	"ACTIVE" => "Y",
	"NAME" => GetMessage("WEBDEBUG_SMS_FIELD_NAME_DEAFULT"),
	"SORT" => "100",
	"DESCRIPTION" => "",
	"TEMPLATE" => GetMessage("WEBDEBUG_SMS_FIELD_TEMPLATE_DEFAULT"),
	"RECEIVER" => "#DEFAULT_PHONE#",
	"EVENT" => "",
	"STOP" => "",
	"RECEIVER_FROM_EMAIL" => "",
	"EMAIL_FIELD" => "",
);

// Get site list
$arSites = array();
$Res = CSite::GetList($SiteBy="SORT",$SiteOrder="ASC");
while ($arSite = $Res->GetNext()) {
	$arSites[] = $arSite;
}

if (isset($_POST["save"]) && trim($_POST["save"])!="" || isset($_POST["apply"]) && trim($_POST["apply"])!="") {
	$arSaveFields = $_POST["fields"];
	if (!isset($arSaveFields["ACTIVE"]) || $arSaveFields["ACTIVE"]=="") $arSaveFields["ACTIVE"]="N";
	if (!isset($arSaveFields["NAME"]) || trim($arSaveFields["NAME"])=="") $arSaveFields["NAME"]=GetMessage("WEBDEBUG_SMS_FIELD_NAME_DEFAULT");
	if (!isset($arSaveFields["STOP"]) || trim($arSaveFields["STOP"])=="") $arSaveFields["STOP"]="";
	if (!isset($arSaveFields["RECEIVER_FROM_EMAIL"]) || trim($arSaveFields["RECEIVER_FROM_EMAIL"])=="") $arSaveFields["RECEIVER_FROM_EMAIL"]="";
	if (!is_numeric($arSaveFields["SORT"])) $arSaveFields["SORT"]="100";
	if (CModule::IncludeModule("webdebug.sms")) {
		$WebdebugSMSTemplate = new CWD_SMS_Template;
		foreach ($arFields as $Key => $Value) {
			if (!isset($arSaveFields[$Key])) {
				$arSaveFields[$Key] = $Value;
			}
		}
		if ($Mode=="edit") {
			$Res = $WebdebugSMSTemplate->Update($ID, $arSaveFields);
		} else {
			$Res = $WebdebugSMSTemplate->Add($arSaveFields);
			if (is_numeric($Res)) $ID = $Res;
		}
		if (is_numeric($Res)) {
			if (isset($_POST["save"]) && trim($_POST["save"])!="") {
				LocalRedirect("/bitrix/admin/webdebug_sms_templates.php?lang=".LANGUAGE_ID);
			} else {
				LocalRedirect("/bitrix/admin/webdebug_sms_template_edit.php?ID={$ID}&lang=".LANGUAGE_ID."&".$tabControl->ActiveTabParam());
			}
		}
	} else {
		$e = new CAdminException();
		$GLOBALS["APPLICATION"]->ThrowException($e);
		$message = new CAdminMessage(GetMessage("WEBDEBUG_SMS_ERROR_ITEM_NOT_FOUND"), $e);
		echo $message->Show();
	}
}
/////////////////////////////////////////////////////////////////////////////////////////////

// Copying
if (IntVal($_GET["CopyID"])>0 && empty($_POST)) {
	$Res = CWD_SMS_Template::GetByID(IntVal($_GET["CopyID"]));
	$arFields = $Res->GetNext(false,false);
}

if ($ID>0) {
	$Res = CWD_SMS_Template::GetByID($ID);
	$arFields = $Res->GetNext();
}

// Deleting Profile
if ($_GET["action"]=="delete" && IntVal($_GET["ID"])>0 && check_bitrix_sessid()) {
	$_GET["ID"] = IntVal($_GET["ID"]);
	CWD_SMS_Template::Delete($_GET["ID"]);
	LocalRedirect("webdebug_sms_templates.php?lang=".LANGUAGE_ID);
}

// MenuItem: Profiles
$aMenu[] = array(
	"TEXT"	=> GetMessage("WEBDEBUG_SMS_TOOLBAR_LIST_NAME"),
	"LINK"	=> "/bitrix/admin/webdebug_sms_templates.php?lang=".LANGUAGE_ID,
	"ICON"	=> "btn_list",
	"TITLE"	=> GetMessage("WEBDEBUG_SMS_TOOLBAR_LIST_DESC"),
);
if ($Mode == "edit") {
	// MenuItem: Add
	$aMenu[] = array(
		"TEXT"	=> GetMessage("WEBDEBUG_SMS_TOOLBAR_ADD_NAME"),
		"LINK"	=> "/bitrix/admin/webdebug_sms_template_edit.php?lang=".LANGUAGE_ID,
		"ICON"	=> "btn_new",
		"TITLE"	=> GetMessage("WEBDEBUG_SMS_TOOLBAR_ADD_DESC"),
	);
	// MenuItem: Copy
	$aMenu[] = array(
		"TEXT"	=> GetMessage("WEBDEBUG_SMS_TOOLBAR_COPY_NAME"),
		"LINK"	=> "/bitrix/admin/webdebug_sms_template_edit.php?CopyID=".$ID."&lang=".LANGUAGE_ID,
		"ICON"	=> "btn_copy",
		"TITLE"	=> GetMessage("WEBDEBUG_SMS_TOOLBAR_COPY_DESC"),
	);
	// MenuItem: Delete
	$aMenu[] = array(
		"TEXT"	=> GetMessage("WEBDEBUG_SMS_TOOLBAR_DELETE_NAME"),
		"LINK"	=> "javascript:if (confirm('".GetMessage("WEBDEBUG_SMS_TOOLBAR_DELETE_NAME_CONFIRM")."')) window.location='/bitrix/admin/webdebug_sms_template_edit.php?action=delete&ID=".$ID."&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."';",
		"ICON"	=> "btn_delete",
		"TITLE"	=> GetMessage("WEBDEBUG_SMS_TOOLBAR_DELETE_DESC"),
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?if($WebdebugSMSTemplate->LAST_ERROR) ShowError($WebdebugSMSTemplate->LAST_ERROR);?>
<?#$APPLICATION->AddHeadString('<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>');?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

<?if($Mode=="edit" && !empty($arFields) || $Mode=="add"):?>
	<form method="post" action="<?=$_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data" name="post_form" id="webdebug-sms-template-parameters">
		<?$tabControl->Begin();?>
		<?$tabControl->BeginNextTab();?>
		<tr id="tr_active">
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_ACTIVE")?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[ACTIVE]" value="Y"<?if($arFields["ACTIVE"]=="Y"):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_site_id">
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_SITE_ID")?>:</td>
			<td class="field-data">
				<?$arTemplateSites = CWD_SMS_Template::GetSitesForTemplate($ID);?>
				<?foreach($arSites as $arSite):?>
					<label for="webdebug-sms-site-id-<?=$arSite["ID"]?>">
						<input type="checkbox" name="fields[SITE_ID][<?=$arSite["ID"]?>]" value="Y" id="webdebug-sms-site-id-<?=$arSite["ID"]?>"<?if(!$ID || in_array($arSite["ID"], $arTemplateSites)):?> checked="checked"<?endif?> />
						<?=$arSite["NAME"]?>
					</label><br/>
				<?endforeach?>
			</td>
		</tr>
		<tr id="tr_name">
			<td class="field-name" width="40%"><span class="required">*</span><?=GetMessage("WEBDEBUG_SMS_FIELD_NAME")?>:</td>
			<td class="field-data">
				<input type="text" name="fields[NAME]" value="<?=$arFields["NAME"]?>" size="60" maxlength="255" />
			</td>
		</tr>
		<tr id="tr_sort">
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_SORT")?>:</td>
			<td class="field-data">
				<input type="text" name="fields[SORT]" value="<?=$arFields["SORT"]?>" size="10" maxlength="255" />
			</td>
		</tr>
		<tr id="tr_receiver">
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_RECEIVER")?>:</td>
			<td class="field-data">
				<input type="text" class="field_target" name="fields[RECEIVER]" value="<?=$arFields["RECEIVER"]?>" size="60" maxlength="255" />
			</td>
		</tr>
		<tr id="tr_phone_from_email">
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_PHONE_FROM_EMAIL")?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[RECEIVER_FROM_EMAIL]" value="Y"<?if($arFields["RECEIVER_FROM_EMAIL"]=="Y"):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_email_field"<?if($arFields["RECEIVER_FROM_EMAIL"]!="Y"):?> style="display:none"<?endif?>>
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_EMAIL_FIELD")?>:</td>
			<td class="field-data">
				<select name="fields[EMAIL_FIELD]" id="email_field_switcher">
					<option data-empty="Y"><?=GetMessage("WEBDEBUG_SMS_FIELD_EMAIL_FIELD_EMPTY");?></option>
					<?if(!empty($arFields['EMAIL_FIELD'])):?>
						<option value="<?=$arFields['EMAIL_FIELD'];?>" selected="selected"><?=$arFields['EMAIL_FIELD']?></option>
					<?endif?>
				</select>
			</td>
		</tr>
		<?$arCurrentEvent = array();?>
		<tr id="tr_event">
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_EVENT")?>:</td>
			<td class="field-data">
				<select name="fields[EVENT]" id="event_switcher">
					<?if(is_array($arEventList) && !empty($arEventList)):?>
						<option value=""><?=GetMessage("WEBDEBUG_SMS_FIELD_EVENT_SELECT");?></option>
						<?$arCurrentEvent=$arEventList[0]?>
						<?foreach ($arEventList as $arEvent):?>
							<option value="<?=$arEvent["EVENT_NAME"]?>"<?if($arEvent["EVENT_NAME"]==$arFields["EVENT"]):?> selected="selected"<?$arCurrentEvent=$arEvent?><?endif?>>
								[<?=$arEvent["EVENT_NAME"]?>] [<?=$arEvent["NAME"]?>]
							</option>
						<?endforeach?>
					<?else:?>
						<option><?=GetMessage("WEBDEBUG_SMS_FIELD_EVENT_EMPTY");?></option>
					<?endif?>
				</select>
			</td>
		</tr>
		<tr id="tr_stop">
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_STOP")?>:</td>
			<td class="field-data">
				<input type="checkbox" name="fields[STOP]" value="Y"<?if($arFields["STOP"]=="Y"):?> checked="checked"<?endif?> />
			</td>
		</tr>
		<tr id="tr_description">
			<td class="field-name" width="40%"><?=GetMessage("WEBDEBUG_SMS_FIELD_DESCRIPTION")?>:</td>
			<td class="field-data">
				<textarea name="fields[DESCRIPTION]" cols="47" rows="3" style="resize:vertical; width:100%;"><?=$arFields["DESCRIPTION"]?></textarea>
			</td>
		</tr>
		<tr class="heading"><td colspan="2"><?=GetMessage("WEBDEBUG_SMS_FIELD_TEMPLATE")?></td></tr>
		<tr id="tr_template">
			<td class="field-data" colspan="2">
				<textarea class="field_target field_current_target" id="template_textarea" name="fields[TEMPLATE]" cols="60" rows="8" style="resize:vertical; width:100%;"><?=$arFields["TEMPLATE"]?></textarea>
			</td>
		</tr>
		<tr id="tr_avilable_fields"><td colspan="2"><b><?=GetMessage("WEBDEBUG_SMS_EVENT_FIELDS")?></b></td></tr>
		<tr id="tr_template_fields">
			<td class="field-data" colspan="2">
				<script type="text/javascript">jQuery.fn.extend({insertAtCaret:function(a){return this.each(function(b){if(document.selection)this.focus(),sel=document.selection.createRange(),sel.text=a,this.focus();else if(this.selectionStart||"0"==this.selectionStart){b=this.selectionStart;var c=this.selectionEnd,d=this.scrollTop;this.value=this.value.substring(0,b)+a+this.value.substring(c,this.value.length);this.focus();this.selectionStart=b+a.length;this.selectionEnd=b+a.length;this.scrollTop=d}else this.value+=a,this.focus()})}});</script>
				<div id="tr_template_fields_wrapper"></div>
				<script type="text/javascript">
				// reload event type
				$("#event_switcher").change(function(){
					var EventSelected = $(this).val();
					$.ajax({
						url: "/bitrix/admin/webdebug_sms_event_reload.php",
						type: "GET",
						data: "event="+EventSelected,
						success: function(res) {
							$("#tr_template_fields_wrapper").html(res);
							if (EventSelected.substr(0,5).toLowerCase()=='sale_' && res.toLowerCase().indexOf('#order_id#')!=-1) {
								$('#wd_sms_sale_block').show();
							} else {
								$('#wd_sms_sale_block').hide();
							}
						}
					});
				}).change();
				$('#tr_phone_from_email input[type=checkbox]').change(function(){
					if ($(this).is(':checked')) {
						$('#tr_email_field').show();
					} else {
						$('#tr_email_field').hide();
					}
				});
				</script>
			</td>
		</tr>
		<?$tabControl->Buttons(array("disabled"=>false,"back_url"=>"webdebug_sms_templates.php?lang=".LANG));?>
		<?$tabControl->End();?>
	</form>
<?elseif($Mode=="edit" && empty($arFields)):?>
	<?ShowError(GetMessage("WEBDEBUG_SMS_ERROR_TEMPLATE_NOT_FOUND"))?>
<?endif?>

<?
/////////////////////////////////////////////////////////////////////////////////////////////
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>