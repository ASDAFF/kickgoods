<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("CLASS_ID_BODY", "404-not-found");
$APPLICATION->SetPageProperty("CLASS_BODY", "template-404");

$APPLICATION->SetPageProperty("NOT_SHOW_TITLE_H1", "Y");
$APPLICATION->SetTitle("404 Page not found");
?><h1 id="pnf-title" class="page-title">404</h1>
<h2 id="pnf-tagline">Сервер не отвечает или такой страницы не существует. Вы можете <a href="/collections/all">продолжить покупки</a> или <a href="http://www.kickgoods.ru/pages/contacts">сообщить нам о поломке</a>, ну или, наконец, почитать <a href="/blogs/news">наш блог</a>.</h2><?
/*
$APPLICATION->IncludeComponent(
	"bitrix:main.map", 
	".default", 
	array(
		"LEVEL" => "3",
		"COL_NUM" => "2",
		"SHOW_DESCRIPTION" => "Y",
		"SET_TITLE" => "Y",
		"CACHE_TIME" => "7776000",
		"CACHE_TYPE" => "A"
	),
	false
);
*/

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>