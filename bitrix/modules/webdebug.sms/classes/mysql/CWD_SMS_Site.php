<?
IncludeModuleLangFile(__FILE__);

class CWD_SMS_Site {
	
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
	
}

?>