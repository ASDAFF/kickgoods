<?
class CWD_SMS_Bytehand extends CWD_SMS {
	CONST CODE = 'bytehand';
	CONST NAME = 'Bytehand.com';
	CONST REG_URL = 'https://www.bytehand.com/registration?r=4342a37828645c79';
	CONST PAY_URL = 'https://www.bytehand.com/secure/pay';
	private $ID;
	private $Key;
	function __construct($arParams) {
		if ($_GET['wd_sms_reg']=='Y') {
			LocalRedirect(self::REG_URL);
		}
		$this->ID = COption::GetOptionString(self::ModuleID, self::CODE.'_id');
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
	function Send($arParams) {
		if (!isset($arParams['SENDER']) || empty($arParams['SENDER'])) {
			$arParams['SENDER'] = $this->GetDefaultSender();
		}
		if (!defined('BX_UTF') || BX_UTF!==true) {
			$arParams['MESSAGE'] = $GLOBALS['APPLICATION']->ConvertCharset($arParams['MESSAGE'], 'CP1251', 'UTF-8');
		}
		$arGet = array(
			'id' => $this->ID,
			'key' => $this->Key,
			'to' => $this->ClearPhoneNumber($arParams['TO']),
			'from' => $arParams['SENDER'],
			'text' => $arParams['MESSAGE'],
		);
		$arData = array(
			'URL' => 'http://bytehand.com:3800/send?'.http_build_query($arGet),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = $this->json_decode($strResult,true);
		if ($arResult['status']===0) {
			return true;
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Message sent error:'.$arResult['description']);
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
		if ($this->ID=='' || $this->Key=='') {
			return $arResult;
		}
		$arGet = array(
			'id' => $this->ID,
			'key' => $this->Key,
			'state' => 'ACCEPTED',
		);
		$arData = array(
			'URL' => 'http://bytehand.com:3800/signatures?'.http_build_query($arGet),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = $this->json_decode($strResult,true);
		if (!isset($arResult['status'])) {
			if (is_array($arResult)) {
				foreach($arResult as $arSender) {
					$SenderCode = $arSender['text'];
					if (!defined('BX_UTF')||BX_UTF!==true) {
						$SenderCode = $GLOBALS['APPLICATION']->ConvertCharset($SenderCode, 'UTF-8', 'CP1251');
					}
					$arResultTmp[$SenderCode] = $SenderCode;
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
			'id' => $this->ID,
			'key' => $this->Key,
		);
		$arData = array(
			'URL' => 'http://bytehand.com:3800/balance?'.http_build_query($arGet),
			'METHOD' => 'GET',
		);
		$strResult = $this->Request($arData);
		$arResult = $this->json_decode($strResult,true);
		if ($arResult['status']===0) {
			return number_format($arResult['description'], 2, '.', ' ').' RUB';
		} else {
			CWD_SMS_Provider::Log('('.self::CODE.')'.' Balance receive error:');
			CWD_SMS_Provider::Log($arResult);
			return false;
		}
	}
	function GetTabContent() {
		global $MESS, $APPLICATION;
		$Mess = array();
		$Mess['CWD_BYTEHAND_ID'] = 'ID';
		$Mess['CWD_BYTEHAND_KEY'] = 'Ключ';
		$Mess['CWD_BYTEHAND_SENDER'] = 'Отправитель';
		$Mess['CWD_BYTEHAND_SENDER_EMPTY'] = '--- выберите отправителя ---';
		$Mess['CWD_BYTEHAND_BALANCE'] = 'Остаток на счету';
		$Mess['CWD_BYTEHAND_REGISTER_LINK'] = '<a href="%s"><span class="webdebug_sms_register_icon"></span> Регистрация у SMS-провайдера</a> (обязательно)';
		if (!defined('BX_UTF') || BX_UTF!==true) {
			foreach($Mess as $Key => $Value) {
				$Mess[$Key] = $APPLICATION->ConvertCharset($Value, 'UTF-8', 'CP1251');
			}
		}
		foreach($Mess as $Key => $Value) {
			$MESS[$Key] = $Value;
		}
		$arOptions = array();
		$arOptions[self::CODE.'_id'] = '';
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
						<td style="text-align:right; width:30%"><?=GetMessage('CWD_BYTEHAND_BALANCE')?>:</td>
						<td><big><?=$Balance;?></big></td>
					</tr>
					<tr><td colspan="2"><br/></td></tr>
				<?endif?>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_BYTEHAND_ID')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_id" value="<?=$arOptions[self::CODE.'_id'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_BYTEHAND_KEY')?>:</td>
					<td><input type="text" name="<?=self::CODE;?>_key" value="<?=$arOptions[self::CODE.'_key'];?>" /></td>
				</tr>
				<tr>
					<td style="text-align:right; width:30%"><?=GetMessage('CWD_BYTEHAND_SENDER')?>:</td>
					<td>
						<select name="<?=self::CODE;?>_sender">
							<option value=""><?=GetMessage('CWD_BYTEHAND_SENDER_EMPTY');?></option>
							<?foreach($arSenders as $SenderKey => $SenderName):?>
								<option value="<?=$SenderKey;?>"<?if($SenderKey==$arOptions[self::CODE.'_sender']):?> selected="selected"<?endif?>><?=$SenderName;?></option>
							<?endforeach?>
						</select>
					</td>
				</tr>
				<?if ($this->ID=='' || $this->Key==''):?>
					<?$RegisterLink = $APPLICATION->GetCurPageParam('wd_sms_reg=Y',array('wd_sms_reg'));?>
					<tr><td colspan="2"><br/></td></tr>
					<tr>
						<td style="text-align:right; width:30%"></td>
						<td><?=sprintf(GetMessage('CWD_BYTEHAND_REGISTER_LINK'),$RegisterLink)?></td>
					</tr>
				<?endif?>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}
}
?>