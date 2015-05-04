<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта сайта");
?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.map", 
	".default", 
	array(
		"LEVEL" => "3",
		"COL_NUM" => "1",
		"SHOW_DESCRIPTION" => "N",
		"SET_TITLE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "7776000"
	),
	false
);?>
<br />
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>