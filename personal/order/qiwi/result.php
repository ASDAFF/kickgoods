<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("QIWI");
?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.payment.receive",
	"",
	Array(
		"PAY_SYSTEM_ID" => "2",
		"PERSON_TYPE_ID" => "1"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>