<?
if (sizeof($arResult["ORDER_PROP"]["USER_PROPS_Y"]) > 0)
{
	foreach($arResult["ORDER_PROP"]["USER_PROPS_Y"] as $k=>$arProps)
	{
		if ($arProps["CODE"] == "GREETING_TEXT")
		{
			$arResult["ORDER_PROP"]["USER_PROPS_Y"][$k]["VALUE"] = $_SESSION["ORDER_FIELDS"]["GREETING_TEXT"];
		}
	}
}

if (sizeof($arResult["ORDER_PROP"]["USER_PROPS_N"]) > 0)
{
	foreach($arResult["ORDER_PROP"]["USER_PROPS_N"] as $k=>$arProps)
	{
		if ($arProps["CODE"] == "GREETING_TEXT")
		{
			$arResult["ORDER_PROP"]["USER_PROPS_N"][$k]["VALUE"] = $_SESSION["ORDER_FIELDS"]["GREETING_TEXT"];
		}
	}
}
?>