<?
IncludeModuleLangFile(__FILE__);

class CWD_SMS_Provider {
	const ModuleID = 'webdebug.sms';
	function GetList() {
		$arResult = array();
		$ProvidersPath = BX_ROOT.'/modules/'.self::ModuleID.'/providers/';
		if (is_dir($_SERVER['DOCUMENT_ROOT'].$ProvidersPath)) {
			$Handle = opendir($_SERVER['DOCUMENT_ROOT'].$ProvidersPath);
			while (($File = readdir($Handle))!==false) {
				if ($File != '.' && $File != '..') {
					if (is_file($_SERVER['DOCUMENT_ROOT'].$ProvidersPath.$File)) {
						$arPathInfo = pathinfo($File);
						if (ToUpper($arPathInfo['extension'])=='PHP') {
							require_once($_SERVER['DOCUMENT_ROOT'].$ProvidersPath.$File);
						}
					}
				}
			}
			closedir($Handle);
		}
		foreach(GetModuleEvents(self::ModuleID, 'OnGetProvidersList', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent);
		}
		$arDeclaredClasses = get_declared_classes();
		foreach($arDeclaredClasses as $ClassName) {
			if (stripos($ClassName,'CWD_SMS_')===0 && !in_array($ClassName,array('CWD_SMS_Provider','CWD_SMS_Template','CWD_SMS_History'))) {
				$Code = $ClassName::GetCode();
				$Name = $ClassName::GetName();
				$arResult[$ClassName] = array(
					'NAME' => $Name,
					'CODE' => $Code,
					'CLASS' => $ClassName,
				);
			}
		}
		uasort($arResult, function ($a, $b) {
			return strnatcmp($a['NAME'],$b['NAME']);
		});
		return $arResult;
	}
	function GetCurrentProvider() {
		$arProviders = self::GetList();
		$strProvider = COption::GetOptionString(self::ModuleID, 'provider');
		if (is_array($arProviders[$strProvider]) && class_exists($strProvider)) {
			return $strProvider;
		} else {
			foreach($arProviders as $strProvider => $arProvider) {
				return $strProvider;
			}
		}
		return false;
	}
	function TranslitMessage($Message) {
		if (COption::GetOptionString(self::ModuleID, 'use_translit')=='Y') {
			$Message = self::Translit($Message);
		}
		return $Message;
	}
	function Send($arParams=false, $Event=false) {
		$SMS_Class = self::GetCurrentProvider();
		$arProviders = self::GetList();
		if (is_array($arProviders[$SMS_Class])) {
			$SMS = new $SMS_Class();
			if (!is_array($arParams)) {
				$arParams = array();
			}
			if (isset($arParams['MESSAGE'])) {
				$arParams['MESSAGE'] = self::TranslitMessage($arParams['MESSAGE']);
			}
			$bSent = $SMS->Send($arParams);
			if ($bSent) {
				self::AddToHistory($arParams, $SMS_Class::GetCode(), $Event);
				return true;
			}
		}
		return false;
	}
	function GetBalance() {
		$SMS_Class = self::GetCurrentProvider();
		$arProviders = self::GetList();
		if (is_array($arProviders[$SMS_Class])) {
			$SMS = new $SMS_Class();
			return $SMS->GetBalance();
		}
		return false;
	}
	function GetPayURL() {
		$SMS_Class = self::GetCurrentProvider();
		$arProviders = self::GetList();
		if (is_array($arProviders[$SMS_Class])) {
			$SMS = new $SMS_Class();
			return $SMS->GetPayURL();
		}
		return false;
	}
	function GetEventsList($SortField = false, $SortOrder="asc") {
		$arResult = array();
		$resEvents = CEventType::GetList(array("lid"=>LANGUAGE_ID));
		while ($arEvent = $resEvents->GetNext(false,false)) {
			$arResult[] = $arEvent;
		}
		if ($SortField!==false && in_array(ToLower($SortField),array("name","event_name","id","lid","sort"))) {
			$GLOBALS["WEBDEBUG_SMS_SORT_FIELD"] = $SortField;
			$GLOBALS["WEBDEBUG_SMS_SORT_ORDER"] = $SortOrder;
			usort($arResult, "CWD_SMS_Provider::EventSort");
		}
		return $arResult;
	}
	private function EventSort($a, $b) {
		$SortField = strtoupper($GLOBALS["WEBDEBUG_SMS_SORT_FIELD"]);
		$SortOrder = strtoupper($GLOBALS["WEBDEBUG_SMS_SORT_ORDER"]);
		if (!in_array(ToLower($SortField),array("name","event_name","id","lid","sort")) || $a[$SortField] == $b[$SortField]) {
			return 0;
		}
		if (ToLower($SortOrder)=="desc")
			return ($a[$SortField] > $b[$SortField]) ? -1 : 1;
		else
			return ($a[$SortField] < $b[$SortField]) ? -1 : 1;
	}
	/**
	 *	Get phone number of user, selected by email
	 */
	function GetUserPhoneByEmail($UserEmail) {
		$strPhone = false;
		$resUser = CUser::GetList($by='ID',$order='DESC',array('EMAIL'=>$UserEmail));
		if ($arUser = $resUser->GetNext(false,false)) {
			if ($arUser['PERSONAL_MOBILE']!='') {
				$strPhone = $arUser['PERSONAL_MOBILE'];
			} elseif ($arUser['PERSONAL_PHONE']!='') {
				$strPhone = $arUser['PERSONAL_PHONE'];
			} elseif ($arUser['WORK_PHONE']!='') {
				$strPhone = $arUser['WORK_PHONE'];
			}
		}
		foreach(GetModuleEvents(self::ModuleID, 'OnGetUserPhoneByEmail', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, array($UserEmail, &$strPhone));
		}
		return $strPhone;
	}
	/**
	 *	Handle 'OnBeforeEventAdd'
	 */
	function OnBeforeEventAddHandler($Event, $SiteID, $arFields) {
		$resSMSTemplate = CWD_SMS_Template::GetList(array("SORT"=>"ASC"),array("EVENT"=>$Event,"ACTIVE"=>"Y"));
		$bSent = false;
		$bStop = false;
		while ($arSMSTemplate = $resSMSTemplate->GetNext(false,false)) {
			$MessageText = self::ReplaceMacroses($Event, $arSMSTemplate["TEMPLATE"], $arFields, $SiteID);
			$Receiver = false;
			if ($arSMSTemplate['RECEIVER_FROM_EMAIL']=='Y' && !empty($arSMSTemplate['EMAIL_FIELD']) && isset($arFields[$arSMSTemplate['EMAIL_FIELD']])) {
				$Receiver = self::GetUserPhoneByEmail($arFields[$arSMSTemplate['EMAIL_FIELD']]);
			}
			if ($Receiver===false) {
				$Receiver = self::ReplaceMacroses($Event, $arSMSTemplate["RECEIVER"], $arFields, $SiteID);
				if (trim($Receiver)=='') {
					$Receiver = false;
				}
			}
			if ($Receiver!==false) {
				$arParams = array(
					'TO' => $Receiver,
					'MESSAGE' => $MessageText,
				);
				$bSent = self::Send($arParams, $Event);
				if ($bSent && $arSMSTemplate['STOP']=='Y') {
					$bStop = true;
				}
			}
		}
		return ($bStop ? false : true);
	}
	/**
	 *	Replace #FIELDS# to its data
	 */
	function ReplaceMacroses($Event, $Text, $arFields, $SiteID=SITE_ID) {
		// Additional values
		$arFields = self::GetAdditionalFields($Event, $arFields, $SiteID);
		// Get event data
		$resEvents = CEventType::GetList(array("lid"=>LANGUAGE_ID,"TYPE_ID"=>$Event));
		if ($arEvent = $resEvents->GetNext(false,false)) {
			$Description = $arEvent["DESCRIPTION"];
			$arMatches = array();
			if (preg_match_all("/(#[A-Z0-9-_]+#)/i", $arEvent["DESCRIPTION"], $arMatches)) {
				if (is_array($arMatches[1])) {
					foreach ($arMatches[1] as $FieldSharp) {
						$Field = substr($FieldSharp, 1, -1);
						$Text = str_replace($FieldSharp, $arFields[$Field], $Text);
					}
				}
			}
		}
		// Replace additional values
		foreach($arFields as $Key => $Value) {
			$Text = str_replace("#".$Key."#", $Value, $Text);
		}
		return $Text;
	}
	/**
	 *	Get additional fields for some events
	 */
	function GetAdditionalFields($Event, $arFields, $SiteID=SITE_ID) {
		$arUser = array();
		$UserID = false;
		if (CUser::IsAuthorized()) {
			$UserID = CUser::GetID();
		}
		$arOrderProps = array();
		if (ToLower(substr($Event,0,5))=='sale_' && CModule::IncludeModule('sale')) {
			$OrderID = $arFields['ORDER_ID'];
			if ($OrderID>0) {
				$arOrder = CSaleOrder::GetByID($OrderID);
				$arFields['X_ORDER_SUMM'] = $arOrder['PRICE'];
				$arFields['X_ORDER_DATE'] = $arOrder['DATE_INSERT'];
				$arFields['X_ORDER_COMMENTS'] = $arOrder['USER_DESCRIPTION'];
				$UserID = $arOrder['USER_ID'];
				$resProps = CSaleOrderPropsValue::GetList(array("SORT"=>"ASC"), array("ORDER_ID"=>$OrderID));
				while ($arProp = $resProps->GetNext(false,false)) {
					$arOrderProps[$arProp['CODE']] = $arProp['VALUE'];
				}
			}
		}
		if ($UserID>0) {
			$resUser = CUser::GetByID($UserID);
			$arUser = $resUser->GetNext(false,false);
			$arFields["X_USER_ID"] = $arUser["ID"];
			$arFields["X_USER_LOGIN"] = $arUser["LOGIN"];
			$arFields["X_USER_NAME"] = $arUser["NAME"];
			$arFields["X_USER_LAST_NAME"] = $arUser["LAST_NAME"];
			$arFields["X_USER_SECOND_NAME"] = $arUser["SECOND_NAME"];
			$arFields["X_USER_EMAIL"] = $arUser["EMAIL"];
			$arFields["X_USER_MOBILE"] = $arUser["PERSONAL_MOBILE"];
			$arFields["X_USER_PHONE"] = $arUser["PERSONAL_PHONE"];
			if (!$arFields["X_USER_MOBILE"]) {
				$arFields["X_USER_MOBILE"] = $arFields["DEFAULT_PHONE"];
			}
			// Order props
			foreach ($arOrderProps as $Key => $Value) {
				$arFields['ORDER_PROP_'.$Key] = $Value;
			}
			$arFields['X_PHONE'] = $arFields['X_USER_MOBILE'];
			if (!$arFields['X_PHONE']) $arFields['X_PHONE'] = $arFields['ORDER_PROP_PHONE'];
			if (!$arFields['X_PHONE']) $arFields['X_PHONE'] = $arFields['X_USER_PHONE'];
			// Process some events
			if ($Event=="SALE_NEW_ORDER") {
				// Get new order information (summ)
			}
		}
		$arFields["SITE_NAME"] = SITE_SERVER_NAME;
		$arFields["SERVER_NAME"] = COption::GetOptionString('main', 'server_name');
		$arFields["DEFAULT_EMAIL_FROM"] = COption::GetOptionString('main', 'email_from');
		// Get sitename
		$resSite = CSite::GetByID($SiteID);
		if ($arSite = $resSite->GetNext()) {
			$arFields["SITE_NAME"] = $arSite["NAME"];
		}
		$arFields["DEFAULT_PHONE"] = COption::GetOptionString(self::ModuleID, 'default_phone');
		
		foreach(GetModuleEvents(self::ModuleID, 'OnGetAdditionalFields', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, array($Event, &$arFields, $SiteID));
		}
		
		return $arFields;
	}
	function GetOrderAllFields() {
		$arResult = array();
		CModule::IncludeModule('sale');
		$resProps = CSaleOrderProps::GetList(array('SORT'=>'ASC'),array('TYPE'=>'TEXT'));
		while ($arProp = $resProps->GetNext(false,false)) {
			$arResult[] = $arProp;
		}
		return $arResult;
	}
	public function Translit($Message) {
		//Get chars
		$TranslitFrom = explode(",", GetMessage("WEBDEBUG_SMS_TRANSLIT_FROM"));
    $TranslitTo = explode(",", GetMessage("WEBDEBUG_SMS_TRANSLIT_TO"));
		// Do it!
		$Length = strlen($Message);
		$Result = "";
		for($i = 0; $i < $Length; $i++) {
      $Char = substr($Message, $i, 1);
			$CharFound = false;
			foreach ($TranslitFrom as $Index => $CharFrom) {
				if ($Char==$CharFrom) {
					$Result .= $TranslitTo[$Index];
					$CharFound = true;
					break;
				}
			}
			// Char with no-replace
			if (!$CharFound) {
				$Result .= $Char;
			}
		}
		return $Result;
	}
	/**
	 *	Log errors to log.txt
	 */
	function Log($Message, $Force=false) {
		if (COption::GetOptionString(self::ModuleID, 'log_errors')=='Y' || $Force) {
			if (is_array($Message)) {
				$Message = print_r($Message,1);
			}
			$file_path = $_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/'.self::ModuleID.'/log.txt';
			$handle = fopen($file_path, 'a+');
			@flock($handle, LOCK_EX);
			fwrite($handle, '['.date('d.m.Y H:i:s').'] '.$Message."\r\n");
			@flock($handle, LOCK_UN);
			fclose($handle);
		}
	}
	/**
	 *	Add message to history
	 */
	function AddToHistory($arParams, $Provider, $Event) {
		if (ToLower($Provider)!='_log') {
			$arFields = array(
				'RECEIVER' => $arParams['TO'],
				'SENDER' => $arParams['SENDER'],
				'MESSAGE' => $arParams['MESSAGE'],
				'PROVIDER' => $Provider,
				'EVENT' => $Event,
				'DATETIME' => date('Y-m-d H:i:s'),
			);
			CWD_SMS_History::Add($arFields);
		}
	}
}

?>