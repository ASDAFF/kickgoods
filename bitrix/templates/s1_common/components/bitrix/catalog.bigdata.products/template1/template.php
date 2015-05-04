<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$frame = $this->createFrame()->begin("");

$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
);

$injectId = 'bigdata_recommeded_products_'.rand();

?>

<script type="application/javascript">
	BX.cookie_prefix = '<?=CUtil::JSEscape(COption::GetOptionString("main", "cookie_name", "BITRIX_SM"))?>';
	BX.cookie_domain = '<?=$APPLICATION->GetCookieDomain()?>';
	BX.current_server_time = '<?=time()?>';

	function bx_rcm_recommndation_event_attaching(rcm_items_cont)
	{

		var detailLinks = BX.findChildren(rcm_items_cont, {'className':'bx_rcm_view_link'}, true);

		if (detailLinks)
		{
			for (i in detailLinks)
			{
				BX.bind(detailLinks[i], 'click', function(e){
					window.JCCatalogBigdataProducts.prototype.RememberRecommendation(
						BX(this),
						BX(this).getAttribute('data-product-id')
					);
				});
			}
		}
	}

	BX.ready(function(){
		bx_rcm_recommndation_event_attaching(BX('<?=$injectId?>_items'));
	});

</script>

<?

if (isset($arResult['REQUEST_ITEMS']))
{
	CJSCore::Init(array('ajax'));

	// component parameters
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedParameters = $signer->sign(
		base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
		'bx.bd.products.recommendation'
	);
	$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');

	?>

	<span id="<?=$injectId?>" class="bigdata_recommended_products_container"></span>

	<script type="application/javascript">

		BX.ready(function(){

			var params = <?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>;
			var url = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php';
			var data = BX.ajax.prepareData(params);

			if (data)
			{
				url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
				data = '';
			}

			var onready = function(response) {

				if (!response.items)
				{
					response.items = [];
				}
				BX.ajax({
					url: '/bitrix/components/bitrix/catalog.bigdata.products/ajax.php?'+BX.ajax.prepareData({'AJAX_ITEMS': response.items, 'RID': response.id}),
					method: 'POST',
					data: {'parameters':'<?=CUtil::JSEscape($signedParameters)?>', 'template': '<?=CUtil::JSEscape($signedTemplate)?>', 'rcm': 'yes'},
					dataType: 'html',
					processData: false,
					start: true,
					onsuccess: function (html) {
						var ob = BX.processHTML(html);

						// inject
						BX('<?=$injectId?>').innerHTML = ob.HTML;
						BX.ajax.processScripts(ob.SCRIPT);
					}
				});
			};

			BX.ajax({
				'method': 'GET',
				'dataType': 'json',
				'url': url,
				'timeout': 3,
				'onsuccess': onready,
				'onfailure': onready
			});
		});
	</script>

	<?
	$frame->end();
	return;
}



if (!empty($arResult['ITEMS']))
{
	if ($USER->GetID() == 1)
	{
		#print_array($arResult['ITEMS']);
	}
	?>
	
	<? if ($arParams["PAGE_TYPE"] == "index") { ?>
	<div class="clearfix" id="columns-wrap">

		<div id="products-column" class="full-width">
			<div id="products-column-header" class="clearfix">
				<h2>Самые интересные</h2>
			</div>
			<!-- #products-column-header -->

			<ul id="fp-product-list" class="clearfix">
	<? } else { ?>
	<div class="related-products-container">
		<h2 class="related-products-title smooth">Похожие товары</h2>
			<ul class="related-products-list clearfix">
	<? } ?>
	
	<?
	$CATALOG_SERVICES_ID = array();
	if (defined("CATALOG_SERVICES_ID"))
	{
		$CATALOG_SERVICES_ID = explode(",",CATALOG_SERVICES_ID);
	}

	$ItemCount = 0;
	foreach($arResult['ITEMS'] as $KEY=>$arItem)
	{
		$ItemShow = true;

		if (sizeof($arItem["OFFERS"]) > 0)
		{
			foreach($arItem["OFFERS"] as $arOffer)
			{
				if (in_array($arOffer['ID'],$CATALOG_SERVICES_ID))
				{
					$ItemShow = false;
				}
			}
		}

		if (in_array($arItem['ID'],$CATALOG_SERVICES_ID) or $arParams['ID'] == $arItem['ID'])
		{
			$ItemShow = false;
		}
		
		if ($ItemShow)
		{
			$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"],"ELEMENT_EDIT");
			$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"],"ELEMENT_DELETE");
			$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
			$strMainID = $this->GetEditAreaId($arItem['ID']);

			$productTitle =
			(
				isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
				? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
				: $arItem['NAME']
			);

			$imgTitle =
			(
				isset($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
				? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
				: $arItem['NAME']
			);
			?>
			<li class="four-per-row clearfix" id="<?=$strMainID;?>">
				<div class="coll-image-wrap">  
					<a href="<?=$arItem["DETAIL_PAGE_URL"];?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" alt="<?=$imgTitle;?>" /></a>
				</div>
				<!-- .coll-image-wrap -->

				<div class="coll-prod-caption">
					<div class="coll-prod-meta no-medallion">
						<a href="<?=$arItem["DETAIL_PAGE_URL"];?>" class="coll-prod-title"><?=$productTitle;?></a>
						<? if (!empty($arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']))
						{
							?>
							<p class="coll-prod-price accent-text"><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];?></p>
							<?
						}
						?>
					</div>
					<!-- .coll-prod-meta -->
				</div>
				<!-- .coll-prod-caption -->
			</li>
			<?
			$ItemCount++;
		}
		
		if ($ItemCount == ($arParams["PAGE_ELEMENT_COUNT"]-4))
		{
			break;
		}
	}
	?>
				
	<? if ($arParams["PAGE_TYPE"] == "index") { ?>
			</ul>

		</div>
		<!-- #products-column -->

	</div>
	<!-- #columns-wrap -->
	<? } else { ?>
		</ul>
	</div>
	<? } ?>

	<?
}

$frame->end();

?>