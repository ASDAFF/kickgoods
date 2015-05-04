<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? if(!empty($arResult["CATEGORIES"])): ?>
	<table class="title-search-result">
		<? foreach($arResult["CATEGORIES"] as $category_id => $arCategory): ?>
			<tr>
				<th class="title-search-separator">&nbsp;</th>
				<td class="title-search-separator">&nbsp;</td>
			</tr>
			<? foreach($arCategory["ITEMS"] as $i => $arItem): ?>
			<tr>
				<? if($i == 0): ?>
					<th>&nbsp;<?=$arCategory["TITLE"];?></th>
				<? else: ?>
					<th>&nbsp;</th>
				<? endif; ?>

				<? if ($category_id === "all"): ?>
					<td class="title-search-all"><a href="<?=$arItem["URL"];?>"><?=$arItem["NAME"];?></a></td>
				<? elseif (isset($arItem["ICON"])): ?>
					<td class="title-search-item"><a href="<?=$arItem["URL"];?>"><? if (!empty($arItem["PREVIEW_PICTURE"])) { ?><img src="<?=$arItem["PREVIEW_PICTURE"];?>" style="margin: 0 10px 0 0;"><? } ?><?=$arItem["NAME"];?></a></td>
				<? else: ?>
					<td class="title-search-more"><a href="<?=$arItem["URL"];?>"><?=$arItem["NAME"];?></a></td>
				<? endif; ?>
			</tr>
			<? endforeach; ?>
		<? endforeach; ?>
		<tr>
			<th class="title-search-separator">&nbsp;</th>
			<td class="title-search-separator">&nbsp;</td>
		</tr>
	</table><div class="title-search-fader"></div>
<? endif; ?>