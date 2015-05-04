<?
class CWD_SMS_MainSMS extends CWD_SMS {
	CONST CODE = 'mainsms';
	CONST NAME = 'MainSMS.ru';
	CONST REG_URL = 'http://mainsms.ru/users/sign_up?ref=3277';
	CONST PAY_URL = 'http://mainsms.ru/office/pay_system';
	private $Project;
	private $Key;
	function __construct($arParams) {
		if ($_GET['wd_sms_reg']=='Y') {
			LocalRedirect(self::REG_URL);
		}
		$this->Project = COption::GetOptionString(self::ModuleID, self::CODE.'_project');
		$this->Key = COption::GetOptionString(self::ModuleID, self::CODE.'_key');
	}
	function GetCode() {
		return self::CODE;
	}
	function GetName() {
		return self::NAME;
	}
	function GetPayURL() {
		return self::PAY_URL;
	}
	function OnBeforeRequest(&$arParams) {
		//
	}
	function OnAfterRequest($arParams, &$strResult) {
		//
	}
	function GetSign($arParams) {
		ksort($arParams);
		$arParams[] = $this->Key;
		return md5(sha1(implode(';', $arParams)));
	}
	function Send($arParams) {
		if (!isset($arParams['SENDER']) || empty($arParams['SENDER'])) {
			$arParams['SENDER'] = $this->GetDefaultSender();
		}
		if (!defined('BX_UTF') || BX_UTF!==true) {
			$arParams['MESSAGE'] = $GLOBALS['APPLICATION']->ConvertCharset($arParams['MESSAGE'], 'CP1251', 'UTF-8');
		}
		$arPost = array(
			'project' => $this->Project,
			'recipients' => $this->ClearPhoneNumber($arParams['TO']),
			'message' => $arParams['MESSAGE'],
			'sender' => $arParams['SENDER'],
		);
		$arPost['sign'] = $this->GetSign($arPost);
		$arData = array(
			'URL' => 'http://mainsms.ru/api/mainsms/message/send',
			'METHOD' => 'POST',
			'CONTENT' => http_build_query($arPost),
			'HEADER' => 'Content-type: application/x-www-form-urlencoded',
		);
		$strResult = $this->Request($arData);
		$arResult = $this->json_decode($strResult,true);
		if ($arResult['status'] == 'success') {
			return true;
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Message sent error:');
			CWD_SMS_Provider::Log($arResult);
			CWD_SMS_Provider::Log($arParams);
			return false;
		}
	}
	function GetSendersList() {
		if (isset($GLOBALS['WD_GetSendersList'][self::CODE])) {
			return $GLOBALS['WD_GetSendersList'][self::CODE];
		}
		$arResult = array();
		if ($this->Project=='' || $this->Key=='') {
			return $arResult;
		}
		$arPost = array(
			'project' => $this->Project,
		);
		$arPost['sign'] = $this->GetSign($arPost);
		$arData = array(
			'URL' => 'http://mainsms.ru/api/mainsms/sender/list',
			'METHOD' => 'POST',
			'CONTENT' => http_build_query($arPost),
			'HEADER' => 'Content-type: application/x-www-form-urlencoded',
		);
		$strResult = $this->Request($arData);
		$arResult = $this->json_decode($strResult,true);
		if ($arResult['status']=='error') {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Get sender list error:');
			CWD_SMS_Provider::Log($arResult);
		}
		$arResult = $arResult['senders'];
		if (!is_array($arResult)) {
			$arResult = array();
		}
		$arResultTmp = array();
		foreach($arResult as $strItem) {
			$arResultTmp[$strItem] = $strItem;
		}
		$GLOBALS['WD_GetSendersList'][self::CODE] = $arResultTmp;
		return $arResultTmp;
	}
	function GetDefaultSender() {
		$arSenders = $this->GetSendersList();
		if (!is_array($arSenders) || empty($arSenders)) {
			$arSenders = array('');
		}
		$strDefaultSender = COption::GetOptionString(self::ModuleID,self::CODE.'_sender');
		if (in_array($strDefaultSender,$arSenders)) {
			return $strDefaultSender;
		}
		foreach($arSenders as $strSender) {
			return $strSender;
		}
	}
	function GetBalance() {
		$arPost = array(
			'project' => $this->Project,
		);
		$arPost['sign'] = $this->GetSign($arPost);
		$arData = array(
			'URL' => 'http://mainsms.ru/api/mainsms/message/balance',
			'METHOD' => 'POST',
			'CONTENT' => http_build_query($arPost),
			'HEADER' => 'Content-type: application/x-www-form-urlencoded',
		);
		$strResult = $this->Request($arData);
		$arResult = $this->json_decode($strResult,true);
		if ($arResult['status'] == 'success') {
			return number_format($arResult['balance'], 2, '.', ' ').' RUB';
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Balance receive error:');
			CWD_SMS_Provider::Log($arResult);
			return false;
		}
	}
	function GetTabContent() {
		global $MESS, $APPLICATION;
		$Mess = array();
		$Mess['CWD_MAINSMS_PROJECT'] = 'Проект';
		$Mess['CWD_MAINSMS_KEY'] = 'API-ключ';
		$Mess['CWD_MAINSMS_SENDER'] = 'Отправитель';
		$Mess['CWD_MAINSMS_BALANCE'] = 'Остаток на счету';
		$Mess['CWD_MAINSMS_REGISTER_LINK'] = '<a href="%s"><span class="webdebug_sms_register_icon"></span> Регистрация у SMS-провайдера</a> (обязательно)';
		if (!defined('BX_UTF') || BX_UTF!==true) {
			foreach($Mess as $Key => $Value) {
				$Mess[$Key] = $APPLICATION->ConvertCharset($Value, 'UTF-8', 'CP1251');
			}
		}
		foreach($Mess as $Key => $Value) {
			$MESS[$Key] = $Value;
		}
		$arOptions = array();
		$arOptions[self::CODE.'_project'] = '';
		$arOptions[self::CODE.'_key'] = '';
		$arOptions[self::CODE.'_sender'] = '';
		if (defined('WEBDEBUG_SMS_SAVING_OPTIONS') && WEBDEBUG_SMS_SAVING_OPTIONS===true) {
			foreach($arOptions as $strOption => $Value) {
				COption::SetOptionString(self::ModuleID, $strOption, $_REQUEST[$strOption]);
			}
		} else {
			$arSenders = $this->GetSendersList();
		}
		foreach($arOptions as $strOption => $Value) {
			$arOptions[$strOption] = COption::GetOptionString(self::ModuleID, $strOption);
		}
		$Balance = $this->GetBalance();
		ob_start();
		?>
		<table style="width:100%">
			<tbody>
				<?if($Balance!==false):?>
					<tr>
						<td style="text-align:right; width:30%"><?=GetMessage('CWD_MAINSMS_BALANCE')?>:</td>
						<td><big><?=$Balance;?></big></td>
					</tr>
					<tr><td colspan="2"><br/></td></tr>
				<?endif?>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_MAINSMS_PROJECT')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_project" value="<?=$arOptions[self::CODE.'_project'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_MAINSMS_KEY')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_key" value="<?=$arOptions[self::CODE.'_key'];?>" /></td>
				</tr>
				<?if(!empty($arSenders)):?>
					<tr>
						<td style="text-align:right; width:30%"><?=GetMessage('CWD_MAINSMS_SENDER')?>:</td>
						<td>
							<select name="<?=self::CODE;?>_sender">
								<option value=""></option>
								<?foreach($arSenders as $SenderKey => $SenderName):?>
									<option value="<?=$SenderKey;?>"<?if($SenderKey==$arOptions[self::CODE.'_sender']):?> selected="selected"<?endif?>><?=$SenderName;?></option>
								<?endforeach?>
							</select>
						</td>
					</tr>
				<?endif?>
				<?if ($this->Project=='' || $this->Key==''):?>
					<?$RegisterLink = $APPLICATION->GetCurPageParam('wd_sms_reg=Y',array('wd_sms_reg'));?>
					<tr><td colspan="2"><br/></td></tr>
					<tr>
						<td style="text-align:right; width:30%"></td>
						<td><?=sprintf(GetMessage('CWD_MAINSMS_REGISTER_LINK'),$RegisterLink)?></td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
}
?>