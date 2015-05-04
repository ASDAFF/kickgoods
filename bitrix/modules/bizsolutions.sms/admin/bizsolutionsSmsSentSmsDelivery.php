<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/include.php");
IncludeModuleLangFile(__FILE__);

$module_id = "bizsolutions.sms";
CModule::IncludeModule($module_id);
$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($SMS_RIGHT < "R")
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

global $SMSBiz;
global $APPLICATION;

$arTime = localtime(time(), true);
$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/jquery.js');
$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/SMSBizSendSms.js');
$APPLICATION->SetAdditionalCSS('/bitrix/js/'.$module_id.'/css/styles.css');

global $USER;
$groupsList = array();
$rsGroups = CGroup::GetList(($by="name"), ($order="asc"), false);
while ($group = $rsGroups->GetNext()) {
    $groupsList[$group['ID']] = $group['NAME'];
}

$socialGroupsList = array();
if(CModule::IncludeModule("socialnetwork")) {

    $groupList = CSocNetGroup::GetList(array("ID" => "DESC"), array(), false, false, array());

    while($group = $groupList->GetNext()){
        $socialGroupsList[$group['ID']] = $group['NAME'];
    }
}
$arUserPhoneField = $SMSBiz->getUserPhoneFields();

$SMSBiz->setLogin(COption::GetOptionString("bizsolutions.sms", "LOGIN"));
$SMSBiz->setPassword(COption::GetOptionString("bizsolutions.sms", "PASSWORD"));
$profileBalance = $SMSBiz->balance();
$serverResponse = false;
$errorMsg = "";
$userGroupArray = array();
$socialUserGroupArray = array();
// Get list of sources from profile
$orgInfoString = $SMSBiz->getProfileOrgInfo();
if ($orgInfoString) {
	$orgInfo = json_decode($orgInfoString, true);
	$sourceArray = array();
	foreach($orgInfo["source"] as $source) {
		$sourceArray[$source] = $source;
	}
} 

