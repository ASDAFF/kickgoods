<?

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/classes/general/CSmsBizBase.php");


class CSmsBizEvents extends CSmsBizBase {

    function getBitrixModuleArray() {
        return array(
            "sale",
            "support",
            "subscribe",
            "tasks"
        );
    }

    function getBitrixEventsArray() {
        return array(
            "TICKET_NEW_FOR_TECHSUPPORT",
            "TICKET_CHANGE_FOR_TECHSUPPORT",
            "SUBSCRIBE_CONFIRM",
            "SALE_NEW_ORDER",
            "SALE_ORDER_CANCEL",
            "SALE_ORDER_PAID",
            "SALE_ORDER_DELIVERY",
            "SALE_ORDER_REMIND_PAYMENT",
            // "TASKS_TASK_ADD",
            // "TASKS_TASK_UPDATE",
            // "TASKS_TASK_DELETE",
			"TASKS_TASK_ADD",
			"TASKS_TASK_UPDATE_TITLE",
			"TASKS_TASK_UPDATE_STATUS_2", // change status to "needs revision"
			"TASKS_TASK_UPDATE_STATUS_3", // change status to "in progress"
			"TASKS_TASK_UPDATE_STATUS_4", // change status to "completed"
			"TASKS_TASK_UPDATE_STATUS_5", // change status to "accepted"
			"TASKS_TASK_UPDATE_DEADLINE",
			"TASKS_TASK_UPDATE_PRIORITY",
			"TASKS_TASK_UPDATE_RESPONSIBLE_ID"
        );
    }

