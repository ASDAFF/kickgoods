<?
class CWD_SMS_SmsRu extends CWD_SMS {
	CONST CODE = 'smsru';
	CONST NAME = 'Sms.ru';
	CONST REG_URL = 'http://webdebug.sms.ru/';
	CONST PAY_URL = 'http://sms.ru/pay.php';
	private $Username;
	private $Password;
	function __construct($arParams) {
		if ($_GET['wd_sms_reg']=='Y') {
			LocalRedirect(self::REG_URL);
		}
		$this->Username = COption::GetOptionString(self::ModuleID, self::CODE.'_username');
		$this->Password = COption::GetOptionString(self::ModuleID, self::CODE.'_password');
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
	function GetSign($Receiver) {
		return MD5($this->Username.MD5($this->Password).$Receiver);
	}
	function ClearPhoneNumber($Phone) {
		$strResult = '';
		for($i=0; $i<strlen($Phone); $i++) {
			$Char = substr($Phone,$i,1);
			if (in_array($Char,array('+','1','2','3','4','5','6','7','8','9','0'))) {
				$strResult .= $Char;
			}
		}
		return $strResult;
	}
	function Send($arParams) {
		if (!isset($arParams['SENDER']) || empty($arParams['SENDER'])) {
			$arParams['SENDER'] = $this->GetDefaultSender();
		}
		if (!defined('BX_UTF') || BX_UTF!==true) {
			$arParams['MESSAGE'] = $GLOBALS['APPLICATION']->ConvertCharset($arParams['MESSAGE'], 'CP1251', 'UTF-8');
		}
		$arParams['TO'] = $this->ClearPhoneNumber($arParams['TO']);
		$arPost = array(
			'login' => $this->Username,
			'password' => $this->Password,
			'to' => $arParams['TO'],
			'text' => $arParams['MESSAGE'],
			'partner_id' => '93137',
		);
		if (trim($arParams['SENDER'])!='') {
			$arPost['from'] = $arParams['SENDER'];
		}
		$arPost['check2'] = $this->GetSign($arParams['TO']);
		$arData = array(
			'URL' => 'http://sms.ru/sms/send?'.http_build_query($arPost),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = explode("\n",$strResult);
		$arResult = array_filter($arResult);
		if ($arResult[0]=='100') {
			return true;
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Message sent error:');
			CWD_SMS_Provider::Log($strResult);
			CWD_SMS_Provider::Log($arParams);
			return false;
		}
	}
	function GetSendersList() {
		if (isset($GLOBALS['WD_GetSendersList'][self::CODE])) {
			return $GLOBALS['WD_GetSendersList'][self::CODE];
		}
		$arResult = array();
		if ($this->Username=='' || $this->Password=='') {
			return $arResult;
		}
		$arPost = array(
			'login' => $this->Username,
			'password' => $this->Password,
		);
		$arData = array(
			'URL' => 'http://sms.ru/my/senders?'.http_build_query($arPost),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = explode("\n",$strResult);
		$arResult = array_filter($arResult);
		$arResultTmp = array();
		if ($arResult[0]=='100') {
			unset($arResult[0]);
			foreach($arResult as $strItem) {
				$arResultTmp[$strItem] = $strItem;
			}
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Get sender list error:');
			CWD_SMS_Provider::Log($strResult);
			return $arResultTmp;
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
			'login' => $this->Username,
			'password' => $this->Password,
		);
		$arPost['sign'] = $this->GetSign($arPost);
		$arData = array(
			'URL' => 'http://sms.ru/my/balance?'.http_build_query($arPost),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = explode("\n",$strResult);
		$arResult = array_filter($arResult);
		if ($arResult[0]=='100') {
			return number_format($arResult[1], 2, '.', ' ').' RUB';
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Balance receive error:');
			CWD_SMS_Provider::Log($arResult);
			return false;
		}
	}
	function GetTabContent() {
		global $MESS, $APPLICATION;
		$Mess = array();
		$Mess['CWD_SMSRU_USERNAME'] = 'Телефон';
		$Mess['CWD_SMSRU_PASSWORD'] = 'Пароль';
		$Mess['CWD_SMSRU_SENDER'] = 'Отправитель';
		$Mess['CWD_SMSRU_BALANCE'] = 'Остаток на счету';
		$Mess['CWD_SMSRU_REGISTER_LINK'] = '<a href="%s"><span class="webdebug_sms_register_icon"></span> Регистрация у SMS-провайдера</a> (обязательно)';
		if (!defined('BX_UTF') || BX_UTF!==true) {
			foreach($Mess as $Key => $Value) {
				$Mess[$Key] = $APPLICATION->ConvertCharset($Value, 'UTF-8', 'CP1251');
			}
		}
		foreach($Mess as $Key => $Value) {
			$MESS[$Key] = $Value;
		}
		$arOptions = array();
		$arOptions[self::CODE.'_username'] = '';
		$arOptions[self::CODE.'_password'] = '';
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
						<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMSRU_BALANCE')?>:</td>
						<td><big><?=$Balance;?></big></td>
					</tr>
					<tr><td colspan="2"><br/></td></tr>
				<?endif?>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMSRU_USERNAME')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_username" value="<?=$arOptions[self::CODE.'_username'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMSRU_PASSWORD')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_password" value="<?=$arOptions[self::CODE.'_password'];?>" /></td>
				</tr>
				<?if(!empty($arSenders)):?>
					<tr>
						<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMSRU_SENDER')?>:</td>
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
				<?if ($this->Username=='' || $this->Password==''):?>
					<?$RegisterLink = $APPLICATION->GetCurPageParam('wd_sms_reg=Y',array('wd_sms_reg'));?>
					<tr><td colspan="2"><br/></td></tr>
					<tr>
						<td style="text-align:right; width:30%"></td>
						<td><?=sprintf(GetMessage('CWD_SMSRU_REGISTER_LINK'),$RegisterLink)?></td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
}
?>