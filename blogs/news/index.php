<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("CLASS_BODY", "template-blog");
$APPLICATION->SetTitle("Блог");
?><?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"blogs",
	Array(
		"IBLOCK_TYPE" => "blogs",
		"IBLOCK_ID" => "7",
		"NEWS_COUNT" => "10",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "DESC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(0=>"",1=>"",),
		"PROPERTY_CODE" => array(0=>"",1=>"",),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "7776000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "news",
		"INCLUDE_SUBSECTIONS" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"PAGER_TEMPLATE" => "blogs",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "7776000",
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