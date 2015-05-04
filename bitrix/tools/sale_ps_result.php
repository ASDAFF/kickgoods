<?
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define("DisableEventsCheck", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

#file_put_contents($_SERVER['DOCUMENT_ROOT'].'/1.php',print_r($_SERVER,true).print_r($_REQUEST,true),FILE_APPEND);

if (isset($_REQUEST["orderNumber"]) && intval($_REQUEST["orderNumber"]) > 0)
{
	if (CModule::IncludeModule("sale"))
	{
		$arOrder = CSaleOrder::GetByID(intval($_REQUEST["orderNumber"]));

		if ($arOrder)
		{
			$personTypeId = $arOrder["PERSON_TYPE_ID"];
			$paySystemId = $arOrder["PAY_SYSTEM_ID"];

			$APPLICATION->IncludeComponent(
				"bitrix:sale.order.payment.receive",
				"",
				array(
					"PAY_SYSTEM_ID" => $paySystemId,
					"PERSON_TYPE_ID" => $personTypeId
				),
			false
			);
		}
	}
}

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
