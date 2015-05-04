<?
define("PAGE_TYPE","catalog");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("CLASS_ID_BODY", "products");
$APPLICATION->SetPageProperty("NOT_SHOW_TITLE_H1", "Y");
$APPLICATION->SetPageProperty("CLASS_BODY", "template-collection");
$APPLICATION->SetTitle("Apple Watch");
?><div id="template-collection">
<ul class="clearfix" id="collection-header">
		<li id="collection-title">
		<h1>Apple Watch</h1>		</li>
	</ul>
</div>
<?
$arFilter = array();
if (!empty($_REQUEST["VENDORS_CODE"]))
{
	$APPLICATION->SetPageProperty("CLASS_ID_BODY", "");
	$arFilter = array("PROPERTY_VENDOR_ID.CODE" => $_REQUEST["VENDORS_CODE"]);
}
?>
<div class="aw">
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"template1",
	Array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "3",
		"SECTION_ID" => "",
		"SECTION_CODE" => "apple-watch-all",
		"SECTION_USER_FIELDS" => array("",""),
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "desc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "arFilter",
		"INCLUDE_SUBSECTIONS" => "A",
		"SHOW_ALL_WO_SECTION" => "N",
		"HIDE_NOT_AVAILABLE" => "N",
		"PAGE_ELEMENT_COUNT" => "1000",
		"LINE_ELEMENT_COUNT" => "4",
		"PROPERTY_CODE" => array("","",""),
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
		"PRICE_CODE" => array("Розничная"),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "N",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/cart/",
		"USE_PRODUCT_QUANTITY" => "N",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(),
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
		"OFFERS_FIELD_CODE" => array("",""),
		"OFFERS_PROPERTY_CODE" => array("TYPE","COLOR","BRACELET","SIZE",""),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "desc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "desc",
		"PRODUCT_DISPLAY_MODE" => "N",
		"ADD_PICT_PROP" => "-",
		"LABEL_PROP" => "-",
		"MESS_BTN_COMPARE" => "Сравнить",
		"OFFERS_CART_PROPERTIES" => array("TYPE","COLOR","BRACELET","SIZE"),
		"CURRENCY_ID" => "RUB",
		"AJAX_OPTION_ADDITIONAL" => "",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"COMPONENT_TEMPLATE" => "template1"
	)
);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>