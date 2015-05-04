<?

$arSort = array
(
	"DATE_ACTIVE_FROM"=>"DESC",
	"SORT"=>"DESC",
);

$arSelect = array("ID","NAME","DETAIL_PAGE_URL");

$arFilter = array
(
	"IBLOCK_ID" => $arResult["IBLOCK_ID"],
	"SECTION_CODE" => "news",
	"ACTIVE" => "Y",
	"ACTIVE_DATE" => "Y",
	"IBLOCK_ACTIVE" => "Y",
	"SECTION_ACTIVE" => "Y",
	"SECTION_GLOBAL_ACTIVE" => "Y",
	"CHECK_PERMISSIONS" => "Y"
);

$arNavParams = array
(
	"nPageSize" => 1,
	"nElementID" => $arResult["ID"],
);

if ($result = CIBlockElement::GetList($arSort,$arFilter,false,$arNavParams,$arSelect))
{
	if ($result->SelectedRowsCount() > 0)
	{
		$arItems = array();
		
		while($arItem = $result->GetNext())
		{
			$arItems[] = $arItem;
		}

		$arItemsQNT = sizeof($arItems);

		if ($arItemsQNT == 3)
		{
		   $arResult["TOLEFT"] = array("NAME"=>$arItems[0]["NAME"],"DETAIL_PAGE_URL"=>$arItems[0]["DETAIL_PAGE_URL"]);
		   $arResult["TORIGHT"] = array("NAME"=>$arItems[2]["NAME"],"DETAIL_PAGE_URL"=>$arItems[2]["DETAIL_PAGE_URL"]);
		}
		else if ($arItemsQNT == 2)
		{
			if ($arItems[0]["ID"] != $arResult["ID"])
			{
				$arResult["TOLEFT"] = array("NAME"=>$arItems[0]["NAME"],"DETAIL_PAGE_URL"=>$arItems[0]["DETAIL_PAGE_URL"]);
			}
			else
			{
				$arResult["TORIGHT"] = array("NAME"=>$arItems[1]["NAME"],"DETAIL_PAGE_URL"=>$arItems[1]["DETAIL_PAGE_URL"]);
			}
		}
	}
}

###

$this->__component->SetResultCacheKeys(array('DETAIL_PICTURE','DETAIL_TEXT','DETAIL_PAGE_URL'));

?> 