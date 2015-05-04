<!doctype html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if IE 9 ]>	<html class="ie9 no-js"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
	<? $APPLICATION->AddHeadScript("//yandex.st/jquery/1.7.2/jquery.min.js"); ?>
	<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.colorbox-min.js"); ?>
	<? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/scripts.js"); ?>
	<link href="http://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold,bolditalic" rel="stylesheet" type="text/css" />
	<? $APPLICATION->AddHeadString('<meta name="yandex-verification" content="66819c0dbc9096ae" />',true); ?>
		<? $APPLICATION->AddHeadString('<meta name="format-detection" content="telephone=no">',true); ?>
	
	<? $APPLICATION->AddHeadString('<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />',true); ?>	

	<? $APPLICATION->AddHeadString('<link rel="shortcut icon" type="image/png" href="/favicon.ico" />',true); ?>

	<? $APPLICATION->ShowHead(); ?>
	
	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH;?>/js/html5.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH;?>/js/dd_roundies.js"></script>
	<![endif]-->

	<!--[if lt IE 8]>
	<script type="text/javascript" src="//yastatic.net/json2/2011-10-19/json2.min.js"></script>
	<![endif]-->

	<!--[if lt IE 9]>
	<script type="text/javascript">
	DD_roundies.addRule('.roundify-total','42px');
	DD_roundies.addRule('.sale-overlay span','50px');
	DD_roundies.addRule('.sold-out-overlay span','50px');
	</script>
	<![endif]-->

	<title><? $APPLICATION->ShowTitle(); ?></title>

	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include", 
		".default", 
		array(
			"COMPONENT_TEMPLATE" => ".default",
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "inc",
			"EDIT_TEMPLATE" => "",
			"PATH" => SITE_TEMPLATE_PATH."/includes/ga.php"
		),
		false
	);?>
</head>

<body id="<? $APPLICATION->ShowProperty("CLASS_ID_BODY");?>" class="<? $APPLICATION->ShowProperty("CLASS_BODY");?>">

	<? $APPLICATION->ShowPanel(); ?>

	<div style="background:#000; width:100%; height:60px;">&nbsp;</div>

	<div id="container">

		<header id="header" class="clearfix use-logo currencies">

			<nobr>
				<div class="phonenumber" style="float:left">+7 499 678-02-55 <span class="regime"><br>для Москвы и МО</span></div>
				<div class="phonenumber2">8 800 700-23-94 <span class="regime"><br />для регионов</span></div>
			</nobr>

			<div id="cart-summary" class="accent-text">
				<form action="/search/" method="get" id="search-form" role="search">
					<input name="q" type="text" id="search-field" placeholder="Поиск" class="hint" autocomplete="off" />
					<input type="submit" value="OK" name="submit" id="search-submit" />

					<div id="search-result" style="position: relative; right: -85px; top: 40px; width: 300px;"></div>
				</form>					
				
				<? $APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	"template1", 
	array(
		"NUM_CATEGORIES" => "3",
		"TOP_COUNT" => "5",
		"ORDER" => "date",
		"USE_LANGUAGE_GUESS" => "Y",
		"CHECK_DATES" => "Y",
		"SHOW_OTHERS" => "N",
		"PAGE" => "#SITE_DIR#search/",
		"SHOW_INPUT" => "N",
		"INPUT_ID" => "search-field",
		"CONTAINER_ID" => "search-result",
		"CATEGORY_0_TITLE" => "Каталог",
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"CATEGORY_0_iblock_catalog" => array(
			0 => "all",
		),
		"CATEGORY_1_TITLE" => "Блог",
		"CATEGORY_1" => array(
			0 => "iblock_blogs",
		),
		"CATEGORY_1_iblock_blogs" => array(
			0 => "all",
		),
		"CATEGORY_2_TITLE" => "Страницы",
		"CATEGORY_2" => array(
			0 => "main",
		),
		"CATEGORY_OTHERS_TITLE" => "Прочее"
	),
	false
);?>

				<a href="/cart/" class="cart-elem smooth roundify-total round" id="cart-total"><span id="cart-price"></span></a>
			</div>
			<!-- #cart-summary -->

			<a id="logo" href="/" role="banner">
				<img src="<?=SITE_TEMPLATE_PATH;?>/images/logo.png" alt="Кickgoods" width="250px" height="59px"/>
			</a>
			<!-- #logo -->

			<nav role="navigation">
				<ul id="nav">
			<? $APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
	"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_TIME" => "7776000",	// Время кеширования (сек.)
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"MENU_CACHE_GET_VARS" => array(	// Значимые переменные запроса
			0 => "",
		),
		"MAX_LEVEL" => "1",	// Уровень вложенности меню
		"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
); ?>
				</ul>
			</nav>

			<? if (defined("PAGE_TYPE") && PAGE_TYPE == "index") { ?>
			<h1 class="hidden"><? $APPLICATION->ShowTitle(""); ?></h1>
			<? } ?>

		</header>
		<!-- #header -->


		<div class="clearfix" id="content" role="main">
		
			<? if (!defined("PAGE_TYPE")) { ?>
			<div class="clearfix page-container <? $APPLICATION->ShowProperty("CLASS_PAGE_CONTAINER"); ?>">
			<? } ?>

				<? $APPLICATION->AddBufferContent("SHOW_TITLE_H1"); ?>

				