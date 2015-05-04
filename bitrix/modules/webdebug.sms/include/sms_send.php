<?
IncludeModuleLangFile(__FILE__);
?>
<div id="webdebug-sms-send" class="webdebug-sms-options-block">
	<?
		if ($WD_SMS==null || !is_object($WD_SMS)) {
			$strSmsProvider = CWD_SMS_Provider::GetCurrentProvider();
			$WD_SMS = new $strSmsProvider();
		}
		$arSendersList = $WD_SMS->GetSendersList();
		if (isset($_REQUEST["send_sms"])) {
			$arParams = array(
				'TO' => $_POST['sms_receiver'],
				'MESSAGE' => $_POST['sms_text'],
				'SENDER' => $_POST['sms_sender'],
			);
			$bSent = CWD_SMS_Provider::Send($arParams);
			/*
			$bSent = $WD_SMS->Send($arParams);
			*/
			if ($bSent) {
				$_SESSION['WD_SMS_SENT'] = true;
				$URL = '';
				if (is_object($GLOBALS['WD_SMS_TABCONTROL'])) {
					$URL = $GLOBALS['WD_SMS_TABCONTROL']->ActiveTabParam();
				}
				LocalRedirect($APPLICATION->GetCurPageParam($URL,array()));
			} else {
				?>
				<p class="errros" style="color:red">
					<?=GetMessage("WEBDEBUG_SMS_SEND_ERROR");?>
				</p>
				<?
			}
		}
	?>
	<?if($_SESSION['WD_SMS_SENT']):?>
		<p class="success" style="color:green"><?=GetMessage("WEBDEBUG_SMS_SEND_SUCCESS")?></p>
		<?unset($_SESSION['WD_SMS_SENT']);?>
	<?endif?>
	<p class="label"><?=GetMessage("WEBDEBUG_SMS_SEND_RECEIVER")?>:</p>
	<p class="field"><input type="text" name="sms_receiver" value="<?=htmlspecialcharsbx($_POST['sms_receiver']);?>" size="60" /></p>
	<?if(is_array($arSendersList) && !empty($arSendersList)):?>
		<p class="label"><?=GetMessage("WEBDEBUG_SMS_SEND_SENDER")?>:</p>
		<p class="field">
			<?$CurrentSender = COption::GetOptionString('webdebug.sms',$WD_SMS->GetCode().'_sender');?>
			<select name="sms_sender">
				<?foreach ($arSendersList as $SenderID => $SenderName):?>
					<option value="<?=$SenderID?>"<?if($SenderID==$CurrentSender):?> selected="selected"<?endif?>><?=$SenderName?></option>
				<?endforeach?>
			</select>
		</p>
	<?endif?>
	<p class="label"><?=GetMessage("WEBDEBUG_SMS_SEND_TEXT")?> <span id="sms_text_status">0/0</span>:</p>
	<p class="field"><textarea id="sms_text_area" name="sms_text" cols="60" rows="8"></textarea></p>
	<input type="submit" name="send_sms" class="adm-btn-save" value="<?=GetMessage("WEBDEBUG_SMS_SEND_SUBMIT")?>" />
	<p class="small"><?=GetMessage("WEBDEBUG_SMS_SEND_NOTICE_1")?></p>
	<p class="small"><?=GetMessage("WEBDEBUG_SMS_SEND_NOTICE_2")?></p><br/>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type="text/javascript">(function(a){a.event.special.textchange={setup:function(){a(this).data("lastValue","true"===this.contentEditable?a(this).html():a(this).val());a(this).bind("keyup.textchange",a.event.special.textchange.handler);a(this).bind("cut.textchange paste.textchange input.textchange",a.event.special.textchange.delayedHandler)},teardown:function(){a(this).unbind(".textchange")},handler:function(){a.event.special.textchange.triggerIfChanged(a(this))},delayedHandler:function(){var c=a(this);setTimeout(function(){a.event.special.textchange.triggerIfChanged(c)},25)},triggerIfChanged:function(a){var b="true"===a[0].contentEditable?a.html():a.val();b!==a.data("lastValue")&&(a.trigger("textchange",[a.data("lastValue")]),a.data("lastValue",b))}};a.event.special.hastext={setup:function(){a(this).bind("textchange",a.event.special.hastext.handler)},teardown:function(){a(this).unbind("textchange",a.event.special.hastext.handler)},handler:function(c,b){""===b&&b!==a(this).val()&&a(this).trigger("hastext")}};a.event.special.notext={setup:function(){a(this).bind("textchange",a.event.special.notext.handler)},teardown:function(){a(this).unbind("textchange",a.event.special.notext.handler)},handler:function(c,b){""===a(this).val()&&a(this).val()!==b&&a(this).trigger("notext")}}})(jQuery);</script>
	<script type="text/javascript">
	function RussSymbolsExists(TextValue) {
		var reg = new RegExp('([<?=GetMessage("WEBDEBUG_SMS_SEND_RUSS_SYMBOL_A")?>-<?=GetMessage("WEBDEBUG_SMS_SEND_RUSS_SYMBOL_YA")?>])','i');
		return (reg.test(TextValue) ? true : false);
	}
	function PrintStatus(Length, Count) {
		Length = Length + "<?=GetMessage("WEBDEBUG_SMS_SEND_STATUS_LENGTH")?>";
		Count = Count + "<?=GetMessage("WEBDEBUG_SMS_SEND_STATUS_COUNT")?>";
		$("#sms_text_status").html(Length + " / " + Count);
	}
	function LengthCalculation(Sender) {
		var Value = Sender.val();
		var CyrillicEnabled = RussSymbolsExists(Value);
		var Length = Value.length;
		var Count = 0;
		var BaseLengthFull = (CyrillicEnabled ? 70 : 160);
		var BaseLengthPart = (CyrillicEnabled ? 67 : 153);
		if (Length<=BaseLengthFull) {
			Count = 1;
		} else {
			Count = Math.ceil(Length/BaseLengthPart);
		}
		PrintStatus(Length, Count);
	}
	$("#sms_text_area").bind("textchange", function(){
		LengthCalculation($(this));
	}).trigger("textchange");
	<?if(isset($_POST['sms_text'])):?>
		$(document).ready(function(){
			$("#sms_text_area").val("<?=htmlspecialcharsbx($_POST['sms_text']);?>");
		});
	<?endif?>
	</script>
</div>