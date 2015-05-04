<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
//==================== edost НАЧАЛО
  if (isset($arResult['edost']['javascript'])) echo $arResult['edost']['javascript'];
  if (isset($arResult['edost']['warning'])) echo $arResult['edost']['warning'].'<br>';
//==================== edost КОНЕЦ
?>


<script type="text/javascript">
	function fShowStore(id, showImages, formWidth, siteId)
	{
		var strUrl = '<?=$templateFolder?>' + '/map.php';
		var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;

		var storeForm = new BX.CDialog({
					'title': '<?=GetMessage('SOA_ORDER_GIVE')?>',
					head: '',
					'content_url': strUrl,
					'content_post': strUrlPost,
					'width': formWidth,
					'height':450,
					'resizable':false,
					'draggable':false
				});

		var button = [
				{
					title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
					id: 'crmOk',
					'action': function ()
					{
						GetBuyerStore();
						BX.WindowManager.Get().Close();
					}
				},
				BX.CDialog.btnCancel
			];
		storeForm.ClearButtons();
		storeForm.SetButtons(button);
		storeForm.Show();
	}

	function GetBuyerStore()
	{
		BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
		//BX('ORDER_DESCRIPTION').value = '<?=GetMessage("SOA_ORDER_GIVE_TITLE")?>: '+BX('POPUP_STORE_NAME').value;
		BX('store_desc').innerHTML = BX('POPUP_STORE_NAME').value;
		BX.show(BX('select_store'));
	}

	function showExtraParamsDialog(deliveryId)
	{
		var strUrl = '<?=$templateFolder?>' + '/delivery_extra_params.php';
		var formName = 'extra_params_form';
		var strUrlPost = 'deliveryId=' + deliveryId + '&formName=' + formName;

		if(window.BX.SaleDeliveryExtraParams)
		{
			for(var i in window.BX.SaleDeliveryExtraParams)
			{
				strUrlPost += '&'+encodeURI(i)+'='+encodeURI(window.BX.SaleDeliveryExtraParams[i]);
			}
		}

		var paramsDialog = new BX.CDialog({
			'title': '<?=GetMessage('SOA_ORDER_DELIVERY_EXTRA_PARAMS')?>',
			head: '',
			'content_url': strUrl,
			'content_post': strUrlPost,
			'width': 500,
			'height':200,
			'resizable':true,
			'draggable':false
		});

		var button = [
			{
				title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
				id: 'saleDeliveryExtraParamsOk',
				'action': function ()
				{
					insertParamsToForm(deliveryId, formName);
					BX.WindowManager.Get().Close();
				}
			},
			BX.CDialog.btnCancel
		];

		paramsDialog.ClearButtons();
		paramsDialog.SetButtons(button);
		//paramsDialog.adjustSizeEx();
		paramsDialog.Show();
	}

	function insertParamsToForm(deliveryId, paramsFormName)
	{
		var orderForm = BX("ORDER_FORM"),
			paramsForm = BX(paramsFormName);
			wrapDivId = deliveryId + "_extra_params";

		var wrapDiv = BX(wrapDivId);
		window.BX.SaleDeliveryExtraParams = {};

		if(wrapDiv)
			wrapDiv.parentNode.removeChild(wrapDiv);

		wrapDiv = BX.create('div', {props: { id: wrapDivId}});

		for(var i = paramsForm.elements.length-1; i >= 0; i--)
		{
			var input = BX.create('input', {
				props: {
					type: 'hidden',
					name: 'DELIVERY_EXTRA['+deliveryId+']['+paramsForm.elements[i].name+']',
					value: paramsForm.elements[i].value
					}
				}
			);

			window.BX.SaleDeliveryExtraParams[paramsForm.elements[i].name] = paramsForm.elements[i].value;

			wrapDiv.appendChild(input);
		}

		orderForm.appendChild(wrapDiv);

		BX.onCustomEvent('onSaleDeliveryGetExtraParams',[window.BX.SaleDeliveryExtraParams]);
	}
</script>

