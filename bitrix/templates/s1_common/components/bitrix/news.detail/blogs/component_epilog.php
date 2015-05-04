<?
$APPLICATION->SetPageProperty('og_url','http://'.SITE_SERVER_NAME.$arResult["DETAIL_PAGE_URL"]);
$APPLICATION->SetPageProperty('og_title',$arResult["NAME"]);
$APPLICATION->SetPageProperty('og_description',trim(htmlspecialchars(strip_tags($arResult["DETAIL_TEXT"]))));
if (!empty($arResult["DETAIL_PICTURE"]["SRC"]))
{
	$APPLICATION->SetPageProperty('og_image','http://'.SITE_SERVER_NAME.$arResult["DETAIL_PICTURE"]["SRC"]);
}
?>