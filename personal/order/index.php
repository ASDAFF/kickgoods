<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Мои заказы");
?>
<? $APPLICATION->IncludeComponent("bitrix:menu", "content", Array(
	"ROOT_MENU_TYPE" => "content",	// Тип меню для первого уровня
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_TIME" => "7776000",	// Время кеширования (сек.)
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
		"MAX_LEVEL" => "1",	// Уровень вложенности меню
		"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
); ?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order", 
	".default", 
	array(
		"SEF_MODE" => "Y",
		"ORDERS_PER_PAGE" => "20",
		"PATH_TO_PAYMENT" => "/personal/order/payment.php",
		"PATH_TO_BASKET" => "/cart/",
		"SET_TITLE" => "N",
		"SAVE_IN_SESSION" => "N",
		"NAV_TEMPLATE" => "",
		"PROP_1" => array(
		),
		"PROP_2" => "",
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "7776000",
		"CACHE_GROUPS" => "Y",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"HISTORIC_STATUSES" => array(
			0 => "F",
		),
		"STATUS_COLOR_N" => "green",
		"STATUS_COLOR_F" => "gray",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red",
		"COMPONENT_TEMPLATE" => ".default",
		"SEF_FOLDER" => "/personal/order/",
		"SEF_URL_TEMPLATES" => array(
			"list" => "/",
			"detail" => "#ID#/",
			"cancel" => "#ID#/",
		)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>