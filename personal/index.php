<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрационная информация");
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
	"bitrix:main.profile",
	"",
	Array(
		"USER_PROPERTY_NAME" => "",
		"SET_TITLE" => "N",
		"AJAX_MODE" => "N",
		"USER_PROPERTY" => array(),
		"SEND_INFO" => "N",
		"CHECK_RIGHTS" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N"
	),
false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>