<style>
	#EdostPickPointRef { font-size: 13px; }
	table.edost_office_table { color: #000; font-size: 13px; }
	table.edost_office_table select { height: 25px; padding: 2px !important; }
	.edost_title { font-size: 14px; cursor: default; }
	.edost_description { line-height: normal !important; overflow: hidden; }
	.bx_order_make .bx_logotype span { width: 95px; height: 55px; }
	.bx_element input[type="radio"]:checked + label .bx_logotype, .bx_element label.selected .bx_logotype { border: 4px solid #00AAFF !important; padding: 1px; }
	.bx_order_make .bx_logotype { border: 4px solid rgba(255, 255, 255, 0.1); padding: 1px; }
	.bx_order_make .bx_logotype.active, .bx_order_make .bx_logotype:hover { border: 4px solid #AAE2FF; padding: 1px; }
</style>

<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" />
<div class="bx_section">
	<?
	if(!empty($arResult["DELIVERY"]))
	{
		$width = ($arParams["SHOW_STORES_IMAGES"] == "Y") ? 850 : 700;
		?>
		<h4><?=GetMessage("SOA_TEMPL_DELIVERY")?></h4>
		<?
		uasort($arResult["DELIVERY"], 'cmpBySort'); // resort delivery arrays according to SORT value

		foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
		{
			if ($delivery_id !== 0 && intval($delivery_id) <= 0)
			{
				foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
				{
					if ($delivery_id != 'edost' && $arProfile["DESCRIPTION"] == '' && $arDelivery["DESCRIPTION"] == '') $top = 24;
					else if ($delivery_id == 'edost' && !(isset($arProfile['office']) || (isset($arProfile["DESCRIPTION"]) && $arProfile["DESCRIPTION"] != ''))) $top = 24;
					else if (isset($arProfile["DESCRIPTION"]) && (strpos($arProfile["DESCRIPTION"], '<br>') > 0 || strlen($arProfile["DESCRIPTION"]) > 120)) $top = 0;
					else $top = 12;
					$top = 'padding-top: '.$top.'px;';

					?>
					<div class="bx_block w100 vertical">
						<div class="bx_element">

							<input
								type="radio"
								id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>"
								name="<?=htmlspecialcharsbx($arProfile["FIELD_NAME"])?>"
								value="<?=$delivery_id.":".$profile_id;?>"
								<?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?>
								onclick="submitForm();"
								/>

								<?
								if ($delivery_id == 'edost') $deliveryImgURL = '/bitrix/images/delivery_edost_img/big/'.ceil($profile_id / 2).'.gif';
								else if (count($arDelivery["LOGOTIP"]) > 0) {
									$arFileTmp = CFile::ResizeImageGet(
										$arDelivery["LOGOTIP"]["ID"],
										array("width" => "95", "height" =>"55"),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);
									$deliveryImgURL = $arFileTmp["src"];
								}
								else $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";

								if($arDelivery["ISNEEDEXTRAINFO"] == "Y")
									$extraParams = "showExtraParamsDialog('".$delivery_id.":".$profile_id."');";
								else
									$extraParams = "";
								?>

								<label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">
								<div class="bx_logotype" onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;<?=$extraParams?>submitForm();">
									<span style='background-image:url(<?=$deliveryImgURL?>);'></span>
								</div>
								</label>

								<div class="bx_description">

									<label for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">
									<strong class="edost_title" style="<?=$top?>" onclick="BX('ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>').checked=true;<?=$extraParams?>submitForm();">
										<?=('<b>'.($delivery_id == 'edost' ? '' : htmlspecialcharsbx($arDelivery["TITLE"]).' - ') . str_replace('<br><font color="#FF0000">Ошибка: У товаров не задан вес!</font>','',$arProfile["TITLE"]) . '</b>' . ($arProfile['day'] != '' ? ', '.$arProfile['day'] : '') . (isset($arProfile['price']) && $arProfile['price'] != '' ? ' - <b>'.$arProfile['price'].'</b>' : ''))?>
									</strong>
									</label>

									<? if ($delivery_id != 'edost') { ?>
									<span class="bx_result_price"><!-- click on this should not cause form submit -->
										<?
											if($arProfile["CHECKED"] == "Y" && doubleval($arResult["DELIVERY_PRICE"]) > 0):
										?>
											<div><?=GetMessage("SALE_DELIV_PRICE")?>:&nbsp;<b><?=$arResult["DELIVERY_PRICE_FORMATED"]?></b></div>
										<?
											if ((isset($arResult["PACKS_COUNT"]) && $arResult["PACKS_COUNT"]) > 1):
												echo GetMessage('SALE_PACKS_COUNT').': <b>'.$arResult["PACKS_COUNT"].'</b>';
											endif;

										else:
											$APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
												"NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
												"DELIVERY" => $delivery_id,
												"PROFILE" => $profile_id,
												"ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
												"ORDER_PRICE" => $arResult["ORDER_PRICE"],
												"LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
												"LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
												"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
												"ITEMS" => $arResult["BASKET_ITEMS"],
												"EXTRA_PARAMS_CALLBACK" => $extraParams
											), null, array('HIDE_ICONS' => 'Y'));
										endif;
										?>
									</span>
									<? } ?>

						            <?=(isset($arProfile['office']) ? $arProfile['office'] : '')?>

									<p class="edost_description">
										<?if (strlen($arProfile["DESCRIPTION"]) > 0):?>
											<?=nl2br($arProfile["DESCRIPTION"])?>
										<?else:?>
											<?=($delivery_id != 'edost' ? nl2br($arDelivery["DESCRIPTION"]) : '')?>
										<?endif;?>
									</p>
								</div>
						</div>
					</div>
					<?
				} // endforeach
			}
			else // stores and courier
			{
				if ($arDelivery["DESCRIPTION"] == '') $top = 24;
				else if (strlen($arDelivery["DESCRIPTION"]) > 120) $top = 0;
				else $top = 12;
				$top = 'padding-top: '.$top.'px;';

				if (count($arDelivery["STORE"]) > 0)
					$clickHandler = "onClick = \"fShowStore('".$arDelivery["ID"]."','".$arParams["SHOW_STORES_IMAGES"]."','".$width."','".SITE_ID."')\";";
				else
					$clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;submitForm();\"";
				?>
					<div class="bx_block w100 vertical">

						<div class="bx_element">

							<input type="radio"
								id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
								name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
								value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?>
								onclick="submitForm();"
								/>

							<label for="ID_DELIVERY_ID_<?=$arDelivery["ID"]?>" <?=$clickHandler?>>

								<?
								if (count($arDelivery["LOGOTIP"]) > 0):

									$arFileTmp = CFile::ResizeImageGet(
										$arDelivery["LOGOTIP"]["ID"],
										array("width" => "95", "height" =>"55"),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);

									$deliveryImgURL = $arFileTmp["src"];
								else:
									$deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
								endif;
								?>

								<div class="bx_logotype"><span style='background-image:url(<?=$deliveryImgURL?>);'></span></div>

								<div class="bx_description">
									<strong class="edost_title" style="<?=$top?>">
										<?= htmlspecialcharsbx($arDelivery["NAME"]) ?>
									</strong>
									<span class="bx_result_price">
										<?
										if (strlen($arDelivery["PERIOD_TEXT"])>0)
										{
											echo $arDelivery["PERIOD_TEXT"];
											?><br /><?
										}
										?>
										<?=GetMessage("SALE_DELIV_PRICE");?>: <b><?=$arDelivery["PRICE_FORMATED"]?></b><br />
									</span>
									<p class="edost_description">
										<?
										if (strlen($arDelivery["DESCRIPTION"])>0)
											echo $arDelivery["DESCRIPTION"]."<br />";

										if (count($arDelivery["STORE"]) > 0):
										?>
											<span id="select_store"<?if(strlen($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"]) <= 0) echo " style=\"display:none;\"";?>>
												<span class="select_store"><?=GetMessage('SOA_ORDER_GIVE_TITLE');?>: </span>
												<span class="ora-store" id="store_desc"><?=htmlspecialcharsbx($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"])?></span>
											</span>
										<?
									endif;
									?>
									</p>
								</div>

							</label>

						<div class="clear"></div>
					</div>
				</div>
				<?
			}
		}
	}
?>
<div class="clear"></div>
</div>


<?
//==================== edost НАЧАЛО
  if (isset($arResult['edost']['javascript2'])) echo $arResult['edost']['javascript2'];
//==================== edost КОНЕЦ
?>