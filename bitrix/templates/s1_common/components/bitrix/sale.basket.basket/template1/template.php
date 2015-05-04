<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixBasketComponent $component */

#print_array($arResult);

$curPage = $APPLICATION->GetCurPage().'?'.$arParams["ACTION_VARIABLE"].'=';
$arUrls = array(
	"delete" => $curPage."delete&id=#ID#",
	"delay" => $curPage."delay&id=#ID#",
	"add" => $curPage."add&id=#ID#",
);
unset($curPage);

$arBasketJSParams = array(
	'SALE_DELETE' => GetMessage("SALE_DELETE"),
	'SALE_DELAY' => GetMessage("SALE_DELAY"),
	'SALE_TYPE' => GetMessage("SALE_TYPE"),
	'TEMPLATE_FOLDER' => $templateFolder,
	'DELETE_URL' => $arUrls["delete"],
	'DELAY_URL' => $arUrls["delay"],
	'ADD_URL' => $arUrls["add"]
);
?>
<script type="text/javascript">
	var basketJSParams = <?=CUtil::PhpToJSObject($arBasketJSParams);?>
</script>
<?
$APPLICATION->AddHeadScript($templateFolder."/script.js");



$normalCount = count($arResult["ITEMS"]["AnDelCanBuy"]);
$normalHidden = ($normalCount == 0) ? 'style="display:none;"' : '';

$delayCount = count($arResult["ITEMS"]["DelDelCanBuy"]);
$delayHidden = ($delayCount == 0) ? 'style="display:none;"' : '';

$subscribeCount = count($arResult["ITEMS"]["ProdSubscribe"]);
$subscribeHidden = ($subscribeCount == 0) ? 'style="display:none;"' : '';

$naCount = count($arResult["ITEMS"]["nAnCanBuy"]);
$naHidden = ($naCount == 0) ? 'style="display:none;"' : '';



$CATALOG_SERVICES = array();
$BASKET_SERVICES = array();

if (defined("CATALOG_SERVICES_ID") && $normalCount > 0)
{
	$CATALOG_SERVICES_ID = explode(",",CATALOG_SERVICES_ID);
	
	if (sizeof($CATALOG_SERVICES_ID) > 0)
	{	
		if ($result_catalog = CIBlockElement::GetList(array("ID"=>"ASC"),array("IBLOCK_ID"=>6,"ID"=>$CATALOG_SERVICES_ID,"ACTIVE"=>"Y","IBLOCK_ACTIVE"=>"Y"),false,array(),array("ID","DETAIL_PAGE_URL","CATALOG_GROUP_1")))
		{
			if ($result_catalog->SelectedRowsCount())
			{
				while ($arCatalog = $result_catalog->GetNext())
				{
					#print_array($arCatalog);
					
					if ($arCatalog["CATALOG_CAN_BUY_1"] == "Y")
					{
						$CATALOG_SERVICES[$arCatalog["ID"]] = $arCatalog;
					}
				}
				
				if (sizeof($arResult["GRID"]["ROWS"]) > 0)
				{
					foreach($arResult["GRID"]["ROWS"] as $k=>$arItem)
					{
						if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y" && in_array($arItem["PRODUCT_ID"],$CATALOG_SERVICES_ID))
						{
							$BASKET_SERVICES[$arItem["PRODUCT_ID"]]["ID"] = $arItem["ID"];
							$BASKET_SERVICES[$arItem["PRODUCT_ID"]]["CHECKED"] = " checked";
	
							unset($arResult["GRID"]["ROWS"][$k]);
							$normalCount--;
						}
					}
				}
			}
		}
	}
}



if ($normalCount == 0)
{
	?>
	<div id="empty-cart">
		<h1>А корзина-то пустая!</h1>
		<h2>Вы можете продолжить просмотр товаров <a href="/collections/all/">тут</a>.</h2>
	</div><!-- #empty-cart -->
	<?
}
else
{
	if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
	{
		?>
		<div id="warning_message">
			<?
			if (!empty($arResult["WARNING_MESSAGE"]) && is_array($arResult["WARNING_MESSAGE"]))
			{
				foreach ($arResult["WARNING_MESSAGE"] as $v)
					ShowError($v);
			}
			?>
		</div>

		<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form">
			<div id="basket_form_container">
				<div class="bx_ordercart">
					<?
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
					#include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delayed.php");
					#include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribed.php");
					#include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_not_available.php");
					?>
				</div>
			</div>
			<input type="hidden" name="BasketOrder" value="BasketOrder" />
			<!-- <input type="hidden" name="ajax_post" id="ajax_post" value="Y"> -->
		</form>
		<?
	}
	else
	{
		ShowError($arResult["ERROR_MESSAGE"]);
	}
}

?>