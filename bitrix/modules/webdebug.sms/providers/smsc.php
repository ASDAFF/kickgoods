<?
class CWD_SMS_SMSc extends CWD_SMS {
	CONST CODE = 'smsc';
	CONST NAME = 'SMSc.ru';
	CONST REG_URL = 'http://www.smsc.ru/reg/?pp373643';
	CONST PAY_URL = 'http://smsc.ru/pay/';
	private $Username;
	private $Password;
	public $RegURL = '';
	public $PayURL = '';
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
		// Clear old cache in SESSION
		if (is_array($_SESSION['WD_SMS_SMSC'])) {
			foreach($_SESSION['WD_SMS_SMSC'] as $Key => $Value) {
				if(!isset($Value['TIME']) || time()-$Value['TIME']>=100) {
					unset($_SESSION['WD_SMS_SMSC'][$Key]);
				}
			}
		}
	}
	function OnAfterRequest($arParams, &$strResult) {
		// Caching, solution from error: "ERROR = 9 (duplicate request, wait a minute)"
		$strError9 = 'ERROR = 9 (duplicate request, wait a minute)';
		$ResponseHeaders = $this->GetResponseHeaders();
		$strParams = StrToUpper(MD5(serialize($arParams)));
		if ($strResult==$strError9) {
			if (isset($_SESSION['WD_SMS_SMSC'][$strParams]['TEXT'])) {
				$strResult = $_SESSION['WD_SMS_SMSC'][$strParams]['TEXT'];
			}
		} else {
			$_SESSION['WD_SMS_SMSC'][$strParams] = array(
				'TEXT' => $strResult,
				'TIME' => time(),
			);
		}
	}
	function Send($arParams) {
		if (!isset($arParams['SENDER']) || empty($arParams['SENDER'])) {
			$arParams['SENDER'] = $this->GetDefaultSender();
		}
		if (!defined('BX_UTF') || BX_UTF!==true) {
			$arParams['MESSAGE'] = $GLOBALS['APPLICATION']->ConvertCharset($arParams['MESSAGE'], 'CP1251', 'UTF-8');
		}
		$arPost = array(
			'login' => $this->Username,
			'psw' => $this->Password,
			'phones' => $this->ClearPhoneNumber($arParams['TO']),
			'sender' => $arParams['SENDER'],
			'mes' => $arParams['MESSAGE'],
			'charset' => 'utf-8',
			'pp' => '373643',
		);
		$arData = array(
			'URL' => 'http://smsc.ru/sys/send.php',
			'METHOD' => 'POST',
			'CONTENT' => http_build_query($arPost),
			'HEADER' => 'Content-type: application/x-www-form-urlencoded',
		);
		$strResult = $this->Request($arData);
		if (strpos($strResult,'OK')===0) {
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
			'get' => '1',
			'login' => $this->Username,
			'psw' => $this->Password,
		);
		$arData = array(
			'URL' => 'http://smsc.ru/sys/senders.php',
			'METHOD' => 'POST',
			'CONTENT' => http_build_query($arPost),
			'HEADER' => 'Content-type: application/x-www-form-urlencoded',
		);
		$strSenders = trim($this->Request($arData));
		if (stripos($strSenders,'ERROR')===0) {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Get sender list error:');
			CWD_SMS_Provider::Log($strSenders);
			return $arResult;
		}
		$arSenders = explode("\n",$strSenders);
		foreach($arSenders as $strSender) {
			$arSender = explode('=',$strSender);
			$arResult[] = trim($arSender[1]);
		}
		if (empty($arResult)) {
			$arResult = array('');
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
			'login' => $this->Username,
			'psw' => $this->Password,
		);
		$arData = array(
			'URL' => 'http://smsc.ru/sys/balance.php',
			'METHOD' => 'POST',
			'CONTENT' => http_build_query($arPost),
			'HEADER' => 'Content-type: application/x-www-form-urlencoded',
		);
		$strResult = $this->Request($arData);
		if (stripos($strResult,'ERROR')===0) {
			return false;
		} elseif (FloatVal($strResult)==$strResult) {
			return number_format($strResult, 2, '.', ' ').' RUB';
		}
		return false;
	}
	function GetTabContent() {
		global $MESS, $APPLICATION;
		$Mess = array();
		$Mess['CWD_SMSC_USERNAME'] = 'Логин';
		$Mess['CWD_SMSC_PASSWORD'] = 'Пароль';
		$Mess['CWD_SMSC_SENDER'] = 'Отправитель';
		$Mess['CWD_SMSC_BALANCE'] = 'Остаток на счету';
		$Mess['CWD_SMSC_REGISTER_LINK'] = '<a href="%s"><span class="webdebug_sms_register_icon"></span>Регистрация у SMS-провайдера</a> (обязательно)';
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
						<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMSC_BALANCE')?>:</td>
						<td><big><?=$Balance;?></big></td>
					</tr>
					<tr><td colspan="2"><br/></td></tr>
				<?endif?>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMSC_USERNAME')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_username" value="<?=$arOptions[self::CODE.'_username'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMSC_PASSWORD')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_password" value="<?=$arOptions[self::CODE.'_password'];?>" /></td>
				</tr>
				<?if(!empty($arSenders)):?>
					<tr>
						<td style="text-align:right; width:30%"><?=GetMessage('CWD_SMSC_SENDER')?>:</td>
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
						<td><?=sprintf(GetMessage('CWD_SMSC_REGISTER_LINK'),$RegisterLink)?></td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
}
?>