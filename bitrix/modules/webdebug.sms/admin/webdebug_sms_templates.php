<?
$ModuleID = 'webdebug.sms';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
CModule::IncludeModule($ModuleID);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
IncludeModuleLangFile(__FILE__);

if (webdebug_sms_demo_expired()) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	webdebug_sms_show_demo();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$sTableID = "WebdebugSMSTemplates";
$oSort = new CAdminSorting($sTableID, "SORT", "ASC");
$lAdmin = new CAdminList($sTableID, $oSort);

$arEventList = CWD_SMS_Provider::GetEventsList("event_name");

// Filter
function CheckFilter() {
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $f;
	return count($lAdmin->arFilterErrors)==0;
}
$FilterArr = Array(
	"find_id",
	"find_name",
	"find_active",
	"find_sort",
	"find_description",
	"find_template",
	"find_receiver",
	"find_event",
);
$lAdmin->InitFilter($FilterArr);
if (CheckFilter()) {
	$arFilter = Array(
		"ID" => $find_id,
		"%NAME" => $find_name,
		"ACTIVE" => $find_active,
		"SORT" => $find_sort,
		"%DESCRIPTION" => $find_description,
		"%TEMPLATE" => $find_template,
		"%RECEIVER" => $find_receiver,
		"EVENT" => $find_event,
	);
}

