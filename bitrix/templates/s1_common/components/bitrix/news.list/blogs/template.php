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

<ul id="blog-content">

<? $arResultQNT = sizeof($arResult["ITEMS"])-1; ?>

<? foreach($arResult["ITEMS"] as $KEY=>$arItem): ?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>

	<?
	$class_index = '';
	if ($KEY == 0)
	{
		$class_index = ' first';
	}
	else if ($KEY == $arResultQNT)
	{
		$class_index = ' last';
	}

	$DETAIL_PAGE_URL_FULL = 'http://'.SITE_SERVER_NAME.$arItem["DETAIL_PAGE_URL"];

	$SHARE_DETAIL_PAGE_URL = urlencode($DETAIL_PAGE_URL_FULL);
	$SHARE_NAME = urlencode($arItem["NAME"]);
	
	?>

	<li class="blog-article<?=$class_index;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<h1 class="blog-article-title-outter"><a class="blog-article-title" href="<?=$arItem["DETAIL_PAGE_URL"];?>"><?=$arItem["NAME"];?></a></h1>

		<h2 href="<?=$arItem["DETAIL_PAGE_URL"];?>" class="blog-article-date accent-text"><?=ToLower($arItem["DISPLAY_ACTIVE_FROM"]);?></h2>

		<div class="rte">
		<?=$arItem["PREVIEW_TEXT"];?>
		</div>

		<?
		$GLOBALS["arInclude"] = array("DETAIL_PAGE_URL_FULL"=>$DETAIL_PAGE_URL_FULL,"SHARE_DETAIL_PAGE_URL"=>$SHARE_DETAIL_PAGE_URL,"SHARE_NAME"=>$SHARE_NAME);
		?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include", 
			".default", 
			array(
				"COMPONENT_TEMPLATE" => ".default",
				"AREA_FILE_SHOW" => "file",
				"AREA_FILE_SUFFIX" => "inc",
				"EDIT_TEMPLATE" => "",
				"PATH" => SITE_TEMPLATE_PATH."/includes/socials_share.php"
			),
			false
		);?>

	</li>

<? endforeach; ?>

<? if($arParams["DISPLAY_BOTTOM_PAGER"]): ?> 
	<?=$arResult["NAV_STRING"]?>
<? endif; ?>

</ul>
