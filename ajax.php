<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

switch($act)
{
	case "forms":

	$APPLICATION->IncludeComponent(
	"bitrix:form",
	".default",
	Array(
		"START_PAGE" => "new",
		"SHOW_LIST_PAGE" => "N",
		"SHOW_EDIT_PAGE" => "N",
		"SHOW_VIEW_PAGE" => "N",
		"SUCCESS_URL" => "",
		"WEB_FORM_ID" => "2",
		"RESULT_ID" => $_REQUEST[RESULT_ID],
		"SHOW_ANSWER_VALUE" => "N",
		"SHOW_ADDITIONAL" => "N",
		"SHOW_STATUS" => "N",
		"EDIT_ADDITIONAL" => "N",
		"EDIT_STATUS" => "N",
		"NOT_SHOW_FILTER" => array(0=>"",1=>"",),
		"NOT_SHOW_TABLE" => array(0=>"",1=>"",),
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"USE_EXTENDED_ERRORS" => "N",
		"SEF_MODE" => "N",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "7776000",
		"CHAIN_ITEM_TEXT" => "",
		"CHAIN_ITEM_LINK" => "",
		"AJAX_OPTION_ADDITIONAL" => "",
		"VARIABLE_ALIASES" => array("action"=>"action",)
	)
	);
	
	break;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php"); 

?>