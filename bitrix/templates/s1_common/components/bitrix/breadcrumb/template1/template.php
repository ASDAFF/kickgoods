<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '<div class="accent-text" id="breadcrumbs">';

$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);

	if ($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .= '<span><a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a></span> <span class="sep">/</span> ';
	}
	else
	{
		$strReturn .= '<span>'.$title.'</span>';
	}
}

$strReturn .= '</div>';

return $strReturn;

?>