<?
IncludeModuleLangFile(__FILE__);

class CWD_SMS_Template {
	public $LAST_ERROR;
	
	// Get templates list
	function GetList($arSort=false, $arFilter=false) {
		global $DB;
		if (!is_array($arSort)) {$arSort = array("SORT"=>"ASC", "NAME"=>"ASC");}
		foreach ($arSort as $Key => $Value) {
			$Value = ToLower($Value);
			if ($Value!="asc" && $Value!="desc") {
				unset($arSort[$Key]);
			}
		}
		$SQL = "SELECT * FROM `b_webdebug_sms_templates`";
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
					if ($SubStr2 == ">=") {$arWhere[] = "`b_webdebug_sms_templates`.`{$Val}` >= '{$arFilterItem}'";}
					if ($SubStr2 == "<=") {$arWhere[] = "`b_webdebug_sms_templates`.`{$Val}` <= '{$arFilterItem}'";}
				} elseif ($SubStr1==">" || $SubStr1=="<") {
					$Val = substr($Key, 1);
					if ($SubStr1 == ">") {$arWhere[] = "`b_webdebug_sms_templates`.`{$Val}` > '{$arFilterItem}'";}
					if ($SubStr1 == "<") {$arWhere[] = "`b_webdebug_sms_templates`.`{$Val}` < '{$arFilterItem}'";}
					if ($SubStr1 == "!") {$arWhere[] = "`b_webdebug_sms_templates`.`{$Val}` <> '{$arFilterItem}'";}
				} elseif ($SubStr1=="%") {
					$Val = substr($Key, 1);
					$arWhere[] = "upper(`b_webdebug_sms_templates`.`{$Val}`) like upper ('%{$arFilterItem}%') and `b_webdebug_sms_templates`.`{$Val}` is not null";
				} else {
					$arWhere[] = "`b_webdebug_sms_templates`.`{$Key}` = '{$arFilterItem}'";
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
		if (!is_array($arFields) || empty($arFields)) {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_EMPTY_FIELDS");
			return false;
		}
		if (trim($arFields["NAME"])=="") {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_NO_NAME");
			return false;
		}
		$SQL_Keys = array();
		$SQL_Vals = array();
		foreach ($arFields as $Key => $Field) {
			if ($Key=="SITE_ID") continue;
			$Key = $DB->ForSQL($Key);
			$Field = $DB->ForSQL($Field);
			$SQL_Keys[] = "`{$Key}`";
			$SQL_Vals[] = "'{$Field}'";
		}
		$SQL_Keys = implode(",",$SQL_Keys);
		$SQL_Vals = implode(",",$SQL_Vals);
		$SQL = "INSERT INTO `b_webdebug_sms_templates` ({$SQL_Keys}) VALUES ({$SQL_Vals})";
		$Res = $DB->Query($SQL, false, __LINE__);
		if ($Res === false) {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_ADD_ERROR").$DB->db_Error;
			return false;
		}
		$LastID = $DB->LastID();
		if (is_numeric($LastID)) {
			if ($arFields["SITE_ID"]) {
				self::SetTemplateSiteID($LastID, self::TransformSiteArray($arFields["SITE_ID"]));
			}
			return $LastID;
		} else
			return false;
	}
	
	// Update template
	function Update($ID, $arFields) {
		global $DB;
		$ID = IntVal($ID);
		if ($ID==0) {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_UPDATE_NOID");
			return false;
		}
		if (!is_array($arFields) || empty($arFields)) {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_EMPTY_FIELDS");
			return false;
		}
		if (isset($arFields["NAME"]) && trim($arFields["NAME"])=="") {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_NO_NAME");
			return false;
		}
		$SQL_SET = array();
		foreach ($arFields as $Key => $Field) {
			if ($Key=="SITE_ID") continue;
			$Key = $DB->ForSQL($Key);
			$Field = $DB->ForSQL($Field);
			$SQL_SET[] = "`{$Key}`='{$Field}'";
		}
		$SQL_SET = implode(",",$SQL_SET);
		$SQL = "UPDATE `b_webdebug_sms_templates` SET {$SQL_SET} WHERE `ID`='{$ID}' LIMIT 1";
		$Res = $DB->Query($SQL, true, __LINE__);
		if ($Res === false) {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_UPDATE_ERROR").$DB->db_Error;
			return false;
		}
		if ($arFields["SITE_ID"]) {
			self::SetTemplateSiteID($ID, self::TransformSiteArray($arFields["SITE_ID"]));
		}
		return $Res->AffectedRowsCount();
	}
	
	// Delete template
	function Delete($ID) {
		global $DB;
		$ID = IntVal($ID);
		if ($ID==0) {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_DELETE_NOID");
			return false;
		}
		if ($ID==1) {
			$this->LAST_ERROR = GetMessage("WEBDEBUG_IMAGE_ERROR_DELETE_DEFAULT");
			return false;
		}
		$SQL = "DELETE FROM `b_webdebug_sms_templates` WHERE `ID`='{$ID}' LIMIT 1";
		return $DB->Query($SQL, true, __LINE__);
	}
	
	function TransformSiteArray($arSites) {
		$arResult = array();
		if (!is_array($arSites)) $arSites = array($arSites);
		foreach ($arSites as $SiteID => $Value) {
			$arResult[] = $SiteID;
		}
		return $arResult;
	}
	
	function SetTemplateSiteID($TemplateID, $SiteID) {
		global $DB;
		$TemplateID = IntVal($TemplateID);
		$arSiteID = $SiteID;
		if (!is_array($arSiteID)) $arSiteID = array($arSiteID);
		// Get true site list
		$arSites = array();
		$Res = CSite::GetList();
		while ($arSite = $Res->GetNext()) {
			$arSites[$arSite["ID"]] = $arSite;
		}
		// Remove broken
		foreach ($arSiteID as $Key => $SiteID) {
			if (!isset($arSites[$SiteID])) unset($arSiteID[$Key]);
		}
		$SQL = "DELETE FROM `b_webdebug_sms_templates_site` WHERE `TEMPLATE_ID`='{$TemplateID}'";
		print $SQL;
		$DB->Query($SQL, false, __LINE__);
		// Insert new site id
		foreach ($arSiteID as $Key => $SiteID) {
			$SQL = "INSERT INTO `b_webdebug_sms_templates_site` (`TEMPLATE_ID`,`SITE_ID`) VALUES ('{$TemplateID}','{$SiteID}');";
			$DB->Query($SQL, false, __LINE__);
		}
	}
	
	function GetSitesForTemplate($TemplateID) {
		global $DB;
		$arResult = array();
		$TemplateID = IntVal($TemplateID);
		if (!$TemplateID) return $arResult;
		$SQL = "SELECT * FROM `b_webdebug_sms_templates_site` WHERE `TEMPLATE_ID`='{$TemplateID}';";
		$resSites = $DB->Query($SQL, false, __LINE__);
		while ($arSite = $resSites->GetNext(false,false)) {
			$arResult[] = $arSite["SITE_ID"];
		}
		return $arResult;
	}
	
}

?>