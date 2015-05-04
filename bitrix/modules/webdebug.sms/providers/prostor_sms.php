<?
class CWD_SMS_ProstorSms extends CWD_SMS {
	CONST CODE = 'prostor_sms';
	CONST NAME = 'prostor-sms.ru';
	CONST REG_URL = 'http://www.prostor-sms.ru/';
	CONST PAY_URL = 'http://www.prostor-sms.ru/payments.php';
	private $Login;
	private $Password;
	function __construct($arParams) {
		if ($_GET['wd_sms_reg']=='Y') {
			LocalRedirect(self::REG_URL);
		}
		$this->Login = COption::GetOptionString(self::ModuleID, self::CODE.'_login');
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
	function Send($arParams) {
		if (!isset($arParams['SENDER']) || empty($arParams['SENDER'])) {
			$arParams['SENDER'] = $this->GetDefaultSender();
		}
		if (!defined('BX_UTF') || BX_UTF!==true) {
			$arParams['MESSAGE'] = $GLOBALS['APPLICATION']->ConvertCharset($arParams['MESSAGE'], 'CP1251', 'UTF-8');
		}
		$arGet = array(
			'login' => $this->Login,
			'password' => $this->Password,
			'phone' => $this->ClearPhoneNumber($arParams['TO']),
			'text' => $arParams['MESSAGE'],
			'sender' => $arParams['SENDER'],
		);
		$arData = array(
			'URL' => 'http://api.prostor-sms.ru/messages/v2/send/?'.http_build_query($arGet),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = explode(';',$strResult);
		if (count($arResult)==2 && $arResult['0']=='accepted') {
			return true;
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Message sent error.');
			CWD_SMS_Provider::Log($arResult);
			CWD_SMS_Provider::Log($arParams);
			return false;
		}
	}
	function GetSendersList() {
		$arResult = array();
		$arResultTmp = array();
		if (isset($GLOBALS['WD_GetSendersList'][self::CODE])) {
			return $GLOBALS['WD_GetSendersList'][self::CODE];
		}
		if ($this->Login=='' || $this->Password=='') {
			return $arResult;
		}
		$arGet = array(
			'login' => $this->Login,
			'password' => $this->Password,
		);
		$arData = array(
			'URL' => 'http://api.prostor-sms.ru/messages/v2/senders/?'.http_build_query($arGet),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = explode("\n",$strResult);
		if (is_array($arResult)) {
			foreach($arResult as $Value) {
				$arValue = explode(';',$Value);
				$Sender = $arValue[0];
				$Status = $arValue[1];
				if (in_array($Status,array('default','active'))) {
					$arResultTmp[$Sender] = $Sender;
				}
			}
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
		$arGet = array(
			'login' => $this->Login,
			'password' => $this->Password,
		);
		$arData = array(
			'URL' => 'http://api.prostor-sms.ru/messages/v2/balance/?'.http_build_query($arGet),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = explode("\n",$strResult);
		$arResult = explode(';',$arResult[0]);
		if (isset($arResult[1]) && is_numeric($arResult[1])) {
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
		$Mess['CWD_PROSTORSMS_LOGIN'] = 'Логин';
		$Mess['CWD_PROSTORSMS_PASSWORD'] = 'Пароль';
		$Mess['CWD_PROSTORSMS_SENDER'] = 'Отправитель';
		$Mess['CWD_PROSTORSMS_SENDER_EMPTY'] = '--- выберите отправителя ---';
		$Mess['CWD_PROSTORSMS_BALANCE'] = 'Остаток на счету';
		$Mess['CWD_PROSTORSMS_REGISTER_LINK'] = '<a href="%s"><span class="webdebug_sms_register_icon"></span> Регистрация у SMS-провайдера</a> (обязательно)';
		if (!defined('BX_UTF') || BX_UTF!==true) {
			foreach($Mess as $Key => $Value) {
				$Mess[$Key] = $APPLICATION->ConvertCharset($Value, 'UTF-8', 'CP1251');
			}
		}
		foreach($Mess as $Key => $Value) {
			$MESS[$Key] = $Value;
		}
		$arOptions = array();
		$arOptions[self::CODE.'_login'] = '';
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
						<td style="text-align:right; width:30%"><?=GetMessage('CWD_PROSTORSMS_BALANCE')?>:</td>
						<td><big><?=$Balance;?></big></td>
					</tr>
					<tr><td colspan="2"><br/></td></tr>
				<?endif?>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_PROSTORSMS_LOGIN')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_login" value="<?=$arOptions[self::CODE.'_login'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_PROSTORSMS_PASSWORD')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_password" value="<?=$arOptions[self::CODE.'_password'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_PROSTORSMS_SENDER')?>:</td>
					<td>
						<select name="<?=self::CODE;?>_sender">
							<option value=""><?=GetMessage('CWD_PROSTORSMS_SENDER_EMPTY');?></option>
							<?foreach($arSenders as $SenderKey => $SenderName):?>
								<option value="<?=$SenderKey;?>"<?if($SenderKey==$arOptions[self::CODE.'_sender']):?> selected="selected"<?endif?>><?=$SenderName;?></option>
							<?endforeach?>
						</select>
					</td>
				</tr>
				<?if ($this->Login=='' || $this->Password==''):?>
					<?$RegisterLink = $APPLICATION->GetCurPageParam('wd_sms_reg=Y',array('wd_sms_reg'));?>
					<tr><td colspan="2"><br/></td></tr>
					<tr>
						<td style="text-align:right; width:30%"></td>
						<td><?=sprintf(GetMessage('CWD_PROSTORSMS_REGISTER_LINK'),$RegisterLink)?></td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
}
?>