<?

class EventsCreator {

    function addEvent($bitrixEventName, $lid) {
        $eventType = new CEventType;
        $eventType->Add(array(
            "LID" => $lid,
            "EVENT_NAME" => "SMSBiz_EVENT_".$bitrixEventName,
            "NAME" => GetMessage("EVENT_".$bitrixEventName."_NAME"),
            "DESCRIPTION" => GetMessage("EVENT_".$bitrixEventName."_DESC"),
        ));
    }

    function addAdminEvent($bitrixEventName, $lid) {
        $this->addEvent("ADMIN_".$bitrixEventName, $lid);
    }

    function addEventMessage($bitrixEventName, $arSites) {
        $bizEventName = "SMSBiz_EVENT_".$bitrixEventName;
        if ($this->isEventExist($bizEventName, $arSites)) {
            $eventMessage = new CEventMessage;
            $eventMessage->Add(array(
                "ACTIVE" => "Y",
                "EVENT_NAME" => $bizEventName,
                "LID" => $arSites,
                "EMAIL_FROM" => "#DEFAULT_PHONE_FROM#",
                "EMAIL_TO" => "#PHONE_TO#",
                "SUBJECT" => GetMessage("EVENT_".$bitrixEventName."_SUBJECT"),
                "MESSAGE" => GetMessage("EVENT_".$bitrixEventName."_MESSAGE"),
                "BODY_TYPE" => "text",
            ));
        }
    }

    function addAdminEventMessage($bitrixEventName, $arSites) {
        $this->addEventMessage("ADMIN_".$bitrixEventName, $arSites);
    }

    function isEventExist($eventName, $lid) {
        $result = false;
        $eventType = new CEventType();
        $dbEventType = $eventType->GetList(
            array(
                "EVENT_NAME" => $eventName,
                "LID" => $lid
            )
        );
        if ($dbEventType->Fetch()) {
            $result = true;
        }
        return $result;
    }

    function addChangeSaleStatusEvent($eventNamePrefix, $eventId, $lid, $statusName) {
        if (!$this->isEventExist($eventNamePrefix."SALE_STATUS_CHANGED_".$eventId, $lid)) {
            $eventType = new CEventType();
            $str = "#ORDER_ID# - ".GetMessage("EVENT_ORDER_ID")."\n";
            $str .= "#ORDER_DATE# - ".GetMessage("EVENT_ORDER_DATE")."\n";
            $str .= "#ORDER_STATUS# - ".GetMessage("EVENT_ORDER_STATUS")."\n";
            $str .= "#PHONE_TO# - ".GetMessage("EVENT_ORDER_PHONE")."\n";
            $str .= "#ORDER_DESCRIPTION# - ".GetMessage("EVENT_STATUS_DESCR")."\n";
            $str .= "#TEXT# - ".GetMessage("EVENT_STATUS_TEXT")."\n";
            $str .= "#SALE_PHONE# - ".GetMessage("EVENT_SALE_PHONE");

            $eventType->Add(
                array(
                    "LID" => $lid,
                    "EVENT_NAME" => $eventNamePrefix."SALE_STATUS_CHANGED_".$eventId,
                    "NAME" => GetMessage("EVENT_CHANGING_STATUS_TO")." ".$statusName,
                    "DESCRIPTION" => $str
                )
            );
        }
    }

    function addChangeSaleStatusEventMessage($eventNamePrefix, $eventId, $lid, $statusName) {
        $eventMessage = new CEventMessage();
        $eventName = $eventNamePrefix."SALE_STATUS_CHANGED_".$eventId;

        $dbEventMessage = $eventMessage->GetList(
            ($b = "sort"),
            ($o = "asc"),
            array(
                "EVENT_NAME" => $eventName,
                "SITE_ID" => $lid
            )
        );
        if (!($arEventMessage = $dbEventMessage->Fetch()))
        {
            $subject = GetMessage("EVENT_STATUS_PHONE_SUBJ");
            $message = GetMessage("EVENT_STATUS_PHONE_BODY1").$statusName."\n";
            $message .= "#ORDER_DESCRIPTION#\n";
            $message .= "#TEXT#";

            $arFields = Array(
                "ACTIVE" => "Y",
                "EVENT_NAME" => $eventName,
                "LID" => $lid,
                "EMAIL_FROM" => "#SALE_PHONE#",
                "EMAIL_TO" => "#PHONE_TO#",
                "SUBJECT" => $subject,
                "MESSAGE" => $message,
                "BODY_TYPE" => "text"
            );
            $eventMessage->Add($arFields);
        }
    }