if ($SMSBiz->getError() == '' && $profileBalance !== false) {

    if (strlen($_REQUEST['apply']) > 0) {
        $source = $_REQUEST["source"];
        $phoneFieldBitrixGroup = $_REQUEST["phone_field_bitrix_group"];
		$phoneFieldSocialGroup = $_REQUEST["phone_field_social_group"];

        if ($_REQUEST["user_group"]) {
            $userGroupArray = $_REQUEST["user_group"];
        }
        if ($_REQUEST["social_user_group"]) {
            $socialUserGroupArray = $_REQUEST["social_user_group"];
        }
		if ($_REQUEST["additionalPhoneNumbers"]) {
            $additionalPhoneNumbers = $_REQUEST["additionalPhoneNumbers"];
        }

        $isSelectedBitrixGroup = $_REQUEST["isBitrixGroup"];
        $isSelectedSocialGroup = $_REQUEST["isSocialGroup"];
		$isSelectedOrderGroup = $_REQUEST["isOrderGroup"];
        $isSelectedAdditionalPhoneNumbers = $_REQUEST["isAdditionalPhoneNumbers"];

        $message = $_REQUEST["message"];

        $errorMsg = "";
		if (
			$isSelectedOrderGroup == false
			&& $isSelectedBitrixGroup == false
            && $isSelectedSocialGroup == false
            && $isSelectedAdditionalPhoneNumbers == false
		) {
			$errorMsg = GetMessage("WARNING_CHECKBOXES_NOT_SELECTED");
			if (strlen($message) == 0) {
				$errorMsg .= GetMessage("WARNING_SMS_TEXT_IS_EMPTY");
			}
		} else {
			if ($isSelectedBitrixGroup == true) {
				if ($phoneFieldBitrixGroup == 'blank') {
					$errorMsg .= GetMessage("WARNING_PHONE_FIELD_NOT_SELECTED");
				}
				if (count($userGroupArray) == 0) {
					$errorMsg .= GetMessage("WARNING_GROUP_NOT_SELECTED");
				}
			}
			if ($isSelectedSocialGroup == true) {
				if ($phoneFieldSocialGroup == 'blank') {
					$errorMsg .= GetMessage("WARNING_PHONE_FIELD_NOT_SELECTED");
				}
				if (count($socialUserGroupArray) == 0) {
					$errorMsg .= GetMessage("WARNING_SOCIAL_GROUP_NOT_SELECTED");
				}
			}
			if ($isSelectedAdditionalPhoneNumbers == 'on' && strlen($additionalPhoneNumbers) == 0) {
				$errorMsg .= GetMessage("WARNING_ADDITIONAL_PHONES_IS_EMPTY");
			}
			if (strlen($message) == 0) {
				$errorMsg .= GetMessage("WARNING_SMS_TEXT_IS_EMPTY");
			}
		}
        $destinationsPhoneList = array();
        if(strlen($errorMsg) == 0) {

            $phoneNameList = array();
			
			if ($isSelectedOrderGroup == 'on') {
				if (CModule::IncludeModule("sale")) {
										
					$arFilter = array();
					
					if (strlen($_POST["orderGroupDateFrom"]) > 0) {
						$arFilter[">=DATE_INSERT"] = $_POST["orderGroupDateFrom"];
					}
					
					if (strlen($_POST["orderGroupDateTo"]) > 0) {
						$arFilter["<=DATE_INSERT"] = $_POST["orderGroupDateTo"];
					}

					
					$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
					$orderListIds = array();
					while ($ar_sales = $db_sales->Fetch()) {
						$orderListIds[] = $ar_sales["ID"];
					}
					if ($orderListIds) {
						$dbProp = CSaleOrderPropsValue::GetList(
							array("SORT" => "ASC"),
							array(
								"ORDER_ID" => $orderListIds,
								"CODE" => "PHONE"
							)
						);
						
						while ($arrOrderProps = $dbProp->GetNext()) {
							$destinationsPhoneList[$arrOrderProps["VALUE"]] = $arrOrderProps["VALUE"];
							$phoneNameList[$arrOrderProps["VALUE"]] = "Unknown user name - ".$arrOrderProps["VALUE"];
						}
					}
				}
			}
            if ($isSelectedBitrixGroup == 'on') {
                $phoneList = array();
                $filter = Array (
                    "ACTIVE" => "Y",
                    "GROUPS_ID" => $userGroupArray
                );
                $by = 'last_login';
                $order = 'desc';
                $rsUsers = CUser::GetList($by, $order, $filter, array("FIELDS"=>array($phoneFieldBitrixGroup, "NAME", "LAST_NAME")));

                while($rsUser = $rsUsers->Fetch()) {
                    if (strlen($rsUser[$phoneFieldBitrixGroup]) > 0) {
                        $phoneList[] = $rsUser[$phoneFieldBitrixGroup];
                        $phoneNameList[] = $rsUser["NAME"]." ".$rsUser["LAST_NAME"]." - ".$rsUser[$phoneFieldBitrixGroup];
                    }
                }

                $destinationsPhoneList = array_merge($destinationsPhoneList, $SMSBiz->parseNumbers($phoneList));
            }

            if ($isSelectedSocialGroup == 'on') {
                $phoneList = array();
                $dbMembers = CSocNetUserToGroup::GetList(
                    array("RAND" => "ASC"),
                    array("GROUP_ID" => $socialUserGroupArray, "<=ROLE" => SONET_ROLES_USER, "USER_ACTIVE" => "Y"),
                    false,
                    array("nTopCount" => $arParams["ITEMS_COUNT"]),
                    array("USER_ID")
                );

                $userIds = "";
                while($rsUser = $dbMembers->Fetch()) {
                    $userIds .= $rsUser["USER_ID"]." | ";
                }
                if (count($userIds) > 0) {
                    $filter = Array (
                        "ACTIVE" => "Y",
                        "ID" => $userIds
                    );
                    $by = 'last_login';
                    $order = 'desc';
                    $rsUsers = CUser::GetList($by, $order, $filter, array("FIELDS"=>array($phoneFieldSocialGroup, "NAME", "LAST_NAME")));

                    while($rsUser = $rsUsers->Fetch()) {
                        $phone = $rsUser[$phoneFieldSocialGroup];
                        if (
                            !is_null($rsUser[$phoneFieldSocialGroup])
                            && count($rsUser[$phoneFieldSocialGroup]) > 0
                        ) {
                            $phoneList[] = $rsUser[$phoneFieldSocialGroup];
                            $phoneNameList[] = $rsUser["NAME"]." ".$rsUser["LAST_NAME"]." - ".$rsUser[$phoneFieldSocialGroup];
                        }
                    }
                }
                $destinationsPhoneList = array_merge($destinationsPhoneList, $SMSBiz->parseNumbers($phoneList));
            }

            if ($isSelectedAdditionalPhoneNumbers == "on") {
                $phonesArray = $SMSBiz->parseNumbers($additionalPhoneNumbers);
                foreach ($phonesArray as $phone) {
                    $phoneNameList[] = "Unknown user name - ".$phone;
                }
                $destinationsPhoneList = array_merge($destinationsPhoneList, $phonesArray);
            }
			
			if (count($destinationsPhoneList) == 0) {
                $errorMsg .= GetMessage("WARNING_PHONES_IN_THIS_FIELD_NOT_FOUND");;
            } else {
                $serverResponse = $SMSBiz->send(
                    array(
                        "source" => $source,
                        "text" => $message
                    ),
                    $destinationsPhoneList
                );
            }
        }
    }

} else {
    $errorMsg .= GetMessage("WARNING_ERROR_OCCURED_OR_NOT_AUTHORIZED");
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="sendDeliveryForm" method="POST" action="<?=$APPLICATION->GetCurPage()?>" onsubmit="sendDeliveryForm.sub.disabled=true;">


<script>
    $(document).ready(function() {
        var obSendingForm = new SendDeliveryForm();
    })
</script>

<?=bitrix_sessid_post()?>
<?

$aTabs = array(
    array("DIV"=>"edit1", "TAB"=>GetMessage("TAB_SEND_SMS_TITLE"), "ICON"=>"", "TITLE"=>GetMessage("TAB_SEND_SMS_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>

<?
$tabControl->Begin();
if(strlen($errorMsg) != 0) {
    ShowError($errorMsg);
}
$tabControl->BeginNextTab();

?>

    <?if ($profileBalance):?>
		<tr>
			<td colspan="2" valign="top">
				<?echo GetMessage("TAB_MAIN_BALANCE").$profileBalance.GetMessage("DELIVERY_RUB")?>
				<a href="http://lcab.smsintel.ru/authAs/<?=$SMSBiz->getLogin()?>/<?=$SMSBiz->getPassword()?>/%2Fpay">(<?=GetMessage("TAB_MAIN_TOP_UP_BALANCE")?>)</a>
			</td>
		</tr>
	<?endif;?>

	<?if (!$profileBalance):?>
		<tr>
			<td colspan="2" valign="top">
				<?=GetMessage("TAB_MAIN_REGISTRATION_1")?>
				<a href="http://lcab.smsintel.ru/?manager=77915&promocode=bitrix2311#regTab"><?=GetMessage("TAB_MAIN_REGISTRATION_2")?></a>
			</td>
		</tr>
	<?endif;?>
	
	<tr>
        <td colspan="2" valign="top">
            <a href="/bitrix/admin/settings.php?mid=bizsolutions.sms"><?=GetMessage("WARNING_ERROR_OCCURED_OR_NOT_AUTHORIZED2")?></a>
        </td>
    </tr>

<?if(!$serverResponse):?>

    <tr class="heading">
        <td colspan="3">
            <?=GetMessage("SECTION_SOURCE_OPTIONS")?>
        </td>
    </tr>

    <tr>
        <td class="blankColumn"></td>
        <td class="labelColumn">
            <?=GetMessage("DELIVERY_SETTINGS_SOURCE")?>
        </td>
        <td>
            <select name="source">
                <?foreach ($sourceArray as $sourceItem): ?>
                    <?if($source == $sourceItem):?>
                        <option value="<?=$sourceItem?>" selected><?=$sourceItem?></option>
                    <?else:?>
                        <option value="<?=$sourceItem?>" ><?=$sourceItem?></option>
                    <?endif;?>
                <?endforeach;?>
            </select>
        </td>
    </tr>

    <tr class="heading">
        <td colspan="3">
            <?=GetMessage("SECTION_DESTINATION_OPTIONS")?>
        </td>
    </tr>

	<!-- send to order customers-->
	<?if (CModule::IncludeModule("sale")):?>
		<tr class="subheading">
			<td class="blankColumn"></td>
			<td class="labelColumn">
				<?=GetMessage("DELIVERY_SETTINGS_ORDER_GROUPS")?>
			</td>
			<td class="valueColumn">
				<input type="checkbox" id="isOrderGroup" name="isOrderGroup" <?if ($isSelectedOrderGroup == 'on'):?> checked="checked" <?endif;?>/>
			</td>
		</tr>

		<tr class="optionsCenter">
			<td class="valueColumn" colspan="3">
				<?echo CAdminCalendar::CalendarPeriod("orderGroupDateFrom", "orderGroupDateTo", $orderGroupFromVal, $orderGroupToVal, false, 10, false)?>
			</td>
			
		</tr>
		<tr class="optionsCenter">
			<td class="valueColumn" colspan="3">
				<?=BeginNote();?>
					<?=GetMessage("NOTE_DATES_ORDER")?>
					
				<?=EndNote();?>
			</td>
		</tr>
	<?endif;?>
	<!-- send to order customers-->

	<!-- send to bitrix groups-->
	<tr class="subheading">
        <td class="blankColumn"></td>
        <td class="labelColumn">
            <?=GetMessage("DELIVERY_SETTINGS_USER_GROUPS")?>
        </td>
        <td class="valueColumn">
            <input type="checkbox" id="isBitrixGroup" name="isBitrixGroup" <?if ($isSelectedBitrixGroup == 'on'):?> checked="checked" <?endif;?>/>
        </td>
    </tr>
	
	<tr class="options">
        <td class="blankColumn"></td>
        <td class="labelColumn">
            <?=GetMessage("DELIVERY_SETTINGS_PHONE_FIELD")?>
        </td>
        <td class="valueColumn">
            <select id="phone_field_bitrix_group" name="phone_field_bitrix_group">
                <?foreach ($arUserPhoneField as $key=>$phoneFieldItem): ?>
                    <?if($phoneFieldBitrixGroup == $phoneFieldItem):?>
                        <option value="<?=$key?>" selected><?=$phoneFieldItem?></option>
                    <?else:?>
                        <option value="<?=$key?>" ><?=$phoneFieldItem?></option>
                    <?endif;?>
                <?endforeach;?>
            </select>
        </td>
    </tr>
	
	<tr class="optionsCenter">
        <td class="valueColumn" colspan="3">
            <select id="bitrixGroup" name="user_group[]" multiple="multiple">
                <?foreach ($groupsList as $key=>$group): ?>
                    <?if(array_search($key, $userGroupArray, false) === false):?>
                        <option value="<?=$key?>"><?=$group?></option>
                    <?else:?>
                        <option value="<?=$key?>" selected><?=$group?></option>
                    <?endif;?>
                <?endforeach;?>
            </select>
        </td>
    </tr>
	
	<!-- send to bitrix groups-->

	<!-- send to social group-->
    <?
	if(
		CModule::IncludeModule("socialnetwork") 
		&& count($socialGroupsList) > 0
	):
	?>
	
		<tr class="subheading">
			<td class="blankColumn"></td>
			<td class="labelColumn">
				<?=GetMessage("DELIVERY_SETTINGS_SOCIAL_GROUPS")?>
			</td>
			<td class="valueColumn">
				<input type="checkbox" id="isSocialGroup" name="isSocialGroup"  <?if ($isSelectedSocialGroup == 'on'):?> checked="checked" <?endif;?>/>
			</td>
		</tr>
		
		<tr class="options">
			<td class="blankColumn"></td>
			<td class="labelColumn">
				<?=GetMessage("DELIVERY_SETTINGS_PHONE_FIELD")?>
			</td>
			<td class="valueColumn">
				<select id="phone_field_social_group" name="phone_field_social_group">
					<?foreach ($arUserPhoneField as $key=>$phoneFieldItem): ?>
						<?if($phoneFieldSocialGroup == $phoneFieldItem):?>
							<option value="<?=$key?>" selected><?=$phoneFieldItem?></option>
						<?else:?>
							<option value="<?=$key?>" ><?=$phoneFieldItem?></option>
						<?endif;?>
					<?endforeach;?>
				</select>
			</td>
		</tr>
		
		<tr class="optionsCenter">
			<td class="valueColumn" colspan="3">
				<select id="socialGroup" name="social_user_group[]" multiple="multiple">
                    <?foreach ($socialGroupsList as $keyS=>$groupS): ?>
                        <?if(array_search($keyS, $socialUserGroupArray, false) === false):?>
                            <option value="<?=$keyS?>"><?=$groupS?></option>
                        <?else:?>
                            <option value="<?=$keyS?>" selected><?=$groupS?></option>
                        <?endif;?>
                    <?endforeach;?>
                </select>
			</td>
		</tr>
		
    <?endif;?>
	<!-- send to social group-->
	
	<!-- send to additional phone numbers-->
	
	
	<tr class="subheading">
		<td class="blankColumn"></td>
		<td class="labelColumn">
			<?=GetMessage("DELIVERY_SETTINGS_ADDITIONAL_PHONES")?>
		</td>
		<td class="valueColumn">
			<input type="checkbox" id="isAdditionalPhoneNumbers" name="isAdditionalPhoneNumbers" <?if ($isSelectedAdditionalPhoneNumbers == 'on'):?> checked="checked" <?endif;?>/>
		</td>
	</tr>
	
	<tr class="optionsCenter">
		<td class="valueColumn" colspan="3">
			<textarea
                id="additionalPhoneNumbers"
                rows="3"
                cols="55"
                name="additionalPhoneNumbers"
                placeholder="<?=GetMessage('TAB_DISTRIBUTION_ADDITIONAL_PHONE_NUMBERS_DEFAULT_TEXT')?>">
                <?if(strlen($additionalPhoneNumbers) > 0):?><?=$additionalPhoneNumbers?><?endif;?>
            </textarea>
		</td>
	</tr>
	<!-- send to additional phone numbers-->
    	
	<tr class="heading">
        <td colspan="3">
            <?=GetMessage("SECTION_SMS")?>
        </td>
    </tr>

    <tr>
        <td class="blankColumn"></td>
        <td class="labelColumn"></td>
        <td class="valueColumn">
            <div class="counters">
                <div id="messageLengthDiv"><?=GetMessage('TAB_DISTRIBUTION_SMS_TEXT_LENGTH')?><span id="messageLength">0</span></div>
                <div id="partSizeDiv"><?=GetMessage('TAB_DISTRIBUTION_SMS_PART_LENGTH')?><span id="partSize">160</span></div>
                <div id="partsCountDiv"><?=GetMessage('TAB_DISTRIBUTION_SMS_PARTS_COUNT')?></span><span id="partsCount">0</span></div>
            </div>
        </td>
    </tr>

    <tr>
        <td class="blankColumn"></td>
        <td class="labelColumn">
            <?=GetMessage("DELIVERY_SETTINGS_SMS_TEXT")?>
        </td>
        <td class="valueColumn">
            <textarea
                id="message"
                rows="3"
                cols="55"
                name="message"
                placeholder="<?=GetMessage('TAB_DISTRIBUTION_SMS_DEFAULT_TEXT')?>"
                ><?if(strlen($message) > 0):?><?=$message?><?endif;?>
            </textarea>
            <br/>
        </td>
    </tr>



<?else:?>
    <tr>
        <td>
            <?=GetMessage("RESPONSE_STATE")?>
        </td>
        <td>
            <?=$serverResponse['descr']?>
        </td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("RESPONSE_SMS_COUNT")?>
        </td>
        <td>
            <?=$serverResponse['colSendAbonent']?>
        </td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("RESPONSE_SMS_PHONES_LIST")?>
        </td>
        <td>
            <select multiple>
                <?foreach ($phoneNameList as $phone):?>
                    <option key="<?=$phone?>"><?=$phone?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("RESPONSE_PRICE_FOR_ONE")?>
        </td>
        <td>
            <?=$serverResponse['price']." ".GetMessage("DELIVERY_RUB")?>
        </td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("RESPONSE_DELIVERY_PRICE")?>
        </td>
        <td>
            <?=$serverResponse['priceOfSending']." ".GetMessage("DELIVERY_RUB")?>
        </td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("RESPONSE_DELIVERY_ID")?>
        </td>
        <td>
            <?=$serverResponse['smsid']?>
        </td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("RESPONSE_CREDIT")?>
        </td>
        <td>
            <?=$profileBalance?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input type="button" name="NextDelivery" value="<?=GetMessage("NEXT_DELIVERY")?>" title="<?=GetMessage("NEXT_DELIVERY")?>" onclick="window.location='<?=$APPLICATION->GetCurPage()?>'">
        </td>
    </tr>

<?endif?>


<?
$tabControl->Buttons();
?>
<input type="submit" value="<?=GetMessage("SUBMIT_BUTTON")?>" name="apply">
<?
$tabControl->End();
?>
</form>

<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