    function getUserPhoneFields() {
        $rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>1),array("SELECT"=>array("UF_*")));
        $arUser = $rsUser->Fetch();
        $arUserPhoneField['blank'] = '';
        foreach($arUser as $index => $value) {
            $pattern = '/(PERSONAL|WORK|UF)/';
            if (preg_match($pattern, $index))
            {
                $arUserPhoneField[$index] = $index;
            }
        }
        return $arUserPhoneField;
    }

    function getEventOrderStatusArray() {
        $result = false;
        if (CModule::IncludeModule("sale")) {
            $arStatus = CSaleStatus::GetList(
                array("ID"=> "ASC"),
                array("LID" => "ru"),
                false,
                false,
                array("NAME", "ID")
            );
            $result = array();
            while ($status = $arStatus->GetNext()) {
                $result[$status['ID']] = $status;
            }
        }
        return $result;
    }

    function getEventTemplate($eventType, $site, $from = false)
    {
        $result = false;
        $arFilter = array(
            "ACTIVE"=> "Y",
            "TYPE_ID" => $eventType,
            "FROM" => $from,
            "SITE_ID" => $site
        );

        $dbResult = CEventMessage::GetList($by="site_id", $order="desc", $arFilter);
        if ($text = $dbResult->Fetch()) {
            $result = $text;
        }
        return $result;
    }

    function makeReplacesServiceTextInSmsMessageTemplate($message, $params, $phoneNumber) {
        CModule::IncludeModule("sale");
        $dbOrderProps = CSaleOrderPropsValue::GetList(
            array("SORT" => "ASC"),
            array("ORDER_ID" => $params["ORDER_ID"], "CODE"=>array("NAME", "SURNAME"))
        );
            while ($arOrderProps = $dbOrderProps->GetNext()):
                   $fullname[] = $arOrderProps["VALUE"];
            endwhile;
        $userCity = explode(", ", $params["FULL_ADDRESS"]);
        $userCity = $userCity[1];
        $searchArray = array(
            "#ORDER_DATE#",
            "#ORDER_STATUS#",
            "#ORDER_ID#",
            "#ORDER_DESCRIPTION#",
            "#TEXT#",
            "#FULL_NAME#",
            "#ID#",
            "#PHONE_TO#",
            "#CONFIRM_CODE#",
            "#SUBSCR_SECTION#",
            "#USER_NAME#",
			"#USER_PHONE#",
            "#DATE_SUBSCR#",
            "#PRICE#",
            "#ORDER_CANCEL_DESCRIPTION#",
            "#ORDER_LIST#",
            "#ORDER_USER#",
            "#CRITICAL#",
            "#DATE_TICKET#",
            "#WHAT_CHANGE#",
            "#MESSAGE_BODY#",
            "#USER_CITY#",
        );

        $replaceArray = array(
            $params["ORDER_DATE"],
            $params["ORDER_STATUS"],
            $params["ORDER_ID"],
            $params["ORDER_DESCRIPTION"],
            $params["TEXT"],
            $fullname[0]." ".$fullname[1],

            $params["ID"],
            $phoneNumber,
            $params["CONFIRM_CODE"],
            "",
            $params["USER_NAME"],
			$params["USER_PHONE"],
            $params["DATE_SUBSCR"],
            $params["PRICE"],
            $params["ORDER_CANCEL_DESCRIPTION"],
            $params["ORDER_LIST"],
            $params["ORDER_USER"],
            $params["CRITICALITY"],
            $params["DATE_CREATE"],
            $params["WHAT_CHANGE"],
            $params["MESSAGE_BODY"],
            $userCity,
        );
        AddMessage2Log($params);
        $result = str_replace($searchArray, $replaceArray, $message);
        return $result;
    }
	
	function makeReplacesServiceTextInTaskSmsMessageTemplate($message, $params) {
        $searchArray = array(
            "#TITLE#",
			"#CREATED_BY#",
			"#RESPONSIBLE#",
			"#DEADLINE#",
			"#PRIORITY#"
        );

        $replaceArray = array(
            $params["TITLE"],
			$params["CREATED_BY"],
			$params["RESPONSIBLE"],
			$params["DEADLINE"],
			$params["PRIORITY"]
        );
        $result = str_replace($searchArray, $replaceArray, $message);
        return $result;
    }

    public function getCustomerPhoneForCurrentOrder($orderId) {
		$result = false;
		$saleOrderId = false;
		$db_sales = CSaleOrder::GetList(false, Array("ACCOUNT_NUMBER" => $orderId));
		if ($ar_sales = $db_sales->Fetch()){
			$saleOrderId = $ar_sales["ID"];
		}
		if ($saleOrderId) {
			$dbProp = CSaleOrderPropsValue::GetList(
				array("SORT" => "ASC"),
				array(
					"ORDER_ID" => $saleOrderId,
					"CODE" => "PHONE"
				)
			);
			if ($arrOrderProps = $dbProp->Fetch()) {
				$result = $arrOrderProps["VALUE"];
			}
		}
		return $result;
	}

    function getUserPhone($eventName, $params, $userPhoneField) {
        $resultArray = array();

        $filter = Array("ACTIVE" => "Y");

        if ($eventName == "TICKET_NEW_FOR_TECHSUPPORT" || $eventName == "TICKET_CHANGE_FOR_TECHSUPPORT") {
            $filter["ID"] = $params["CREATED_USER_ID"];
        } else {
            if($params["RESPONSIBLE_USER_ID"] == '') {
                $filter["EMAIL"] = $params["EMAIL"];
            } else {
                $filter["ID"] = $params["RESPONSIBLE_USER_ID"];
            }
        }


        $rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
        while ($user = $rsUsers->Fetch()) {
            $resultArray[] = $user[$userPhoneField];
        }
        return $resultArray;
    }


    public function Events($eventName, $site, &$params) {
        global $SMSBiz;
        $source = COption::GetOptionString("bizsolutions.sms", "source".":".$site);
        $adminPhones = COption::GetOptionString("bizsolutions.sms", "ADMIN_PHONES".":".$site);
        $userPhoneField = COption::GetOptionString("bizsolutions.sms", "userPhoneField".":".$site);
        $adminPhonesArray = explode(';', $adminPhones);

        $isEventOptionSendOn = COption::GetOptionString("bizsolutions.sms", "IS_EVENT_".$eventName.":".$site);
        $IsAdminEventOptionSendOn = COption::GetOptionString("bizsolutions.sms", "IS_EVENT_ADMIN_".$eventName.":".$site);

        $SMSBiz->setLogin(COption::GetOptionString("bizsolutions.sms", "LOGIN"));
        $SMSBiz->setPassword(COption::GetOptionString("bizsolutions.sms", "PASSWORD"));

        $paramString = "";
        foreach($params as $key => $param) {
            $paramString .= $key." = ".$param." \n";
        }
		
		$phoneTo = false;
		if ($params["ORDER_ID"]) {
			$phoneTo = array($SMSBiz->getCustomerPhoneForCurrentOrder($params["ORDER_ID"]));
		} else {
			$phoneTo = $SMSBiz->getUserPhone($eventName, $params, $userPhoneField);
		}
		
		$params["USER_PHONE"] = $phoneTo[0];

        if ($isEventOptionSendOn == "Y") {
            $messageItem = $SMSBiz->GetEventTemplate("SMSBiz_EVENT_".$eventName, $site);
            $message = $messageItem["MESSAGE"];
            $message = $SMSBiz->makeReplacesServiceTextInSmsMessageTemplate($message, $params, "89000000000");
 
            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "SEND_SMSBiz",
                "MODULE_ID" => "bizsolutions.sms",
                "ITEM_ID" => "",
                "DESCRIPTION" => "Ready for send sms for event: ".$eventName." with message: ".$message." for number: ".$phoneTo[0]." Params: ".$paramString,
            ));

            $serverResponse = $SMSBiz->send(
                array(
                    "source" =>$source,
                    "text" => $message
                ),
                $phoneTo
            );
        }

        if ($IsAdminEventOptionSendOn == "Y") {
            $messageItem = $SMSBiz->GetEventTemplate("SMSBiz_EVENT_ADMIN_".$eventName, $site);
            $message = $messageItem["MESSAGE"];
            $message = $SMSBiz->makeReplacesServiceTextInSmsMessageTemplate($message, $params, "89000000000");

            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "SEND_SMSBiz",
                "MODULE_ID" => "bizsolutions.sms",
                "ITEM_ID" => "",
                "DESCRIPTION" => "Ready for send sms for ADMIN event: ".$eventName." with message: ".$message." for number: ".$adminPhonesArray[0]." Params: ".$paramString,
            ));

            $serverResponse = $SMSBiz->send(
                array(
                    "source" =>$source,
                    "text" => $message
                ),
                $adminPhonesArray
            );
        }
    }

    private function getTaskById($id) {
        $result = false;
        if (CModule::IncludeModule("tasks")) {

            $res = CTasks::GetList(
                Array("TITLE" => "ASC"),
                Array("ID" => $id)
            );

            $result = $res->GetNext();

        }
        return $result;
    }
	
	public function TaskActionAdd ($id, $params = array()) {
		global $SMSBiz;
		$SMSBiz->TaskAction("TASKS_TASK_ADD", $id, $params);
	}
	
	public function TaskActionUpdate ($id, $params = array()) {
		global $SMSBiz;	
		$SMSBiz->TaskAction("TASKS_TASK_UPDATE", $id, $params);
	}
	
	public function TaskActionDelete ($id, $params = array()) {
		// global $SMSBiz;
		// $SMSBiz->TaskAction("TASKS_TASK_DELETE", $id, $params);
	}
	
	function decodePriority($priorityId) {
		$priorityArray = array(
            "0" => "LOW",
            "1" => "MIDDLE",
            "2" => "HIGH",
        );
		return $priorityArray[$priorityId];
	}

    public function TaskAction ($eventName, $id, $params) {
        global $SMSBiz;
		
		$SMSBiz->setLogin(COption::GetOptionString("bizsolutions.sms", "LOGIN"));
        $SMSBiz->setPassword(COption::GetOptionString("bizsolutions.sms", "PASSWORD"));

        $task = false;
        
		$newEventName = false;
		$newParams = array();
		$recipientId = false;
        if ($eventName == "TASKS_TASK_UPDATE") {
            
            $task = $SMSBiz->getTaskById($id);
			
			reset($params);
			$firstKey = key($params);

			if ($firstKey == "STATUS") {
				if ($params["STATUS"] != "2" || ($params["META:PREV_FIELDS"]["STATUS"] == "4" && $params["STATUS"] == "2")) {
					$newEventName = "TASKS_TASK_UPDATE_STATUS_".$params["STATUS"];
					$newParams["TITLE"] = $params["META:PREV_FIELDS"]["TITLE"];
					$newParams["CREATED_BY"] = $params["META:PREV_FIELDS"]["CREATED_BY_NAME"]." ".$params["META:PREV_FIELDS"]["CREATED_BY_LAST_NAME"];
					$newParams["RESPONSIBLE"] = $params["META:PREV_FIELDS"]["RESPONSIBLE_NAME"]." ".$params["META:PREV_FIELDS"]["RESPONSIBLE_LAST_NAME"];
					$newParams["DEADLINE"] = $params["META:PREV_FIELDS"]["DEADLINE"];
					$newParams["PRIORITY"] = $SMSBiz->decodePriority($params["META:PREV_FIELDS"]["PRIORITY"]);
					
					if ($params["STATUS"] == "2" || $params["STATUS"] == "5") {
						$recipientId = $params["META:PREV_FIELDS"]["RESPONSIBLE_ID"];
					} else if ($params["STATUS"] == "3" || $params["STATUS"] == "4") {
						$recipientId = $params["META:PREV_FIELDS"]["CREATED_BY"];
					}
				}
			} else if ($firstKey == "TITLE") {
				$newEventName = "TASKS_TASK_UPDATE_TITLE";
				$newParams["TITLE"] = $params["TITLE"];
				$newParams["CREATED_BY"] = $params["META:PREV_FIELDS"]["CREATED_BY_NAME"]." ".$params["META:PREV_FIELDS"]["CREATED_BY_LAST_NAME"];
				$newParams["RESPONSIBLE"] = $params["META:PREV_FIELDS"]["RESPONSIBLE_NAME"]." ".$params["META:PREV_FIELDS"]["RESPONSIBLE_LAST_NAME"];
				$newParams["DEADLINE"] = $params["META:PREV_FIELDS"]["DEADLINE"];
				$newParams["PRIORITY"] = $SMSBiz->decodePriority($params["META:PREV_FIELDS"]["PRIORITY"]);
				
				$recipientId = $params["META:PREV_FIELDS"]["RESPONSIBLE_ID"];
			} else if ($firstKey == "PRIORITY") {
				$newEventName = "TASKS_TASK_UPDATE_PRIORITY";
				$newParams["TITLE"] = $params["META:PREV_FIELDS"]["TITLE"];
				$newParams["CREATED_BY"] = $params["META:PREV_FIELDS"]["CREATED_BY_NAME"]." ".$params["META:PREV_FIELDS"]["CREATED_BY_LAST_NAME"];
				$newParams["RESPONSIBLE"] = $params["META:PREV_FIELDS"]["RESPONSIBLE_NAME"]." ".$params["META:PREV_FIELDS"]["RESPONSIBLE_LAST_NAME"];
				$newParams["DEADLINE"] = $params["META:PREV_FIELDS"]["DEADLINE"];
				$newParams["PRIORITY"] = $SMSBiz->decodePriority($params["PRIORITY"]);
				
				$task["PRIORITY"] = $params["PRIORITY"];
				$recipientId = $params["META:PREV_FIELDS"]["RESPONSIBLE_ID"];
			} else if ($firstKey == "RESPONSIBLE_ID") {
				$newEventName = "TASKS_TASK_UPDATE_RESPONSIBLE_ID";
				$newParams["TITLE"] = $params["META:PREV_FIELDS"]["TITLE"];
				$newParams["CREATED_BY"] = $params["META:PREV_FIELDS"]["CREATED_BY_NAME"]." ".$params["META:PREV_FIELDS"]["CREATED_BY_LAST_NAME"];
				$dbUser = CUser::GetList(($by="id"), ($order="desc"), array("ID" => $params["RESPONSIBLE_ID"]));
				while ($user = $dbUser->Fetch()) {
					$name = $user["NAME"]." ".$user["LAST_NAME"];
				}
				$newParams["RESPONSIBLE"] = $name;
				$newParams["DEADLINE"] = $params["META:PREV_FIELDS"]["DEADLINE"];
				$newParams["PRIORITY"] = $SMSBiz->decodePriority($params["META:PREV_FIELDS"]["PRIORITY"]);
				
				$recipientId = $params["RESPONSIBLE_ID"];
			} else if ($firstKey == "DEADLINE") {
				$newEventName = "TASKS_TASK_UPDATE_DEADLINE";
				$newParams["TITLE"] = $params["META:PREV_FIELDS"]["TITLE"];
				$newParams["CREATED_BY"] = $params["META:PREV_FIELDS"]["CREATED_BY_NAME"]." ".$params["META:PREV_FIELDS"]["CREATED_BY_LAST_NAME"];
				$newParams["RESPONSIBLE"] = $params["META:PREV_FIELDS"]["RESPONSIBLE_NAME"]." ".$params["META:PREV_FIELDS"]["RESPONSIBLE_LAST_NAME"];
				if ($params["DEADLINE"]) {
					$newParams["DEADLINE"] = $params["DEADLINE"];
				} else {
					$newParams["DEADLINE"] = "null";
				}
				$newParams["PRIORITY"] = $SMSBiz->decodePriority($params["META:PREV_FIELDS"]["PRIORITY"]);
				
				$recipientId = $params["META:PREV_FIELDS"]["RESPONSIBLE_ID"];
			}

        } elseif ($eventName == "TASKS_TASK_ADD") {
            // add
			
			$newEventName = "TASKS_TASK_ADD";
			$newParams["TITLE"] = $params["TITLE"];
			
			$dbUser = CUser::GetList(($by="id"), ($order="desc"), array("ID" => $params["CREATED_BY"]));
			while ($user = $dbUser->Fetch()) {
				$name = $user["NAME"]." ".$user["LAST_NAME"];
			}
			$newParams["CREATED_BY"] = $name;
			
			$dbUser = CUser::GetList(($by="id"), ($order="desc"), array("ID" => $params["RESPONSIBLE_ID"]));
			while ($user = $dbUser->Fetch()) {
				$name = $user["NAME"]." ".$user["LAST_NAME"];
			}
			$newParams["RESPONSIBLE"] = $name;
			if ($params["DEADLINE"]) {
				
				$newParams["DEADLINE"] = $params["DEADLINE"];
			} else {
				
				$newParams["DEADLINE"] = "null";
			}
			$newParams["PRIORITY"] = $SMSBiz->decodePriority($params["PRIORITY"]);
            
			$task = array();
			$task["ID"] = $id;
			$task["TITLE"] = $params["TITLE"];
			$task["PRIORITY"] = $params["PRIORITY"];
			$task["CREATED_BY"] = $params["CREATED_BY"];
			$task["RESPONSIBLE_ID"] = $params["RESPONSIBLE_ID"];
			$task["SITE_ID"] = $params["SITE_ID"];
			$recipientId = $params["RESPONSIBLE_ID"];
        }
		
		$siteId = false;
		$changedBy = false;
		if ($eventName == "TASKS_TASK_ADD" || $eventName == "TASKS_TASK_DELETE") {
			$siteId = $params["SITE_ID"];
			$changedBy = $params["CHANGED_BY"];
		} elseif ($eventName == "TASKS_TASK_UPDATE") {
			$siteId = $params["META:PREV_FIELDS"]["SITE_ID"];
			$changedBy = $params["META:PREV_FIELDS"]["CHANGED_BY"];
		}

        $priorityArray = array(
            "0" => "LOW",
            "1" => "MIDDLE",
            "2" => "HIGH",
        );
		
		$settingPriorityName = "TASKS_".$priorityArray[$task["PRIORITY"]].":".$siteId;
		$isEventPriorityOptionSendOn = COption::GetOptionString("bizsolutions.sms", $settingPriorityName);
		
        $settingName = "IS_EVENT_".$newEventName.":".$siteId;
        $isEventOptionSendOn = COption::GetOptionString("bizsolutions.sms", $settingName);


        // CEventLog::Add(array(
            // "SEVERITY" => "SECURITY",
            // "AUDIT_TYPE_ID" => "SEND_SMSBiz",
            // "MODULE_ID" => "bizsolutions.sms",
            // "ITEM_ID" => "",
            // "DESCRIPTION" => "Ready to send task SMS. Setting name: ".$settingName." ID: ".$id." GUID: (".$params["GUID"].") Params: ".json_encode($params),
        // ));
		
		$isNotEqualsRecipientAndChangedBy = true;
		if ($newEventName != "TASKS_TASK_UPDATE_STATUS_3" || $newEventName != "TASKS_TASK_UPDATE_STATUS_4") {
			$isEqualsRecipientAndChangedBy = ($task["RESPONSIBLE_ID"] != $changedBy);
		}

        if ($isEventOptionSendOn == "Y" && $isEventPriorityOptionSendOn == "Y" && $isNotEqualsRecipientAndChangedBy) {
			
            $messageItem = $SMSBiz->GetEventTemplate("SMSBiz_EVENT_".$newEventName, $task["SITE_ID"]);
            $message = $messageItem["MESSAGE"];
			$message = html_entity_decode($SMSBiz->makeReplacesServiceTextInTaskSmsMessageTemplate($message, $newParams));
            $source = COption::GetOptionString("bizsolutions.sms", "source".":".$siteId);
            $userPhoneField = COption::GetOptionString("bizsolutions.sms", "userPhoneField".":".$siteId);

            $phoneArray = array();
            $dbUser = CUser::GetList(($by="id"), ($order="desc"), array("ID" => $recipientId));
            while ($user = $dbUser->Fetch()) {
                $phoneArray[] = $user[$userPhoneField];
            }

            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "SEND_SMSBiz",
                "MODULE_ID" => "bizsolutions.sms",
                "ITEM_ID" => "",
                "DESCRIPTION" => "Ready to send task SMS for event ".$settingName." Phone: ".$phoneArray[0]." Message: ".$message." source: ".$source,
            ));


            $serverResponse = $SMSBiz->send(
                array(
                    "source" => $source,
                    "text" => $message
                ),
                array($phoneArray[0])
            );
        }
    }
}

?>