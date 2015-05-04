<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if (!empty($arResult)): ?>

	<? $arResultQNT = sizeof($arResult)-1; ?>

	<? foreach($arResult as $arItem): ?>
	<?
	$class_index = '';
	if ($arItem["ITEM_INDEX"] == 0)
	{
		$class_index = ' first';
	}
	else if ($arItem["ITEM_INDEX"] == $arResultQNT)
	{
		$class_index = ' last';
	}
	?>
	<li class="nav-item<?=$class_index;?><?=($arItem["SELECTED"]) ? ' active' : ''; ?>">
		<a class="nav-item-link smooth" href="<?=$arItem["LINK"];?>"><?=$arItem["TEXT"];?></a>
	</li>
	<? endforeach; ?>

<? endif; ?>