// Processing with actions
if($lAdmin->EditAction()) {
	foreach($FIELDS as $ID=>$arFields) {
		if(!$lAdmin->IsUpdated($ID)) continue;
		$DB->StartTransaction();
		$ID = IntVal($ID);
		if(($rsData = CWD_SMS_Template::GetByID($ID)) && ($arData = $rsData->Fetch())) {
			foreach($arFields as $key=>$value) $arData[$key]=$value;
			unset($arData["PHRASES_COUNT"]);
			if(!CWD_SMS_Template::Update($ID, $arData)) {
				$lAdmin->AddGroupError(GetMessage("rub_save_error"), $ID);
				$DB->Rollback();
			}
		} else {
			$lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}
if(($arID = $lAdmin->GroupAction())) {
  if($_REQUEST['action_target']=='selected') {
    $rsData = CWD_SMS_Template::GetList(array($by=>$order), $arFilter);
    while($arRes = $rsData->Fetch()) $arID[] = $arRes['ID'];
  }
  foreach($arID as $ID) {
    if(strlen($ID)<=0) continue;
    $ID = IntVal($ID);
    switch($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!CWD_SMS_Template::Delete($ID)) {
					$DB->Rollback();
					$lAdmin->AddGroupError(GetMessage("rub_del_err"), $ID);
				}
				$DB->Commit();
				break;
			case "activate":
			case "deactivate":
				if(($rsData = CWD_SMS_Template::GetByID($ID)) && ($arFields = $rsData->Fetch())) {
					$arFields["ACTIVE"]=($_REQUEST['action']=="activate"?"Y":"N");
					if(!CWD_SMS_Template::Update($ID, $arFields)) {
						$lAdmin->AddGroupError(GetMessage("rub_save_error"), $ID);
					}
				} else {
					$lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
				}
				break;
    }
  }
}

// Get items list
$rsData = CWD_SMS_Template::GetList(array($by => $order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("rub_nav")));

// Add headers
$lAdmin->AddHeaders(array(
  array(
	  "id" => "ID",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_ID"),
    "sort" => "id",
    "align" => "right",
    "default" => true,
  ),
  array(
	  "id" =>"NAME",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_NAME"),
    "sort" => "name",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"ACTIVE",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_ACTIVE"),
    "sort" => "active",
		"align" => "center",
    "default" => true,
  ),
  array(
	  "id" =>"SORT",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_SORT"),
    "sort" => "sort",
    "align" => "right",
    "default" => true,
  ),
  array(
	  "id" =>"DESCRIPTION",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_DESCRIPTION"),
    "sort" => "description",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"TEMPLATE",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_TEMPLATE"),
    "sort" => "template",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"RECEIVER",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_RECEIVER"),
    "sort" => "name",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"EVENT",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_EVENT"),
    "sort" => "event",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"STOP",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_STOP"),
    "sort" => "stop",
		"align" => "center",
    "default" => false,
  ),
  array(
	  "id" =>"RECEIVER_FROM_EMAIL",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_RECEIVER_FROM_EMAIL"),
    "sort" => "receiver_from_email",
		"align" => "center",
    "default" => false,
  ),
  array(
	  "id" =>"EMAIL_FIELD",
    "content" => GetMessage("WEBDEBUG_SMS_HEADER_EMAIL_FIELD"),
    "sort" => "email_field",
		"align" => "left",
    "default" => false,
  ),
));

// Build items list
while ($arRes = $rsData->NavNext(true, "f_")) {
  $row = &$lAdmin->AddRow($f_ID, $arRes); 
	// ID
	$row->AddViewField("ID", "<a href='webdebug_sms_template_edit.php?ID={$f_ID}&lang=".LANGUAGE_ID."' title='".GetMessage("WEBDEBUG_SMS_EDITPROFILE")."'>{$f_ID}</a>");
  // NAME
  $row->AddInputField("NAME",array("SIZE" => "40"));
  $row->AddViewField("NAME", "<a href='webdebug_sms_template_edit.php?ID={$f_ID}&lang=".LANGUAGE_ID."' title='".GetMessage("WEBDEBUG_SMS_EDITPROFILE")."'>{$f_NAME}</a>");
  // ACTIVE
  $row->AddCheckField("ACTIVE"); 
  // SORT
  $row->AddInputField("SORT", array("SIZE"=>5)); 
	// DESCRIPTION
	$sHTML = '<textarea rows="5" cols="50" name="FIELDS['.$f_ID.'][DESCRIPTION]">'.htmlspecialchars($row->arRes["DESCRIPTION"]).'</textarea>';
	$row->AddEditField("DESCRIPTION", $sHTML);
	$row->AddViewField("DESCRIPTION", $f_DESCRIPTION);
	// TEMPLATE
	$sHTML = '<textarea rows="5" cols="50" name="FIELDS['.$f_ID.'][TEMPLATE]">'.htmlspecialchars($row->arRes["TEMPLATE"]).'</textarea>';
	$row->AddEditField("TEMPLATE", $sHTML);
	$row->AddViewField("TEMPLATE", $f_TEMPLATE);
  // RECEIVER
  $row->AddInputField("RECEIVER",array("SIZE" => "20"));
  // EVENT
	$sHTML = '<select name="FIELDS['.$f_ID.'][EVENT]">';
	$sHTML .= '<option>'.GetMessage("WEBDEBUG_SMS_ROW_EVENT_EMPTY").'</option>';
	foreach ($arEventList as $arEvent) {
		$Selected = "";
		if ($arEvent["EVENT_NAME"]==$f_EVENT) {
			$Selected = " selected=\"selected\"";
		}
		$sHTML .= '<option value="'.$arEvent["EVENT_NAME"].'"'.$Selected.'>['.$arEvent["EVENT_NAME"].'] '.$arEvent["NAME"].'</option>';
	}
	$sHTML .= '</select>';
	$row->AddEditField("EVENT", $sHTML);
	$row->AddViewField("EVENT", $f_EVENT);
	// STOP
	$row->AddCheckField("STOP"); 
	// RECEIVER_FROM_EMAIL
	$row->AddCheckField("RECEIVER_FROM_EMAIL");
	// EMAIL_FIELD
  $row->AddInputField("EMAIL_FIELD", array("SIZE"=>25)); 
	
	// Build context menu
  $arActions = Array();
  $arActions[] = array(
    "ICON" => "edit",
    "DEFAULT"=>true,
    "TEXT" => GetMessage("WEBDEBUG_SMS_CONTEXT_EDIT"),
    "ACTION"=>$lAdmin->ActionRedirect("webdebug_sms_template_edit.php?ID=".$f_ID."&lang=".LANGUAGE_ID)
  );
  $arActions[] = array(
    "ICON" => "copy",
    "DEFAULT"=>false,
    "TEXT" => GetMessage("WEBDEBUG_SMS_CONTEXT_COPY"),
    "ACTION"=>$lAdmin->ActionRedirect("webdebug_sms_template_edit.php?CopyID=".$f_ID."&lang=".LANGUAGE_ID)
  );
	$arActions[] = array(
		"ICON" => "delete",
		"DEFAULT"=>false,
		"TEXT" => GetMessage("WEBDEBUG_SMS_CONTEXT_DELETE"),
		"ACTION" => "if(confirm('".sprintf(GetMessage('WEBDEBUG_SMS_CONTEXT_DELETE_CONFIRM'), $f_NAME)."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
	);
  $arActions[] = array("SEPARATOR"=>true);
  if(is_set($arActions[count($arActions)-1], "SEPARATOR")) {
    unset($arActions[count($arActions)-1]);
	}
  $row->AddActions($arActions);
}

// List Footer
$lAdmin->AddFooter(
  array(
    array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
    array("counter"=>true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
  )
);
$lAdmin->AddGroupActionTable(Array(
  "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
  "activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
  "deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
));

// Context menu
$aContext = array(
  array(
    "TEXT" => GetMessage("WEBDEBUG_SMS_TOOLBAR_ADD_NAME"),
    "LINK" => "webdebug_sms_template_edit.php?lang=".LANGUAGE_ID,
    "TITLE" => GetMessage("WEBDEBUG_SMS_TOOLBAR_ADD_DESC"),
    "ICON" => "btn_new",
  ),
);
$lAdmin->AddAdminContextMenu($aContext);

// Start output
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("WEBDEBUG_SMS_SEND_PAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!webdebug_sms_demo_expired()) {
	webdebug_sms_show_demo();
}

// Output filter
$oFilter = new CAdminFilter(
  $sTableID."_filter",
  array(
		'ID' => GetMessage("WEBDEBUG_SMS_FILTER_NAME"),
		'ACTIVE' => GetMessage("WEBDEBUG_SMS_FILTER_ACTIVE"),
		'SORT' => GetMessage("WEBDEBUG_SMS_FILTER_SORT"),
		'DESCRIPTION' => GetMessage("WEBDEBUG_SMS_FILTER_DESCRIPTION"),
		'TEMPLATE' => GetMessage("WEBDEBUG_SMS_FILTER_TEMPLATE"),
		'RECEIVER' => GetMessage("WEBDEBUG_SMS_FILTER_RECEIVER"),
		'EVENT' => GetMessage("WEBDEBUG_SMS_FILTER_EVENT"),
  )
);
?>

<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
	<?$oFilter->Begin();?>
	<tr>
		<td><b><?=GetMessage("WEBDEBUG_SMS_FILTER_ID")?>:</b></td>
		<td>
			<input type="text" size="25" name="find_id" value="<?=htmlspecialchars($find_id)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_ID_DESCR");?>"/>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_SMS_FILTER_NAME")?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_name" value="<?=htmlspecialchars($find_name)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_NAME_DESCR");?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_SMS_FILTER_ACTIVE")?>:</td>
		<td>
			<?
			$arr = array(
				"reference" => array(
					GetMessage("WEBDEBUG_SMS_FILTER_ACTIVE_Y"),
					GetMessage("WEBDEBUG_SMS_FILTER_ACTIVE_N"),
				),
				"reference_id" => array("Y","N")
			);
			echo SelectBoxFromArray("find_active", $arr, $find_active, GetMessage("WEBDEBUG_SMS_FILTER_ACTIVE_ANY"), "");
			?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_SMS_FILTER_SORT")?>:</td>
		<td><input type="text" size="10" maxlength="10" name="find_sort" value="<?=htmlspecialchars($find_sort)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_SORT_DESCR");?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_SMS_FILTER_DESCRIPTION")?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_description" value="<?=htmlspecialchars($find_description)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_DESCRIPTION_DESCR");?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_SMS_FILTER_TEMPLATE")?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_template" value="<?=htmlspecialchars($find_template)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_TEMPLATE_DESCR");?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_SMS_FILTER_RECEIVER")?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_receiver" value="<?=htmlspecialchars($find_receiver)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_RECEIVER_DESCR");?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_SMS_FILTER_EVENT")?>:</td>
		<td>
			<select name="fields[EVENT]">
				<?if(is_array($arEventList) && !empty($arEventList)):?>
					<option value=""><?=GetMessage("WEBDEBUG_SMS_FILTER_EVENT_ANY");?></option>
					<?$arCurrentEvent=$arEventList[0]?>
					<?foreach ($arEventList as $arEvent):?>
						<option value="<?=$arEvent["NAME"]?>">[<?=$arEvent["EVENT_NAME"]?>] [<?=$arEvent["NAME"]?>]</option>
					<?endforeach?>
				<?else:?>
					<option><?=GetMessage("WEBDEBUG_SMS_FIELD_EVENT_EMPTY");?></option>
				<?endif?>
			</select>
		</td>
	</tr>
	<?$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form" => "find_form"));?>
	<?$oFilter->End();?>
</form>

<?// Output ?>
<?$lAdmin->DisplayList();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>