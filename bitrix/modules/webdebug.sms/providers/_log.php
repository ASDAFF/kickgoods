<?
class CWD_SMS_Log extends CWD_SMS {
	CONST CODE = 'log';
	CONST NAME = '--- Save to log.txt ---';
	CONST REG_URL = false;
	CONST PAY_URL = false;
	private $Username;
	private $Password;
	function __construct($arParams) {
		//
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
		CWD_SMS_Provider::Log('('.self::CODE.')'.' Sending message:', true);
		CWD_SMS_Provider::Log($arParams, true);
		return true;
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
		$Mess['CWD_LOG_NOTICE'] = 'Данный виртуальный провайдер вместо отправки SMS выполняет их логирование в лог-файл модуля (log.txt).';
		if (!defined('BX_UTF') || BX_UTF!==true) {
			foreach($Mess as $Key => $Value) {
				$Mess[$Key] = $APPLICATION->ConvertCharset($Value, 'UTF-8', 'CP1251');
			}
		}
		foreach($Mess as $Key => $Value) {
			$MESS[$Key] = $Value;
		}
		ob_start();
		?>
		<div><?=GetMessage('CWD_LOG_NOTICE');?></div>
		<?
		return ob_get_clean();
	}
}
?>