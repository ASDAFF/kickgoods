<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="sl_wrp">
<div id="slideshow">

	<div id="slideshow-container">
		<ul id="slides">
<? foreach($arResult["ITEMS"] as $KEY=>$arItem): ?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	
	#print_array($arItem);
	?>
	
	<li style="background: url('<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>') no-repeat 50% 50% #0ddf79;" id="slide-<?=$KEY;?>" class="slide">
		<a id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="slide-link-through" href="<?=$arItem["PROPERTIES"]["URL"]["VALUE"];?>"></a>
	</li>
<? endforeach; ?>
		</ul>
	</div>
	<!-- #slideshow-container -->

	<ul id="slideshow-controls"></ul>
</div>
<!-- #slideshow -->

<div id="slide-prew-next">
	<a href="#" id="arb" class="slide-prew-next-left-button"></a>
	<a href="#" id="arb" class="slide-prew-next-right-button"></a>
</div>
</div>
<script>
	$(document).ready(function(){
		var timeout;
		$('.sl_wrp').hover(
			function() {
				$('#slide-prew-next').css('opacity', '1')
				/*$('.slide-prew-next-left-button').stop().animate({'left':'-40px'}, 1000);
				$('.slide-prew-next-right-button').stop().animate({'right':'-36px'}, 1000);*/
			},
			function() {
				timeout = setTimeout(
					function(){
						/*$('.slide-prew-next-left-button').stop().animate({'left':'-84px'}, 1000);
						$('.slide-prew-next-right-button').stop().animate({'right':'-80px'}, 1000);*/
						/*setTimeout(function(){*/$('#slide-prew-next').css('opacity', '0');/*}, 1000);*/
					}, 1500
				);
			}
		);

		$('#arb').hover(
			function() {
				clearTimeout(timeout);
			},
			function() {
				timeout = setTimeout(
					function(){
						/*$('.slide-prew-next-left-button').stop().animate({'left':'-84px'}, 1000);
						$('.slide-prew-next-right-button').stop().animate({'right':'-80px'}, 1000);*/
						/*setTimeout(function(){*/$('#slide-prew-next').css('opacity', '0');/*}, 1000);*/
					}, 1500
				);
			}
		);
	});
</script>