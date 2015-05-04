<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!empty($arResult["ORDER"]))
{
	//echo "<pre>".print_r($arResult['ORDER'],1)."</pre>";
	?>
	<?
$dbBasketItems = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => $arResult['ORDER']['ID']
            ),
        false,
        false,
        array("ID", "CALLBACK_FUNC", "MODULE", 
              "PRODUCT_ID", "QUANTITY", "DELAY", 
              "CAN_BUY", "PRICE", "WEIGHT")
    );
	$arSKU=array();
	while($item=$dbBasketItems->GetNext()){
		$res=CIBlockElement::GetById($item['PRODUCT_ID']);
		if($e=$res->GetNext())
			$name=$e['NAME'];
		$item['NAME']=$name;
		$arSKU[]=$item;
	}

	//echo "<pre>".print_r($arSKU,1)."</pre>";
	?>
	<b><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></b><br /><br />
	<table class="sale_order_full_table">
		<tr>
			<td>
				<?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?>
				<br /><br />
				<?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?>
			</td>
		</tr>
	</table>
	<?
	if (!empty($arResult["PAY_SYSTEM"]))
	{
		?>
		<br /><br />

		<table class="sale_order_full_table">
			<tr>
				<td class="ps_logo">
					<div class="pay_name"><?=GetMessage("SOA_TEMPL_PAY")?></div>
					<?=CFile::ShowImage($arResult["PAY_SYSTEM"]["LOGOTIP"], 100, 100, "border=0", "", false);?>
					<div class="paysystem_name"><?= $arResult["PAY_SYSTEM"]["NAME"] ?></div><br>
				</td>
			</tr>
			<?
			if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
			{
				?>
				<tr>
					<td>
						<?
						if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
						{
							?>
							<script language="JavaScript">
								window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
							</script>
							<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
							<?
							if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE']))
							{
								?><br />
								<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
								<?
							}
						}
						else
						{
							if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
							{
								include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
							}
						}
						?>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
}
else
{
	?>
	<b><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></b><br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>
	<?
}
?>
<script type="text/javascript">
 
  var _gaq = _gaq || [];
	//Укажите ID своего аккаунта в G.A.
 _gaq.push(['_setAccount', 'UA-61727753-1']);
 _gaq.push(['_trackPageview']);
 
//Указываем информацию о транзакции
 _gaq.push(['_addTrans',
    '<?=$arResult["ORDER"]["ID"]?>',           // ID заказа - обязательное поле
    'kickgoods.ru',  // Название магазина или точки продажи
    '<?=$arResult["ORDER"]["PRICE"]?>',          // Общая сумма заказа - обязательное поле
    '',           // Налог
    '<?=$arResult["ORDER"]["PRICE_DELIVERY"]?>',              // Стоимость доставки
    '',       // Город
    '',     // Штат, область
    ''             // Страна
  ]);
 
// Указываем информацию о товарах
// Для каждого товара из корзины нужно указать такой блок:

<?foreach($arSKU as $item):?>
 _gaq.push(['_addItem',
    '<?=$arResult["ORDER"]["ID"]?>',           // ID заказа - обязательное поле (для проверки соответствия товара заказу)
    '<?=$item["PRODUCT_ID"]?>',           // артикул / уникальный идентификатор товара - обязательное поле (обязательно должен быть уникальным для каждого товара)
    '<?=$item["NAME"]?>',        // Название товара
    '',   // Категория или модификация
    '<?=$item["PRICE"]?>',          // стоимость товара - обязательное поле
    '<?=$item["QUANTITY"]?>'               // количество - обязательное поле
  ]);
<?endforeach;?>
//Отправляем данные на сервер Google Analytics

  _gaq.push(['_trackTrans']); 
(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
 

 
</script>
