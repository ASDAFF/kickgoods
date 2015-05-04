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

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<li class="blog-pagination">
	<div class="pagination">

	<? if ($arResult["NavPageNomer"] > 1): ?>

		<? if($arResult["bSavePage"]): ?>
			<a class="active" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_prev")?></a>
		<? else: ?>
			<? if ($arResult["NavPageNomer"] > 2): ?>
				<a class="active" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_prev")?></a>
			<? else: ?>
				<a class="active" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("nav_prev")?></a>
			<? endif; ?>
		<? endif; ?>
	<? else: ?>
		<a><?=GetMessage("nav_prev")?></a>
	<? endif ?>

	<? while($arResult["nStartPage"] <= $arResult["nEndPage"]): ?>
		<? if ($arResult["nStartPage"] == $arResult["NavPageNomer"]): ?>
			<span class="current"><?=$arResult["nStartPage"]?></span>
		<? elseif ($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false): ?>
			<a class="active" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a>
		<? else: ?>
			<a class="active" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a>
		<? endif; ?>
		<? $arResult["nStartPage"]++?>
	<? endwhile; ?>

	<? if($arResult["NavPageNomer"] < $arResult["NavPageCount"]): ?>
		<a class="active" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_next")?></a>
	<? else: ?>
		<a><?=GetMessage("nav_next")?></a>
	<? endif; ?>

	</div>
</li>