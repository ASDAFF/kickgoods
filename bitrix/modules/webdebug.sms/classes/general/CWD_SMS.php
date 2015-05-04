<?
IncludeModuleLangFile(__FILE__);

abstract class CWD_SMS {
	const ModuleID = 'webdebug.sms';
	private $ResponseHeaders;
	
	abstract function __construct($arParams);
	abstract function GetCode();
	abstract function GetPayURL();
	abstract function GetName();
	abstract function GetBalance();
	abstract function GetTabContent();
	abstract function Send($arParams);
	abstract function GetSendersList();
	abstract function GetDefaultSender();
	
	function OnBeforeRequest(&$arParams) {}
	function OnAfterRequest($arParams, &$strResult) {}
	
	function GetResponseHeaders() {
		return $this->ResponseHeaders;
	}
	
	function Request($arParams) {
		$this->OnBeforeRequest($arParams);
		if (!in_array($arParams['METHOD'],array('GET','POST','HEAD','PUT','DELETE'))) {
			$arParams['METHOD'] = 'GET';
		}
		if (!is_numeric($arParams['TIMEOUT']) || $arParams['TIMEOUT']<=0) {
			$arParams['TIMEOUT'] = 5;
		}
		$arParams['HEADER'] = trim($arParams['HEADER']);
		if (isset($arParams['BASIC_AUTH'])) {
			$arParams['HEADER'] .= 'Authorization: Basic '.$arParams['BASIC_AUTH']."\n";
		}
		if ($arParams['IGNORE_ERRORS']!==false) {
			$arParams['IGNORE_ERRORS'] = true;
		}
		$arContext = array(
			'http' => array(
				'method' => $arParams['METHOD'],
				'timeout' => $arParams['TIMEOUT'],
				'ignore_errors' => $arParams['IGNORE_ERRORS'],
			)
		);
		if (trim($arParams['HEADER'])!='') {
			$arContext['http']['header'] = $arParams['HEADER'];
		}
		if (trim($arParams['CONTENT'])!='') {
			$arContext['http']['content'] = $arParams['CONTENT'];
		}
		$resContext = stream_context_create($arContext);
		$strResult = @file_get_contents($arParams['URL'], false, $resContext);
		$this->ResponseHeaders = $http_response_header;
		$this->OnAfterRequest($arParams, $strResult);
		return $strResult;
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

	/**
	 *	Преобразование текстового XML в массив
	 */
	function Xml2Array($XML) {
		if (!defined('BX_UTF_PCRE_MODIFIER')) {
			define('BX_UTF_PCRE_MODIFIER','u');
		}
		require_once('xml_class.php');
		$objXML = new CDataXML();
		$objXML->LoadString($XML);
		return $objXML->GetArray();
		unset($objXML);
	}
	
	function ArrayToXml($Array, $Margins=false) {
		$XML = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$XML .= $this->_ArrayToXml($Array, 0, $Margins);
		return $XML;
	}
	function _ArrayToXml($Array, $Level, $Margins=false) {
		$N = "";
		$T = "";
		if ($Margins) {
			$N = "\n";
			$T = "\t";
		}
		$S = "";
		for ($i=0; $i<$Level; $i++) {
			$S .= $T;
		}
		$XML = '';
		foreach($Array as $Key => $Value) {
			$strAttributes = '';
			if (isset($Value['@']) && is_array($Value['@']) && !empty($Value['@'])) {
				foreach($Value['@'] as $AttrName => $AttrValue) {
					$strAttributes .= ' '.$AttrName.'="'.$AttrValue.'"';
				}
			}
			$XML .= $S.'<'.$Key.$strAttributes.'>'.$N;
			if (is_array($Value['#']) && !empty($Value['#'])) {
				++$Level;
				foreach($Value['#'] as $SubValue) {
					$XML .= $this->_ArrayToXml($SubValue, $Level);
				}
			} elseif(trim($Value['#'])!='') {
				$XML .= $S.$T.$Value['#'].$N;
			}
			$XML .= $S.'</'.$Key.'>'.$N;
		}
		return $XML;
	}
	
	function ReplaceUnicode($Matches) {
		return str_replace($GLOBALS['WD_SMS_CHARS_FROM'], $GLOBALS['WD_SMS_CHARS_TO'], $Matches[1]);
	}

	function json_decode($json, $assoc = false) {
		mb_internal_encoding("UTF-8");
		
		if (defined('BX_UTF') && BX_UTF===true) {
			$arFrom = array_merge(explode(',',GetMessage('WD_SMS_LETTERS_LOWER_CHARS')),explode(',',GetMessage('WD_SMS_LETTERS_UPPER_CHARS')));
			$arTo = array_merge(explode(',',GetMessage('WD_SMS_LETTERS_LOWER_UNICODE')),explode(',',GetMessage('WD_SMS_LETTERS_UPPER_UNICODE')));
			$L_01 = GetMessage('WD_SMS_LETTERS_01_UPPER');
			$L_33 = GetMessage('WD_SMS_LETTERS_33_LOWER');
			$GLOBALS['WD_SMS_CHARS_FROM'] = $arFrom;
			$GLOBALS['WD_SMS_CHARS_TO'] = $arTo;
			$json = preg_replace_callback('#(['.$L_01.'-'.$L_33.']{1})#isu','CWD_SMS::ReplaceUnicode',$json);
			unset($GLOBALS['WD_SMS_CHARS_FROM']);
			unset($GLOBALS['WD_SMS_CHARS_TO']);
		}
		
		$i = 0;
		$n = strlen($json);
		try {
			$result = $this->json_decode_value($json, $i, $assoc);
			while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
			if ($i < $n) {
				return null;
			}
			return $result;
		} catch (Exception $e) {
			return null;
		}		
	}

	function json_decode_value($json, &$i, $assoc = false) {
		$n = strlen($json);
		while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
		switch ($json[$i]) {
			// object
			case '{':
				$i++;
				$result = $assoc ? array() : new stdClass();
				while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
				if ($json[$i] === '}') {
					$i++;
					return $result;
				}
				while ($i < $n) {
					$key = $this->json_decode_string($json, $i);
					while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
					if ($json[$i++] != ':') {
							throw new Exception("Expected ':' on ".($i - 1));
					}
					if ($assoc) {
							$result[$key] = $this->json_decode_value($json, $i, $assoc);
					} else {
							$result->$key = $this->json_decode_value($json, $i, $assoc);
					}
					while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
					if ($json[$i] === '}') {
							$i++;
							return $result;
					}
					if ($json[$i++] != ',') {
							throw new Exception("Expected ',' on ".($i - 1));
					}
					while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
				}
				throw new Exception("Syntax error");
			// array
			case '[':
				$i++;
				$result = array();
				while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
				if ($json[$i] === ']') {
					$i++;
					return array();
				}
				while ($i < $n) {
					$result[] = $this->json_decode_value($json, $i, $assoc);
					while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
					if ($json[$i] === ']') {
						$i++;
						return $result;
					}
					if ($json[$i++] != ',') {
						throw new Exception("Expected ',' on ".($i - 1));
					}
					while ($i < $n && $json[$i] && $json[$i] <= ' ') $i++;
				}            	
				throw new Exception("Syntax error");
			// string
			case '"':
				return $this->json_decode_string($json, $i);
			// number
			case '-':
				return $this->json_decode_number($json, $i);
			// true
			case 't':
			 if ($i + 3 < $n && substr($json, $i, 4) === 'true') {
				 $i += 4;
				 return true;
			 }
			// false
			case 'f':
			if ($i + 4 < $n && substr($json, $i, 5) === 'false') {
				$i += 5;
				return false;
			}
			// null
			case 'n':
			if ($i + 3 < $n && substr($json, $i, 4) === 'null') {
				$i += 4;
				return null;
			}
			default:
				// number
				if ($json[$i] >= '0' && $json[$i] <= '9') {
					return $this->json_decode_number($json, $i);
				} else {
					throw new Exception("Syntax error");
				};
		}
	}

	function json_decode_string($json, &$i) {
		$result = '';
		$escape = array('"' => '"', '\\' => '\\', '/' => '/', 'b' => "\b", 'f' => "\f", 'n' => "\n", 'r' => "\r", 't' => "\t");
		$n = strlen($json);
		if ($json[$i] === '"') {
			while (++$i < $n) {
				if ($json[$i] === '"') {
					$i++;
					return $result;
				} elseif ($json[$i] === '\\') {
					$i++;
					if ($json[$i] === 'u') {
							$code = "&#".hexdec(substr($json, $i + 1, 4)).";";
							$convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
							$result .= mb_decode_numericentity($code, $convmap, 'UTF-8');
							$i += 4;
					} elseif (isset($escape[$json[$i]])) {
							$result .= $escape[$json[$i]];
					} else {
							break;
					}
				} else {
					$result .= $json[$i];
				}
			}
		}
		throw new Exception("Syntax error"); 		
	}

	function json_decode_number($json, &$i) {
		$result = '';
		if ($json[$i] === '-') {
			$result = '-';
			$i++;
		}
		$n = strlen($json);
		while ($i < $n && $json[$i] >= '0' && $json[$i] <= '9') {
			$result .= $json[$i++];
		}
		if ($i < $n && $json[$i] === '.') {
			$result .= '.';
			$i++;
			while ($i < $n && $json[$i] >= '0' && $json[$i] <= '9') {
				$result .= $json[$i++];
			}
		}
		if ($i < $n && ($json[$i] === 'e' || $json[$i] === 'E')) {
			$result .= $json[$i];
			$i++;
			if ($json[$i] === '-' || $json[$i] === '+') {
					$result .= $json[$i++];
			}
			while ($i < $n && $json[$i] >= '0' && $json[$i] <= '9') {
					$result .= $json[$i++];
			}
		}
		return (0 + $result);
	}
}
?>