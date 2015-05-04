<?
class CWD_SMS_WebSMS extends CWD_SMS {
	CONST CODE = 'websms';
	CONST NAME = 'WebSMS.ru';
	CONST REG_URL = 'http://websms.ru/r?agent=65518';
	CONST PAY_URL = 'http://cab.websms.ru/Electron.asp';
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
	function GetSign($arParams) {
		ksort($arParams);
		$arParams[] = $this->Key;
		return md5(sha1(implode(';', $arParams)));
	}
	function Send($arParams) {
		if (!defined('BX_UTF') || BX_UTF!==true) {
			$arParams['MESSAGE'] = $GLOBALS['APPLICATION']->ConvertCharset($arParams['MESSAGE'], 'CP1251', 'UTF-8');
		}
		$arPost = array(
			'Http_username' => $this->Username,
			'Http_password' => $this->Password,
			'Phone_list' => $this->ClearPhoneNumber($arParams['TO']),
			'Message' => $arParams['MESSAGE'],
		);
		$arData = array(
			'URL' => 'http://cab.websms.ru/http_in6.asp',
			'METHOD' => 'POST',
			'CONTENT' => http_build_query($arPost),
			'HEADER' => 'Content-type: application/x-www-form-urlencoded',
		);
		$strResult = $this->Request($arData);
		if (in_array('HTTP/1.1 200 OK',$this->GetResponseHeaders())) {
			return true;
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Message sent error:');
			CWD_SMS_Provider::Log($strResult);
			CWD_SMS_Provider::Log($arParams);
			return false;
		}
	}
	function GetSendersList() {
		return false;
	}
	function GetDefaultSender() {
		return false;
	}
	function GetBalance() {
		return false;
	}
	function GetTabContent() {
		global $MESS, $APPLICATION;
		$Mess = array();
		$Mess['CWD_WEBSMS_USERNAME'] = 'Логин';
		$Mess['CWD_WEBSMS_PASSWORD'] = 'Пароль';
		$Mess['CWD_WEBSMS_REGISTER_LINK'] = '<a href="%s"><span class="webdebug_sms_register_icon"></span> Регистрация у SMS-провайдера</a> (обязательно)';
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
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_WEBSMS_USERNAME')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_username" value="<?=$arOptions[self::CODE.'_username'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_WEBSMS_PASSWORD')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_password" value="<?=$arOptions[self::CODE.'_password'];?>" /></td>
				</tr>
				<?if ($this->Username=='' || $this->Password==''):?>
					<?$RegisterLink = $APPLICATION->GetCurPageParam('wd_sms_reg=Y',array('wd_sms_reg'));?>
					<tr><td colspan="2"><br/></td></tr>
					<tr>
						<td style="text-align:right; width:30%"></td>
						<td><?=sprintf(GetMessage('CWD_WEBSMS_REGISTER_LINK'),$RegisterLink)?></td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
}
?>