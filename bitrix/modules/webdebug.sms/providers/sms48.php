<?
class CWD_SMS_Sms48 extends CWD_SMS {
	CONST CODE = 'sms48';
	CONST NAME = 'Sms48.ru';
	CONST REG_URL = 'http://sms48.ru/regme.php?pid2=122';
	CONST PAY_URL = 'http://sms48.ru/acc_invoices.php#pay';
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
			if (in_array($Char,array('1','2','3','4','5','6','7','8','9','0'))) {
				$strResult .= $Char;
			}
		}
		return $strResult;
	}
	function Send($arParams) {
		if (!isset($arParams['SENDER']) || empty($arParams['SENDER'])) {
			$arParams['SENDER'] = $this->GetDefaultSender();
		}
		if (defined('BX_UTF') && BX_UTF===true) {
			$arParams['MESSAGE'] = $GLOBALS['APPLICATION']->ConvertCharset($arParams['MESSAGE'], 'UTF-8', 'CP1251');
		}
		$arParams['TO'] = $this->ClearPhoneNumber($arParams['TO']);
		$arPost = array(
			'login' => $this->Username,
			'to' => $arParams['TO'],
			'msg' => $arParams['MESSAGE'],
			'from' => $arParams['SENDER'],
		);
		$arPost['check2'] = $this->GetSign($arParams['TO']);
		$arData = array(
			'URL' => 'https://sms48.ru/send_sms.php?'.http_build_query($arPost),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		if ($strResult=='1' || $strResult=='Sent for moderation') {
			return true;
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Message sent error:');
			CWD_SMS_Provider::Log($strResult);
			CWD_SMS_Provider::Log($arParams);
			return false;
		}
	}
	function GetSendersList() {
		$strSender = COption::GetOptionString(self::ModuleID,self::CODE.'_sender');
		return array($strSender=>$strSender);
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
		return false;
	}
	function GetTabContent() {
		global $MESS, $APPLICATION;
		$Mess = array();
		$Mess['CWD_SMS48_USERNAME'] = 'Логин (e-mail)';
		$Mess['CWD_SMS48_PASSWORD'] = 'Пароль';
		$Mess['CWD_SMS48_SENDER'] = 'Отправитель';
		$Mess['CWD_SMS48_REGISTER_LINK'] = '<a href="%s"><span class="webdebug_sms_register_icon"></span> Регистрация у SMS-провайдера</a> (обязательно)';
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
		}
		foreach($arOptions as $strOption => $Value) {
			$arOptions[$strOption] = COption::GetOptionString(self::ModuleID, $strOption);
		}
		ob_start();
		?>
		<table style="width:100%">
			<tbody>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMS48_USERNAME')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_username" value="<?=$arOptions[self::CODE.'_username'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMS48_PASSWORD')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_password" value="<?=$arOptions[self::CODE.'_password'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMS48_SENDER')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_sender" value="<?=$arOptions[self::CODE.'_sender'];?>" /></td>
				</tr>
				<?if ($this->Username=='' || $this->Password==''):?>
					<?$RegisterLink = $APPLICATION->GetCurPageParam('wd_sms_reg=Y',array('wd_sms_reg'));?>
					<tr><td colspan="2"><br/></td></tr>
					<tr>
						<td style="text-align:right; width:30%"></td>
						<td><?=sprintf(GetMessage('CWD_SMS48_REGISTER_LINK'),$RegisterLink)?></td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
}
?>