    function installEvents() {

        $langs = CLanguage::GetList(($b=""), ($o=""));
        while($lang = $langs->Fetch()) {
            $lid = $lang["LID"];
            IncludeModuleLangFile(__FILE__, $lid);

            $arSites = array();
            $sites = CSite::GetList(($b=""), ($o=""), Array("LANGUAGE_ID"=>$lid));
            while ($site = $sites->Fetch()) {
                $arSites[] = $site["LID"];
            }
            foreach ($this->getBitrixModuleArray() as $bitrixModule) {
                if (CModule::IncludeModule($bitrixModule)) {
                    $bitrixEventsArray = $this->getBitrixEventsArray();
                    foreach($bitrixEventsArray as $bitrixEventName) {
                        if (stristr($bitrixEventName, $bitrixModule)) {
                            $this->addEvent($bitrixEventName, $lid);
                            if ($bitrixModule != "tasks") {
                                $this->addAdminEvent($bitrixEventName, $lid);
                            }
                            if(count($arSites) > 0) {
                                $this->addEventMessage($bitrixEventName, $arSites);
                                if ($bitrixModule != "tasks") {
                                    $this->addAdminEventMessage($bitrixEventName, $arSites);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (CModule::IncludeModule("sale")) {
            global $EVENT;
            $dbStatus = CSaleStatus::GetList(
                array($by => $order),
                array(),
                false,
                false,
                array("ID", "SORT", "LID", "NAME", "DESCRIPTION")
            );

            while($arStatus = $dbStatus->Fetch()) {
                $ID = $arStatus["ID"];

                $dbSiteList = CSite::GetList(($b = "sort"), ($o = "asc"));
                while ($arSiteList = $dbSiteList->Fetch()) {
                    $arStatusLang = CSaleStatus::GetLangByID($ID, $arSiteList["LANGUAGE_ID"]);
                    $this->addChangeSaleStatusEvent("SMSBiz_EVENT_", $ID, $arSiteList["LANGUAGE_ID"], $arStatusLang["NAME"]);
                    $this->addChangeSaleStatusEvent("SMSBiz_EVENT_ADMIN_", $ID, $arSiteList["LANGUAGE_ID"], $arStatusLang["NAME"]);
                    $this->addChangeSaleStatusEventMessage("SMSBiz_EVENT_", $ID, $arSiteList["LID"], $arStatusLang["NAME"]);
                    $this->addChangeSaleStatusEventMessage("SMSBiz_EVENT_ADMIN_", $ID, $arSiteList["LID"], $arStatusLang["NAME"]);
                }
            }
        }
    }

    function uninstallEvents() {
        global $DB;
        $eventMessageNamesArray = array();
        $dbStatus = $DB->Query("SELECT * FROM b_sale_status", true);
        $eventType = new CEventType;
        if ($dbStatus) {
            while($statusItem = $dbStatus->Fetch()) {
                $eventType->Delete("SMSBiz_EVENT_SALE_STATUS_CHANGED_".$statusItem["ID"]);
                $eventType->Delete("SMSBiz_EVENT_ADMIN_SALE_STATUS_CHANGED_".$statusItem["ID"]);
                $eventMessageNamesArray["TYPE_ID"] .= " | SMSBiz_EVENT_SALE_STATUS_CHANGED_".$statusItem["ID"];
                $eventMessageNamesArray["TYPE_ID"] .= " | SMSBiz_EVENT_ADMIN_SALE_STATUS_CHANGED_".$statusItem["ID"];
            }
        }

        foreach($this->getBitrixEventsArray() as $bitrixEventType) {
            $eventType->Delete("SMSBiz_EVENT_".$bitrixEventType);
            if (!stristr($bitrixEventType, "tasks")) {
                $eventType->Delete("SMSBiz_EVENT_ADMIN_".$bitrixEventType);
            }
            $eventMessageNamesArray["TYPE_ID"] .= " | SMSBiz_EVENT_".$bitrixEventType;
            if (!stristr($bitrixEventType, "tasks")) {
                $eventMessageNamesArray["TYPE_ID"] .= " | SMSBiz_EVENT_ADMIN_".$bitrixEventType;
            }
        }

        $dbEvent = CEventMessage::GetList($b="ID", $order="ASC", $eventMessageNamesArray);
        $eventMessage = new CEventMessage;
        while($eventItem = $dbEvent->Fetch()) {
            $eventMessage->Delete($eventItem["ID"]);
        }
    }
	
	function installTaskEvents() {

        $langs = CLanguage::GetList(($b=""), ($o=""));
        while($lang = $langs->Fetch()) {
            $lid = $lang["LID"];
            IncludeModuleLangFile(__FILE__, $lid);

            $arSites = array();
            $sites = CSite::GetList(($b=""), ($o=""), Array("LANGUAGE_ID"=>$lid));
            while ($site = $sites->Fetch()) {
                $arSites[] = $site["LID"];
            }
            $bitrixModule = "tasks";
			if (CModule::IncludeModule($bitrixModule)) {
				$bitrixEventsArray = $this->getBitrixEventsArray();
				foreach($bitrixEventsArray as $bitrixEventName) {
					if (stristr($bitrixEventName, $bitrixModule)) {
						$this->addEvent($bitrixEventName, $lid);
						if(count($arSites) > 0) {
							$this->addEventMessage($bitrixEventName, $arSites);
						}
					}
				}
			}
        }
    }
	
	function uninstallTaskEvents() {
        global $DB;
        $eventMessageNamesArray = array();
        $oldTasksEvents = array("TASKS_TASK_ADD", "TASKS_TASK_UPDATE", "TASKS_TASK_DELETE");
        $eventType = new CEventType;
        foreach($oldTasksEvents as $bitrixEventType) {
			if (stristr($bitrixEventType, "tasks")) {
				$arFilter = array(
					"TYPE_ID" => $bitrixEventType,
				);
				$rsET = CEventType::GetList($arFilter);
				if ($rsET->Fetch()) {
					$eventType->Delete("SMSBiz_EVENT_".$bitrixEventType);
					$eventMessageNamesArray["TYPE_ID"] .= " | SMSBiz_EVENT_".$bitrixEventType;
				}
			}
        }
		if (count($eventMessageNamesArray) > 0) {
			$dbEvent = CEventMessage::GetList($b="ID", $order="ASC", $eventMessageNamesArray);
			$eventMessage = new CEventMessage;
			while($eventItem = $dbEvent->Fetch()) {
				$eventMessage->Delete($eventItem["ID"]);
			}
		}
    }
	
	

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
}

?>