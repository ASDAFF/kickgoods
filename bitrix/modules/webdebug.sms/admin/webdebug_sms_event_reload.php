<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?if(!isset($_GET["event"]) || trim($_GET["event"])==''):?>
<script>
	$('#email_field_switcher option').each(function(){
		if ($(this).attr('data-empty')!='Y') {
			$(this).remove();
		}
	});
	$('#tr_avilable_fields').hide();
</script>
<?die();?>
<?endif?>

<?$resCurrentEvent = CEventType::GetList(array("LID"=>LANGUAGE_ID,"TYPE_ID"=>htmlspecialchars(trim($_GET["event"]))));?>
<?include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webdebug.sms/lang/".LANGUAGE_ID."/admin/webdebug_sms_template_edit.php");?>

<?if($arCurrentEvent = $resCurrentEvent->GetNext()):?>
	<?$arCurrentEvent["DESCRIPTION"]=trim($arCurrentEvent["DESCRIPTION"]);?>
	<div id="tr_template_fields_links">
		<?
			$arMacroses = explode("\n", $arCurrentEvent["DESCRIPTION"]);
			$arCurrentEvent["DESCRIPTION"] = nl2br($arCurrentEvent["DESCRIPTION"]);
			$arCurrentEvent["DESCRIPTION"] .= GetMessage("WEBDEBUG_SMS_FIELD_DESCRIPTION_ADDITIONAL");
			if (CModule::IncludeModule('sale')) {
				$arCurrentEvent["DESCRIPTION"] .= '<div id="wd_sms_sale_block">';
				$arCurrentEvent["DESCRIPTION"] .= GetMessage("WEBDEBUG_SMS_FIELD_DESCRIPTION_ORDER_PARAMETERS");
				$arCurrentEvent["DESCRIPTION"] .= GetMessage("WEBDEBUG_SMS_FIELD_DESCRIPTION_ORDER_PROPERTIES");
				CModule::IncludeModule('webdebug.sms');
				$arProps = CWD_SMS_Provider::GetOrderAllFields();
				foreach($arProps as $arProp) {
					$arCurrentEvent["DESCRIPTION"] .= '<br/>#ORDER_PROP_'.$arProp['CODE'].'# - '.$arProp['NAME'];
				}
				if (empty($arProps)) $arCurrentEvent["DESCRIPTION"] = '<br/>'.GetMessage('WEBDEBUG_SMS_FIELD_DESCRIPTION_ORDER_PROPERTIES_NO');
				$arCurrentEvent["DESCRIPTION"] .= '</div>';
			}
			$arCurrentEvent["DESCRIPTION"] = preg_replace("/(#[A-Z0-9-_]+#)/i", "<a href='#' field='$1'>$1</a>", $arCurrentEvent["DESCRIPTION"]);
			print $arCurrentEvent["DESCRIPTION"];
		?>
	</div>
<?else:?>
	NO
<?endif?>

<script>
	$(".field_target").focus(function(){
		$(".field_target").removeClass("field_current_target");
		$(this).addClass("field_current_target");
	});
	$("#tr_template_fields_links a").click(function(){
		$(".field_current_target").insertAtCaret($(this).attr("field"));
		return false;
	});
	var EmailFieldOldValue = $('#email_field_switcher').val();
	$('#email_field_switcher option').each(function(){
		if ($(this).attr('data-empty')!='Y') {
			$(this).remove();
		}
	});
	<?foreach($arMacroses as $strMacros):?>
		<?if(preg_match('/#([A-Z0-9-_]+)#(.*?)$/i',$strMacros,$M)):?>
			<?$M1 = $M[1];?>
			<?$M2 = trim($M[2]," -\r\n\t\s");?>
			<?if($M2!=''){$M2 = ' ('.$M2.')';}?>
			$('#email_field_switcher').append('<option value="<?=$M1;?>"><?=$M1;?><?=$M2;?></option>');
		<?endif?>
	<?endforeach?>
	$('#tr_avilable_fields').show();
	$('#email_field_switcher').val(EmailFieldOldValue);
</script>