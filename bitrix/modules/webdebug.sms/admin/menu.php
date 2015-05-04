<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
	"parent_menu" => "global_menu_services",
	"section" => "webdebug_sms",
	"sort" => 1001,
	"text" => GetMessage("WEBDEBUG_SMS_MENU_NAME"),
	"title" => GetMessage("WEBDEBUG_SMS_MENU_DESC"),
	"url" => "webdebug_sms.php?lang=".LANGUAGE_ID,
	"icon" => "webdebug_sms_icon_17_main",
	"page_icon" => "webdebug_sms_icon_34_main",
	"items_id" => "webdebug_sms_submenu",
	"items" => array(
		array(
			"text" => GetMessage("WEBDEBUG_SMS_MENU_PROFILES_NAME"),
			"url" => "webdebug_sms_templates.php?lang=".LANGUAGE_ID,
			"more_url" => array("webdebug_sms_template_edit.php"),
			"title" => GetMessage("WEBDEBUG_SMS_MENU_PROFILES_DESC"),
			"icon" => "webdebug_sms_icon_17_templates",
		),
		array(
			"text" => GetMessage("WEBDEBUG_SMS_MENU_SEND_NAME"),
			"url" => "webdebug_sms_send.php?lang=".LANGUAGE_ID,
			"more_url" => array(),
			"title" => GetMessage("WEBDEBUG_SMS_MENU_SEND_DESC"),
			"icon" => "webdebug_sms_icon_17_send",
		),
		array(
			"text" => GetMessage("WEBDEBUG_SMS_MENU_BALANCE_NAME"),
			"url" => "webdebug_sms_balance.php?lang=".LANGUAGE_ID,
			"more_url" => array(),
			"title" => GetMessage("WEBDEBUG_SMS_MENU_BALANCE_DESC"),
			"icon" => "webdebug_sms_icon_17_balance",
		),
		array(
			"text" => GetMessage("WEBDEBUG_SMS_MENU_HISTORY_NAME"),
			"url" => "webdebug_sms_history.php?lang=".LANGUAGE_ID,
			"more_url" => array(),
			"title" => GetMessage("WEBDEBUG_SMS_MENU_HISTORY_DESC"),
			"icon" => "webdebug_sms_icon_17_history",
		),
	),
);
return $aMenu;
?>
