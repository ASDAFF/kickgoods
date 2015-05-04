<?
define("PAGE_TYPE","index");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("og_image", "/bitrix/templates/s1_common/images/logo.png");
$APPLICATION->SetPageProperty("CLASS_BODY", "template-index");
$APPLICATION->SetPageProperty("NOT_SHOW_TITLE_H1", "Y");
$APPLICATION->SetPageProperty("keywords", "Товары с Кикстартера, Smudge Guard, интересные товары, что подарить на др, что подарить на день рождения, стилусы, подарок художнику, цифровая живопись, купить подарок, Кикстартер в России, Sensu Brush, Mio Alpha, часы пульсометр, корпоративные подарки, умные часы, роботы, купить робота, аксессуары для iPhone, рисование на iPad, что подарить на НГ, что подарить на Новый год, подрок девушке, подарочная упаковка, магазин с подарочной упаковкой, интересные штуки, pebble, glif, cosmonaut, creative stylus, styluses moscow, магазин Кикстартера, новинки технологий, USB джойстик, USB SEGA, USB dandy, USB dendy");
$APPLICATION->SetPageProperty("description", "Товары, придуманные на Кикстартере. Тут можно купить Sensu Brush, Galileo, Adonit Jot, Slope, Swivl, Pebble и другие классные устройства.");
$APPLICATION->SetTitle("Кickgoods — Интернет-магазин изобретений с Кикстартера");
?><?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"slider",
	Array(
		"IBLOCK_TYPE" => "other",
		"IBLOCK_ID" => "2",
		"NEWS_COUNT" => "1000",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_BY2" => "ID",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(0=>"",1=>"",),
		"PROPERTY_CODE" => array(0=>"URL",1=>"",),
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
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "N",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "N",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?> 
<br>
<h2 style="margin-left:-35px;" id="smm">Самые интересные</h2>
<?/*
$arFilter = array();
$arFilter = array("IBLOCK_SECTION_ID" => "13");
*/?> <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"main_page", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "3",
		"SECTION_ID" => "13",
		"SECTION_CODE" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "desc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "arFilter",
		"INCLUDE_SUBSECTIONS" => "A",
		"SHOW_ALL_WO_SECTION" => "N",
		"HIDE_NOT_AVAILABLE" => "N",
		"PAGE_ELEMENT_COUNT" => "20",
		"LINE_ELEMENT_COUNT" => "4",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "0",
		"TEMPLATE_THEME" => "blue",
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_CLOSE_POPUP" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "7776000",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "N",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "N",
		"META_DESCRIPTION" => "-",
		"ADD_SECTIONS_CHAIN" => "N",
		"SET_STATUS_404" => "N",
		"CACHE_FILTER" => "N",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRICE_CODE" => array(
			0 => "Розничная",
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "N",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/cart/",
		"USE_PRODUCT_QUANTITY" => "N",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"ADD_TO_BASKET_ACTION" => "ADD",
		"DISPLAY_COMPARE" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "7776000",
		"PAGER_SHOW_ALL" => "N",
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "TYPE",
			1 => "COLOR",
			2 => "BRACELET",
			3 => "SIZE",
			4 => "CASE",
			5 => "WRIST",
			6 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "desc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "desc",
		"PRODUCT_DISPLAY_MODE" => "N",
		"ADD_PICT_PROP" => "-",
		"LABEL_PROP" => "-",
		"MESS_BTN_COMPARE" => "Сравнить",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "TYPE",
			1 => "COLOR",
			2 => "BRACELET",
			3 => "SIZE",
			4 => "CASE",
			5 => "WRIST",
		),
		"CURRENCY_ID" => "RUB",
		"AJAX_OPTION_ADDITIONAL" => "",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity"
	),
	false
);?>

<?$APPLICATION->IncludeComponent(
	"bitrix:main.include", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "inc",
		"EDIT_TEMPLATE" => "",
		"PATH" => SITE_TEMPLATE_PATH."/includes/vk_index.php"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>