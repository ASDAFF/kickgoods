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

$DETAIL_PAGE_URL_FULL = 'http://'.SITE_SERVER_NAME.$arResult["DETAIL_PAGE_URL"];

$SHARE_DETAIL_PAGE_URL = urlencode($DETAIL_PAGE_URL_FULL);
$SHARE_NAME = urlencode($arResult["NAME"]);
?>
<ul id="blog-content">

	<li class="blog-article no-comment">
		<article class="instapaper_body hentry">
			<header>
				<h1 class="blog-article-title-outter instapaper_title entry-title"><?=$arResult["NAME"];?></h1>
				<h2 class="blog-article-date accent-text"><time pubdate datetime="<?=ConvertDateTime($arResult["ACTIVE_FROM"],"YYYY-MM-DD");?>"><?=ToLower($arResult["DISPLAY_ACTIVE_FROM"]);?></time></h2>
			</header>
			<div class="rte entry-content">
			<?=$arResult["DETAIL_TEXT"];?>
			</div>
		</article>

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

	<li class="accent-text prev-next clearfix"> 
		<? if (isset($arResult["TOLEFT"])) { ?>
		<span class="left"><a href="<?=$arResult["TOLEFT"]["DETAIL_PAGE_URL"];?>">&larr; Следующая запись</a></span>
		<? } ?>

		<? if (isset($arResult["TORIGHT"])) { ?>
		<span class="right"><a href="<?=$arResult["TORIGHT"]["DETAIL_PAGE_URL"];?>">Предыдущая запись &rarr;</a></span>
		<? } ?>
	</li>

	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include", 
		".default", 
		array(
			"COMPONENT_TEMPLATE" => ".default",
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "inc",
			"EDIT_TEMPLATE" => "",
			"PATH" => SITE_TEMPLATE_PATH."/includes/disqus.php"
		),
		false
	);?>

</ul>
<!-- #page-content -->