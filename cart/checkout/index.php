<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.ajax", 
	"template1", 
	array(
		"PAY_FROM_ACCOUNT" => "N",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"COUNT_DELIVERY_TAX" => "N",
		"ALLOW_AUTO_REGISTER" => "Y",
		"SEND_NEW_USER_NOTIFY" => "Y",
		"DELIVERY_NO_AJAX" => "Y",
		"DELIVERY_NO_SESSION" => "N",
		"TEMPLATE_LOCATION" => "popup",
		"DELIVERY_TO_PAYSYSTEM" => "d2p",
		"USE_PREPAYMENT" => "N",
		"ALLOW_NEW_PROFILE" => "Y",
		"SHOW_PAYMENT_SERVICES_NAMES" => "Y",
		"SHOW_STORES_IMAGES" => "N",
		"PATH_TO_BASKET" => "/cart/",
		"PATH_TO_PERSONAL" => "/personal/",
		"PATH_TO_PAYMENT" => "/personal/order/payment.php",
		"PATH_TO_AUTH" => "/personal/",
		"SET_TITLE" => "N",
		"DISABLE_BASKET_REDIRECT" => "N",
		"PRODUCT_COLUMNS" => array(
			0 => "PREVIEW_PICTURE",
			1 => "PROPS",
		),
		"PROP_1" => array(
		),
		"COMPONENT_TEMPLATE" => "template1"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>