<?

$module_id = "bizsolutions.sms";

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$siteList = array();
$rsSites = CSite::GetList($by="sort", $order="asc", Array());
while($arRes = $rsSites->GetNext())
{
	$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
}
$siteCount = count($siteList);

$keySiteList = array();

$APPLICATION->SetAdditionalCSS('/bitrix/js/'.$module_id.'/css/styles.css');

global $SMSBiz;

$SMSBIZ_RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($SMSBIZ_RIGHT >="R"):

	CModule::IncludeModule("bizsolutions.sms");

	$aTabs = array();
	$aTabs[] = array("DIV" => "edit0", "TAB" => GetMessage("TAB_MAIN_NAME"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("TAB_MAIN_TITLE"));
	$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("TAB_EVENTS_NAME"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("TAB_EVENTS_TITLE"));
//	$aTabs[] = array("DIV" => "edit2", "TAB" => GetMessage("TAB_DISTRIBUTION_NAME"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("TAB_DISTRIBUTION_TITLE"));

	$tabControl = new CAdminTabControl("tabControl", $aTabs);

    if(strlen($RestoreDefaults) > 0) {
        COption::RemoveOption("bizsolutions.sms");
        $APPLICATION->DelGroupRight("bizsolutions.sms");
    }

    $login = false;
    $password = false;
    if ($REQUEST_METHOD=="POST" && strlen($Update.$Apply) > 0 && $SMSBIZ_RIGHT >= "W" && check_bitrix_sessid()) {
        $login = $_REQUEST["LOGIN"];
        $password = $_REQUEST["PASSWORD"];

        if ($login != COption::GetOptionString("bizsolutions.sms", "LOGIN") && strlen($RestoreDefaults) == 0) {
            COption::SetOptionString("bizsolutions.sms", "LOGIN", $login);
        }
        if ($password != COption::GetOptionString("bizsolutions.sms", "PASSWORD")&& strlen($RestoreDefaults) == 0) {
            COption::SetOptionString("bizsolutions.sms", "PASSWORD", $password);
        }
    } else {
        $login = COption::GetOptionString("bizsolutions.sms", "LOGIN");
        $password = COption::GetOptionString("bizsolutions.sms", "PASSWORD");
    }


    $SMSBiz->setLogin($login);
    $SMSBiz->setPassword($password);

    $balance = $SMSBiz->balance();

    // Get list of sources from profile
    $sourceArray = array();
    if ($balance) {
        $orgInfoString = $SMSBiz->getProfileOrgInfo();
        if ($orgInfoString) {
            $orgInfo = json_decode($orgInfoString, true);
            foreach($orgInfo["source"] as $source) {
                $sourceArray[$source] = $source;
            }
        }
    }



    // Get list of user fields with "/(PERSONAL|WORK|UF)/" mask
    $arUserPhoneField = $SMSBiz->getUserPhoneFields();

    $arMainOptions = array(
        array("LOGIN", GetMessage("TAB_MAIN_LOGIN_LABEL"), "", array("text",35)),
        array("PASSWORD", GetMessage("TAB_MAIN_PASSWORD_LABEL"), "", array("text",35)),
    );

//    $arEventsMainOptions = array();


    $priorityArray = array("HIGH", "MIDDLE", "LOW");

    foreach ($siteList as $site) {



        $keySiteList[$site["ID"]]["SITE"] = $site;

        $keySiteList[$site["ID"]]["arEventsMainOptions"] = array();
        $keySiteList[$site["ID"]]["arEvents"] = array();
        $keySiteList[$site["ID"]]["arEventsOrderStatus"] = array();
        $keySiteList[$site["ID"]]["arEventsAdmin"] = array();
        $keySiteList[$site["ID"]]["arEventsAdminOrderStatus"] = array();
        $keySiteList[$site["ID"]]["taskOptionsCheckboxes"] = array();
		$keySiteList[$site["ID"]]["taskOptionsPriority"] = array();

        if ($orgInfo["source"] != false) {
            $keySiteList[$site["ID"]]["arEventsMainOptions"][] = array(
                "source".":".$site["ID"],
                GetMessage("TAB_EVENTS_SOURCE_LABEL")." (<a href='http://lcab.smsintel.ru/authAs/".$SMSBiz->getLogin()."/".$SMSBiz->getPassword()."/%2Fsources'>".GetMessage("TAB_MAIN_SOURCES_LINK")."</a>)",
                "",
                array("selectbox", $sourceArray)
            );
        }

        if (count($arUserPhoneField) > 1) {
            $keySiteList[$site["ID"]]["arEventsMainOptions"][] = array("userPhoneField".":".$site["ID"], GetMessage("TAB_EVENTS_USER_PHONE_FIELD_LABEL"), "", array("selectbox", $arUserPhoneField));
        }
		
		if (CModule::IncludeModule("tasks")) {
			foreach ($priorityArray as $priority) {
				$keySiteList[$site["ID"]]["taskOptionsPriority"][] = array(
					"TASKS_".$priority.":".$site["ID"],
					"<span class='task_event_".$priority."'>".GetMessage("TAB_EVENTS_".$priority."_LABEL")."</span>",
					"n",
					array("checkbox", "y")
				);
			}
			
		}

        foreach ($SMSBiz->getBitrixEventsArray() as $bitrixEventType) {
            foreach($SMSBiz->getBitrixModuleArray() as $bitrixModule) {
                if (CModule::IncludeModule($bitrixModule)) {
                    if (stristr($bitrixEventType, $bitrixModule)) {
                        // FOR CUSTOMER SECTION INTERFACE
                        // set names for variable and option
                        $variableName = "IS_EVENT_".$bitrixEventType;

                        if ($bitrixModule == "tasks") {
							
							$eventMessageTemplateId = false;
							$eventName = "SMSBiz_EVENT_".$bitrixEventType;
							$dbEvent = CEventMessage::GetList($b="ID", $order="ASC", array("TYPE_ID" => $eventName));
							while($eventItem = $dbEvent->Fetch()) {
								$eventMessageTemplateId = $eventItem["ID"];
							}
							$linkToEdit = "/bitrix/admin/message_edit.php?ID=".$eventMessageTemplateId;
						
							if ($eventMessageTemplateId) {
								$keySiteList[$site["ID"]]["taskOptionsCheckboxes"][] = array(
									$variableName.":".$site["ID"],
									// "<span class='task_event'>".GetMessage("TAB_EVENTS_".$variableName."_LABEL")."</span>",
									(
										GetMessage("TAB_EVENTS_".$variableName."_LABEL")
										."<a title='".GetMessage("TAB_EVENTS_TITLE_MESSAGE_EDIT_LINK")."' class='message-edit-link' href='/bitrix/admin/message_edit.php?ID=".$eventMessageTemplateId."'>edit</a>"
									),
									"n",
									array("checkbox", "y")
								);
							}
                            
                        } else {
                            // get event message template id
                            $eventName = "SMSBiz_EVENT_".$bitrixEventType;
                            $dbEvent = CEventMessage::GetList($b="ID", $order="ASC", array("TYPE_ID" => $eventName));
                            while($eventItem = $dbEvent->Fetch()) {
                                $eventMessageTemplateId = $eventItem["ID"];
                            }

                            // check value in options
                            // add checkbox to interface
                            $keySiteList[$site["ID"]]["arEvents"][] = array(
                                $variableName.":".$site["ID"],
                                (
                                    GetMessage("TAB_EVENTS_".$variableName."_LABEL")
                                    ."<a title='".GetMessage("TAB_EVENTS_TITLE_MESSAGE_EDIT_LINK")."' class='message-edit-link' href='/bitrix/admin/message_edit.php?ID=".$eventMessageTemplateId."'>edit</a>"
                                ),
                                "n",
                                array("checkbox", "y")
                            );

                            //FOR ADMIN SECTION INTERFACE
                            // set names for variable and option
                            $variableName = "IS_EVENT_ADMIN_".$bitrixEventType;
                            $variableLabelName = "IS_EVENT_".$bitrixEventType;
                            // check value in options
                            ${$variableName} = COption::GetOptionString('bizsolutions.sms', $variableName.":".$site["ID"], false);

                            // get event message template id
                            $eventName = "SMSBiz_EVENT_ADMIN_".$bitrixEventType;
                            $dbEvent = CEventMessage::GetList($b="ID", $order="ASC", array("TYPE_ID" => $eventName));
                            while($eventItem = $dbEvent->Fetch()) {
                                $eventMessageTemplateId = $eventItem["ID"];
                            }
                            // add checkbox to interface
                            $keySiteList[$site["ID"]]["arEventsAdmin"][] = array(
                                $variableName.":".$site["ID"],
                                (
                                    GetMessage("TAB_EVENTS_".$variableLabelName."_LABEL")
                                    ."<a title='".GetMessage("TAB_EVENTS_TITLE_MESSAGE_EDIT_LINK")."' class='message-edit-link' href='/bitrix/admin/message_edit.php?ID=".$eventMessageTemplateId."'>edit</a>"
                                ),
                                "n",
                                array("checkbox", "y")
                            );

                        }
                    }
                }
            }
        }
		


        if (CModule::IncludeModule("sale")) {
            $statusArray = $SMSBiz->getEventOrderStatusArray();
            foreach ($statusArray as $status) {
                // FOR CUSTOMER SECTION INTERFACE
                // set names for variable and option
                $variableName = "IS_EVENT_SALE_STATUS_CHANGED_".$status["ID"];
                // check value in options
                ${$variableName} = COption::GetOptionString('bizsolutions.sms', $variableName.":".$site["ID"], false);

                // get event message template id
                $eventName = "SMSBiz_EVENT_SALE_STATUS_CHANGED_".$status["ID"];
                $dbEvent = CEventMessage::GetList($b="ID", $order="ASC", array("TYPE_ID" => $eventName));
                while($eventItem = $dbEvent->Fetch()) {
                    $eventMessageTemplateId = $eventItem["ID"];
                }

                // add checkbox to interface
                $keySiteList[$site["ID"]]["arEventsOrderStatus"][] = array(
                    $variableName.":".$site["ID"],
                    (
                        $status["NAME"]
                        ."<a title='".GetMessage("TAB_EVENTS_TITLE_MESSAGE_EDIT_LINK")."' class='message-edit-link' href='/bitrix/admin/message_edit.php?ID=".$eventMessageTemplateId."'>edit</a>"
                    ),
                    "n",
                    array("checkbox", "y")
                );

                //FOR ADMIN SECTION INTERFACE
                // set names for variable and option
                $variableName = "IS_EVENT_ADMIN_SALE_STATUS_CHANGED_".$status["ID"];
                // check value in options
                ${$variableName} = COption::GetOptionString('bizsolutions.sms', $variableName.":".$site["ID"], false);

                // get event message template id
                $eventName = "SMSBiz_EVENT_ADMIN_SALE_STATUS_CHANGED_".$status["ID"];
                $dbEvent = CEventMessage::GetList($b="ID", $order="ASC", array("TYPE_ID" => $eventName));
                while($eventItem = $dbEvent->Fetch()) {
                    $eventMessageTemplateId = $eventItem["ID"];
                }

                // add checkbox to interface
                $keySiteList[$site["ID"]]["arEventsAdminOrderStatus"][] = array(
                    $variableName.":".$site["ID"],
                    (
                        $status["NAME"]
                        ."<a title='".GetMessage("TAB_EVENTS_TITLE_MESSAGE_EDIT_LINK")."' class='message-edit-link' href='/bitrix/admin/message_edit.php?ID=".$eventMessageTemplateId."'>edit</a>"
                    ),
                    "n",
                    array("checkbox", "y")
                );
            }
        }
    }

    $errorMsg = "";
    if($REQUEST_METHOD=="POST" && strlen($Update.$Apply) > 0 && $SMSBIZ_RIGHT >= "W" && check_bitrix_sessid()) {

        if ((strlen(trim($login)) < 1) || (strlen(trim($password)) < 1)) {
            $errorMsg .= GetMessage("TAB_EVENTS_ERROR_ENTER_LOGIN_PASSWORD");
        }

        if (((strlen(trim($login)) > 0) || (strlen(trim($password)) > 0)) && !$balance) {
            $errorMsg = GetMessage("TAB_EVENTS_ERROR_PAIR_LOGIN_PASSWORD_NOT_FOUND");
        }



		foreach($siteList as $val) {

            $userCheckBoxSelected = false;
            $adminCheckBoxSelected = false;
			$taskCheckboxSelected = false;
			$priorityCheckBoxSelected = false;
			
			foreach($priorityArray as $priority) {
				if ($_REQUEST["TASKS_".$priority.":".$val["ID"]] == "Y") {
					$priorityCheckBoxSelected = true;
				}
			}
			
			foreach ($SMSBiz->getBitrixEventsArray() as $bitrixEventType) {
				if ($_REQUEST["IS_EVENT_".$bitrixEventType.":".$val["ID"]] == "Y") {
					$userCheckBoxSelected = true;
					if (strpos($bitrixEventType, "TASKS_") === 0) {
						$taskCheckboxSelected = true;
					}
				}
			}
			if (CModule::IncludeModule("sale")) {
				$statusArray = $SMSBiz->getEventOrderStatusArray();
				foreach ($statusArray as $status) {
					if ($_REQUEST["IS_EVENT_SALE_STATUS_CHANGED_".$status["ID"].":".$val["ID"]] == "Y") {
						$userCheckBoxSelected = true;
					}
				}
			}

			foreach ($SMSBiz->getBitrixEventsArray() as $bitrixEventType) {
				if ($_REQUEST["IS_EVENT_ADMIN_".$bitrixEventType.":".$val["ID"]] == "Y") {
					$adminCheckBoxSelected = true;
				}
			}
			if (CModule::IncludeModule("sale")) {
				$statusArray = $SMSBiz->getEventOrderStatusArray();
				foreach ($statusArray as $status) {
					if ($_REQUEST["IS_EVENT_ADMIN_SALE_STATUS_CHANGED_".$status["ID"].":".$val["ID"]] == "Y") {
						$adminCheckBoxSelected = true;
					}
				}
			}
			
			if ($taskCheckboxSelected && !$priorityCheckBoxSelected) {
				$errorMsg .= GetMessage("TAB_EVENTS_ERROR_TASKS_PRIOR");
			}
			
			if (!$taskCheckboxSelected && $priorityCheckBoxSelected) {
				$errorMsg .= GetMessage("TAB_EVENTS_ERROR_TASKS_TYPES");
			}

			if ($userCheckBoxSelected) {
				if ($_REQUEST["userPhoneField".":".$val["ID"]] == "blank") {
					$errorMsg .= str_replace("#SITE_ID#", "[".$val["ID"]."] ".($val["NAME"]), GetMessage("TAB_EVENTS_ERROR_USER_PHONE_FIELD_IS_EMPTY"));
				}
			}

			if ($adminCheckBoxSelected) {
				if (strlen(trim($_REQUEST["adminPhones".":".$val["ID"]])) == 0) {
                    $errorMsg .= str_replace("#SITE_ID#", "[".$val["ID"]."] ".($val["NAME"]), GetMessage("TAB_EVENTS_ERROR_ADMIN_PHONES_IS_EMPTY"));
				}
			}
		}
		
		
        if (strlen(trim($errorMsg)) == 0) {
			foreach($siteList as $site) {
                $allInterfaceOptions = array();

                $allInterfaceOptions = array_merge(
                    $arMainOptions,
                    $keySiteList[$site["ID"]]["arEventsMainOptions"],
                    $keySiteList[$site["ID"]]["arEvents"],
                    $keySiteList[$site["ID"]]["arEventsOrderStatus"],
                    $keySiteList[$site["ID"]]["arEventsAdmin"],
                    $keySiteList[$site["ID"]]["arEventsAdminOrderStatus"],
                    $keySiteList[$site["ID"]]["taskOptionsCheckboxes"],
					$keySiteList[$site["ID"]]["taskOptionsPriority"]
                );

                foreach($allInterfaceOptions as $arOption)
                {
                    $name = $arOption[0];
                    $val = $_REQUEST[$name];
                    if($arOption[2][0] == "checkbox" && $site != "Y") {
                        $site="N";
                    }
                    COption::SetOptionString("bizsolutions.sms", $name, $val, $arOption[1]);
                }

                $val = $_REQUEST["adminPhones".":".$site["ID"]];
                if ($val && strlen(trim($val)) > 0) {
                    COption::SetOptionString("bizsolutions.sms", "ADMIN_PHONES".":".$site["ID"], $val);
                }
			}
        }
    }
	
	if (CModule::IncludeModule("sale")) {
		foreach($siteList as $val) {
			$keySiteList[$val["ID"]]["adminPhones"] = COption::GetOptionString('bizsolutions.sms', 'ADMIN_PHONES'.":".$val["ID"], "");
		}
    }

    $report = $SMSBiz->reports("1970-01-01", date('Y-m-d', time()));

	$tabControl->Begin();
    if(strlen($errorMsg) != 0) {
        ShowError($errorMsg);
    }
    ?>
        <form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
        <?$tabControl->BeginNextTab();?>
		
            <tr>
                <td colspan="2" valign="top">
                    <a href="/bitrix/admin/bizsolutionsSmsIndex.php"><?=GetMessage("TAB_ALL_SERVICES")?></a>&nbsp;
                    <a href="/bitrix/admin/bizsolutionsSmsSentSmsDelivery.php"><?=GetMessage("TAB_ALL_SERVICES_SMS_DELIVERY")?></a>
                     | <a href="/bitrix/admin/bizsolutionsSmsReport.php"><?=GetMessage("TAB_ALL_SERVICES_REPORTS")?></a>
                </td>
            </tr>

            <?if ($balance):?>
                <tr>
                    <td colspan="2" valign="top">
                        <?echo GetMessage("TAB_MAIN_BALANCE").$balance.GetMessage("DELIVERY_RUB")?>
                        <a href="http://lcab.smsintel.ru/authAs/<?=$SMSBiz->getLogin()?>/<?=$SMSBiz->getPassword()?>/%2Fpay">(<?=GetMessage("TAB_MAIN_TOP_UP_BALANCE")?>)</a>
                    </td>
                </tr>
            <?endif;?>

            <?if (!$balance):?>
                <tr>
                    <td colspan="2" valign="top">
                        <?=GetMessage("TAB_MAIN_REGISTRATION_1")?>
                        <a href="http://lcab.smsintel.ru/?manager=77915&promocode=bitrix2311#regTab"><?=GetMessage("TAB_MAIN_REGISTRATION_2")?></a>
                    </td>
                </tr>
            <?endif;?>

            <tr>
				<td width="50%" class="adm-detail-content-cell-l"><?=GetMessage("TAB_MAIN_LOGIN_LABEL")?></td>
				<td width="50%" class="adm-detail-content-cell-r"><input type="text" size="35" maxlength="255" value="<?=COption::GetOptionString("bizsolutions.sms", "LOGIN")?>" name="LOGIN"></td>
			</tr>
			<tr>
				<td width="50%" class="adm-detail-content-cell-l"><?=GetMessage("TAB_MAIN_PASSWORD_LABEL")?></td>
				<td width="50%" class="adm-detail-content-cell-r"><input type="password" size="35" maxlength="255" value="<?=COption::GetOptionString("bizsolutions.sms", "PASSWORD")?>" name="PASSWORD"></td>
			</tr>

        <? $tabControl->BeginNextTab();?>
			<tr>
				<td colspan="2" valign="top">
					<?
						$aTabs3 = Array();
						foreach($siteList as $val) {
							$aTabs3[] = Array(
								"DIV"=>"options".$val["ID"],
								"TAB" => "[".$val["ID"]."] ".($val["NAME"]),
								"TITLE" => GetMessage("site_title"). "[".$val["ID"]."] ".($val["NAME"])
							);
						}
						$tabControl3 = new CAdminViewTabControl("tabControl3", $aTabs3);
						$tabControl3->Begin();
						
						for ($j = 0; $j < $siteCount; $j++):

						$tabControl3->BeginNextTab();
						?>	
							<table width="100%">
								<tr>
									<td colspan="2" valign="top">
										<a href="/bitrix/admin/bizsolutionsSmsIndex.php"><?=GetMessage("TAB_ALL_SERVICES")?></a>&nbsp;
										<a href="/bitrix/admin/bizsolutionsSmsSentSmsDelivery.php"><?=GetMessage("TAB_ALL_SERVICES_SMS_DELIVERY")?></a>
										| <a href="/bitrix/admin/bizsolutionsSmsReport.php"><?=GetMessage("TAB_ALL_SERVICES_REPORTS")?></a>
									</td>
								</tr>

								<tr class="heading">
									<td colspan="2">
										<?=GetMessage("TAB_EVENTS_MAIN_SECTION_LABEL")?>
									</td>
								</tr>
								<?
								foreach($keySiteList[$siteList[$j]["ID"]]["arEventsMainOptions"] as $arOption) {
									__AdmSettingsDrawRow("bizsolutions.sms", $arOption);
								}
								?>

								<?if (count($keySiteList[$siteList[$j]["ID"]]["taskOptionsCheckboxes"]) > 0):?>
									<tr class="heading">
										<td colspan="2">
											<?=GetMessage("TAB_EVENTS_TASK_SECTION_LABEL")?>
										</td>
									</tr>
																		
									<?
									foreach($keySiteList[$siteList[$j]["ID"]]["taskOptionsPriority"] as $arOption) {
										__AdmSettingsDrawRow("bizsolutions.sms", $arOption);
									}
									?>
									
									<tr>
										<td colspan="2" align="center">
											<?echo BeginNote('align="center"');?>
												<?=GetMessage("TAB_EVENTS_SAME_RESPONCIBLE_AND_CHANGED_BY_IDS")?>
											<?echo EndNote();?>
										</td>
									</tr>
									
									<tr class="subheading">
										<td class="subheading" colspan="2">
											<?=GetMessage("TAB_EVENTS_TYPES")?>
										</td>
									</tr>
																
									<?
									foreach($keySiteList[$siteList[$j]["ID"]]["taskOptionsCheckboxes"] as $arOption) {
										__AdmSettingsDrawRow("bizsolutions.sms", $arOption);
									}
									?>
								<?endif;?>

								<?if (count($keySiteList[$siteList[$j]["ID"]]["arEvents"]) > 0):?>
									<tr class="heading">
										<td colspan="2">
											<?=GetMessage("TAB_EVENTS_EVENTS_SECTION_LABEL")?>
										</td>
									</tr>
									<?
									foreach($keySiteList[$siteList[$j]["ID"]]["arEvents"] as $arOption) {
										__AdmSettingsDrawRow("bizsolutions.sms", $arOption);
									}
									?>
								<?endif;?>

								<?if (count($keySiteList[$siteList[$j]["ID"]]["arEventsOrderStatus"]) > 0):?>
									<tr class="heading">
										<td colspan="2">
											<?=GetMessage("TAB_EVENTS_CHANGE_STATUS_SECTION_LABEL")?>
										</td>
									</tr>
									<?
									foreach($keySiteList[$siteList[$j]["ID"]]["arEventsOrderStatus"] as $arOption) {
										__AdmSettingsDrawRow("bizsolutions.sms", $arOption);
									}
									?>
								<?endif;?>

								<?if (count($keySiteList[$siteList[$j]["ID"]]["arEventsAdmin"]) > 0):?>
									<tr class="heading">
										<td colspan="2">
											<?=GetMessage("TAB_EVENTS_ADMIN_EVENTS_SECTION_LABEL")?>
										</td>
									</tr>
									<tr>
										<td>
											<?=GetMessage("TAB_EVENTS_ADMIN_PHONE")?>
										</td>
										<td>
											<textarea id="adminPhones" rows="3" cols="55" name="adminPhones<?=":".$siteList[$j]["ID"]?>" placeholder="<?=GetMessage('TAB_EVENTS_ADMIN_PHONE_EXAMPLE')?>"><?if(strlen($keySiteList[$siteList[$j]["ID"]]["adminPhones"]) > 0):?><?=$keySiteList[$siteList[$j]["ID"]]["adminPhones"]?><?endif;?></textarea>
										</td>
									</tr>
									<?
									foreach($keySiteList[$siteList[$j]["ID"]]["arEventsAdmin"] as $arOption) {
										__AdmSettingsDrawRow("bizsolutions.sms", $arOption);
									}
									?>
								<?endif;?>

								<?if (count($keySiteList[$siteList[$j]["ID"]]["arEventsAdminOrderStatus"]) > 0):?>
									<tr class="heading">
										<td colspan="2">
											<?=GetMessage("TAB_EVENTS_ADMIN_CHANGE_STATUS_SECTION_LABEL")?>
										</td>
									</tr>
									<?
									foreach($keySiteList[$siteList[$j]["ID"]]["arEventsAdminOrderStatus"] as $arOption) {
										__AdmSettingsDrawRow("bizsolutions.sms", $arOption);
									}
									?>
								<?endif;?>
							</table>
				 
						<?endfor;?>
				</td>
			</tr>
		<?$tabControl3->End();?>
        <? $tabControl->Buttons();?>
            <input 
				<?if ($SMSBIZ_RIGHT<"W") echo "disabled" ?> 
				type="submit" 
				name="Update" 
				value="<?=GetMessage("MAIN_SAVE")?>" 
				title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
            <input 
				<?if ($SMSBIZ_RIGHT<"W") echo "disabled" ?> 
				type="submit" 
				name="Apply" 
				value="<?=GetMessage("MAIN_OPT_APPLY")?>" 
				title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
            <?=bitrix_sessid_post();?>

        <? $tabControl->End(); ?>
    </form>
<?else:?>
    <?=CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING'));?>
<?endif;
?>