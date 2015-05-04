<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket", 
	"template1", 
	array(
		"COLUMNS_LIST" => array(
			0 => "NAME",
			1 => "PROPS",
			2 => "DELETE",
			3 => "PRICE",
			4 => "QUANTITY",
			5 => "SUM",
		),
		"PATH_TO_ORDER" => "/cart/checkout/",
		"HIDE_COUPON" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"USE_PREPAYMENT" => "N",
		"QUANTITY_FLOAT" => "N",
		"SET_TITLE" => "N",
		"ACTION_VARIABLE" => "action",
		"OFFERS_PROPS" => array(
			0 => "TYPE",
			1 => "COLOR",
			2 => "BRACELET",
			3 => "SIZE",
			4 => "CASE",
			5 => "WRIST",
		)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>