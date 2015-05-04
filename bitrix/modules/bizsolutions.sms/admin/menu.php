<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("bizsolutions.sms")!="D")
{
	$aMenu = array	(
        "parent_menu" => "global_menu_services",
        "section" => "SMSBiz",
        "sort" => 100,
        "text" => GetMessage("MENU_MAIN"),
        "title" => GetMessage("MENU_MAIN_TITLE"),
        "url" => "bizsolutionsSmsIndex.php?lang=".LANGUAGE_ID,
        "icon" => "smsbiz_menu_icon",
        "page_icon" => "smsbiz_page_icon",
        "items_id" => "menu_smsbiz",
        "items" => array(
            array(
                "text" => GetMessage("SECTION_SEND_SMS_DELIVERY"),
                "url" => "bizsolutionsSmsSentSmsDelivery.php?lang=".LANGUAGE_ID,
                "title" => GetMessage("SECTION_SEND_SMS_DELIVERY_TITLE"),
                "more_url" => Array()
            ),

            array(
                "text" => GetMessage("SECTION_REPORTS"),
                "url" => "bizsolutionsSmsReport.php?lang=".LANGUAGE_ID,
                "title" => GetMessage("SECTION_REPORTS_TITLE"),
                "more_url" => Array()
            ),
        )
    );

	return $aMenu;
}
return false;
?>
