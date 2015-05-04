<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("CLASS_BODY", "template-article");
$APPLICATION->SetTitle("");
?><?$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"blogs",
	Array(
		"IBLOCK_TYPE" => "blogs",
		"IBLOCK_ID" => "7",
		"ELEMENT_ID" => "",
		"ELEMENT_CODE" => $_REQUEST["ELEMENT_CODE"],
		"CHECK_DATES" => "Y",
		"FIELD_CODE" => array(0=>"",1=>"",),
		"PROPERTY_CODE" => array(0=>"",1=>"",),
		"IBLOCK_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "7776000",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_STATUS_404" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_ELEMENT_CHAIN" => "N",
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"USE_PERMISSIONS" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"USE_SHARE" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Страница",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?>

<?$APPLICATION->IncludeComponent(
	"bitrix:news.line", 
	"blogs", 
	array(
		"IBLOCK_TYPE" => "blogs",
		"IBLOCKS" => array(
			0 => "7",
		),
		"NEWS_COUNT" => "6",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "DESC",
		"DETAIL_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "300",
		"CACHE_GROUPS" => "Y",
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"HEADER_TEXT" => ""
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>