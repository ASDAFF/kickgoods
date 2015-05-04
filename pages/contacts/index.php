<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("CLASS_BODY", "template-page");
$APPLICATION->SetTitle("Обратная связь");
?><div id="page-content">
	<div class="rte">
		<p>
			 Звоните: +7 499 678-02-55 <em>(для Москвы и МО)</em><br>
 <span style="color: #ffffff;">Звоните: +</span>8 800 700-23-94 <em>(для регионов России)</em><!--<br /><span style="color: #ffffff;">Звоните: </span>+7 963 924-90-55 <em>(в выходные)</em><span style="color: #ffffff;"><br /></span>-->
		</p>
		<p>
			 Пишите: <a href="mailto:hello@kickgoods.ru">hello@kickgoods.ru</a>
		</p>
	</div>
	 <?$APPLICATION->IncludeComponent(
	"bitrix:form", 
	".default", 
	array(
		"START_PAGE" => "new",
		"SHOW_LIST_PAGE" => "N",
		"SHOW_EDIT_PAGE" => "N",
		"SHOW_VIEW_PAGE" => "N",
		"SUCCESS_URL" => "",
		"WEB_FORM_ID" => "1",
		"RESULT_ID" => $_REQUEST[RESULT_ID],
		"SHOW_ANSWER_VALUE" => "N",
		"SHOW_ADDITIONAL" => "N",
		"SHOW_STATUS" => "N",
		"EDIT_ADDITIONAL" => "N",
		"EDIT_STATUS" => "N",
		"NOT_SHOW_FILTER" => array(
			0 => "",
			1 => "",
		),
		"NOT_SHOW_TABLE" => array(
			0 => "",
			1 => "",
		),
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"USE_EXTENDED_ERRORS" => "N",
		"SEF_MODE" => "N",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "7776000",
		"CHAIN_ITEM_TEXT" => "",
		"CHAIN_ITEM_LINK" => "",
		"SEF_FOLDER" => "/pages/contacts/",
		"AJAX_OPTION_ADDITIONAL" => "",
		"VARIABLE_ALIASES" => array(
			"action" => "action",
		)
	),
	false
);?>

</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>