<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Мои профили");
?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.profile", 
	".default", 
	array(
		"SEF_MODE" => "N",
		"PER_PAGE" => "20",
		"USE_AJAX_LOCATIONS" => "N",
		"SET_TITLE" => "N",
		"SEF_FOLDER" => "/personal/order/"
	),
	false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>