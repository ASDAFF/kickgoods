<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

switch($act)
{
	case "ORDER_FIELDS":
	unset($_SESSION["ORDER_FIELDS"]);
	$_SESSION["ORDER_FIELDS"]["GREETING_TEXT"] = $_POST["ORDER_FIELDS_GREETING_TEXT"];
	break;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

?>