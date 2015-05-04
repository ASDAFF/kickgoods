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

$HEADER_TEXT = 'Недавно опубликованные';
if (!empty($arParams["HEADER_TEXT"]))
{
	$HEADER_TEXT = $arParams["HEADER_TEXT"];
}

$this->setFrameMode(true);
?>
<ul id="snippet-blog-sidebar" class="sidebar ">
	<li>
		<h2 id="snippet-blog-sidebar-title"><?=$HEADER_TEXT;?></h2>
	</li>

	<? foreach($arResult["ITEMS"] as $arItem): ?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<li class="sidebar-article " id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<h2><a class="sidebar-article-title" href="<?=$arItem["DETAIL_PAGE_URL"];?>"><?=$arItem["NAME"];?></a></h2>
			<p class="sidebar-article-date accent-text"><?=ToLower($arItem["DISPLAY_ACTIVE_FROM"]);?></p>
 		</li>
	<? endforeach; ?>
</ul>