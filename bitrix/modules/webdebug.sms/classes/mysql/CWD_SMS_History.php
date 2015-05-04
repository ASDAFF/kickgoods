<?
IncludeModuleLangFile(__FILE__);

class CWD_SMS_History {
	
	// Get sent messages list
	function GetList($arSort=false, $arFilter=false) {
		global $DB;
		if (!is_array($arSort)) {$arSort = array("ID"=>"ASC", "DATETIME"=>"ASC");}
		foreach ($arSort as $Key => $Value) {
			$Value = ToLower($Value);
			if ($Value!="asc" && $Value!="desc") {
				unset($arSort[$Key]);
			}
		}
		$Datetime = $DB->DateToCharFunction("DATETIME");
		$SQL = "SELECT *, {$Datetime} as `DATETIME` FROM `b_webdebug_sms_history`";
		// Filter
		if (is_array($arFilter) && !empty($arFilter)) {
			foreach ($arFilter as $arFilterKey => $arFilterVal) {
				if (trim($arFilterVal)=="") {unset($arFilter[$arFilterKey]);}
			}
			$arWhere = array();
			foreach ($arFilter as $Key => $arFilterItem) {
				$SubStr2 = substr($Key, 0, 2);
				$SubStr1 = substr($Key, 0, 1);
				$Key = $DB->ForSQL($Key);
				$arFilterItem = $DB->ForSQL($arFilterItem);
				if ($SubStr2==">=" || $SubStr2=="<=") {
					$Val = substr($Key, 2);
					if ($SubStr2 == ">=") {$arWhere[] = "`b_webdebug_sms_history`.`{$Val}` >= '{$arFilterItem}'";}
					if ($SubStr2 == "<=") {$arWhere[] = "`b_webdebug_sms_history`.`{$Val}` <= '{$arFilterItem}'";}
				} elseif ($SubStr1==">" || $SubStr1=="<") {
					$Val = substr($Key, 1);
					if ($SubStr1 == ">") {$arWhere[] = "`b_webdebug_sms_history`.`{$Val}` > '{$arFilterItem}'";}
					if ($SubStr1 == "<") {$arWhere[] = "`b_webdebug_sms_history`.`{$Val}` < '{$arFilterItem}'";}
					if ($SubStr1 == "!") {$arWhere[] = "`b_webdebug_sms_history`.`{$Val}` <> '{$arFilterItem}'";}
				} elseif ($SubStr1=="%") {
					$Val = substr($Key, 1);
					$arWhere[] = "upper(`b_webdebug_sms_history`.`{$Val}`) like upper ('%{$arFilterItem}%') and `b_webdebug_sms_history`.`{$Val}` is not null";
				} else {
					$arWhere[] = "`b_webdebug_sms_history`.`{$Key}` = '{$arFilterItem}'";
				}
			}
			if (count($arWhere)>0) {
				$SQL .= " WHERE ".implode(" AND ", $arWhere);
			}
		}
		// Sort
		if (is_array($arSort) && !empty($arSort)) {
			$SQL .= " ORDER BY ";
			$arSortBy = array();
			foreach ($arSort as $arSortKey => $arSortItem) {
				$arSortKey = $DB->ForSQL($arSortKey);
				$arSortItem = $DB->ForSQL($arSortItem);
				if (trim($arSortKey)!="") {
					$SortBy = "`{$arSortKey}`";
					if (trim($arSortItem)!="") {
						$SortBy .= " {$arSortItem}";
					}
					$arSortBy[] = $SortBy;
				}
			}
			$SQL .= implode(", ", $arSortBy);
		}
		return $DB->Query($SQL, false, __LINE__);
	}
	
	// Get by ID
	function GetByID($ID) {
		global $DB;
		$ID = IntVal($ID);
		if ($ID) {
			return self::GetList(false, array("ID"=>$ID));
		} else {
			return new CDBResult;
		}
	}
	
	// Add template
	function Add($arFields) {
		global $DB;
		$SQL_Keys = array();
		$SQL_Vals = array();
		foreach ($arFields as $Key => $Field) {
			$Key = $DB->ForSQL($Key);
			$Field = $DB->ForSQL($Field);
			$SQL_Keys[] = "`{$Key}`";
			$SQL_Vals[] = "'{$Field}'";
		}
		$SQL_Keys = implode(",",$SQL_Keys);
		$SQL_Vals = implode(",",$SQL_Vals);
		$SQL = "INSERT INTO `b_webdebug_sms_history` ({$SQL_Keys}) VALUES ({$SQL_Vals})";
		$Res = $DB->Query($SQL, false, __LINE__);
		$LastID = $DB->LastID();
		return $LastID>0;$LastID;
	}
	
}

?>