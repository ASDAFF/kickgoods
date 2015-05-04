<?
global $APPLICATION;
$MODULE_ID = 'edost.delivery';
if (!CModule::IncludeModule('sale')) return false;

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$MODULE_ID.'/classes/general/delivery_edost.php');

CModule::AddAutoloadClasses($MODULE_ID, array('CEdostModifySaleOrderAjax' => 'general/edost_saleorderajax.php'));

// подключение скрипта карты и стилей
$order_link = COption::GetOptionString('edost.delivery', 'order_link', '/personal/order/make');
if ($order_link != '' && strpos($_SERVER['REQUEST_URI'], $order_link) === 0) {
	$config = CEdostModifySaleOrderAjax::GetEdostConfig(SITE_ID); // параметры модуля
	$date = date('dmY');
	$map_link = 'http://edostimg.ru/map/';
//	$map_link = '/bitrix/js/edost.delivery/'; // !!!!!

//	if ($config['template'] == 'Y') $APPLICATION->SetAdditionalCSS($map_link.'edost.css?a='.$date);
	if ($config['template'] == 'Y') $APPLICATION->AddHeadString('<link href="'.$map_link.'edost.css?a='.$date.'" type="text/css" rel="stylesheet" />');

	if ($config['map'] == 'Y')
		if ($config['template'] == 'Y') {
//			$APPLICATION->AddHeadScript('http://api-maps.yandex.ru/2.0-stable/?load=package.standard,package.clusters&lang=ru-RU');
			$APPLICATION->AddHeadString('<script type="text/javascript" src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard,package.clusters&lang=ru-RU"></script>');
			$APPLICATION->AddHeadString('<script type="text/javascript" src="'.$map_link.'edost.js?a='.$date.'" charset="utf-8"></script>');
		}
		else $APPLICATION->AddHeadString('<script type="text/javascript" src="http://www.pickpoint.ru/select/postamat.js" charset="utf-8"></script>');
}
?>