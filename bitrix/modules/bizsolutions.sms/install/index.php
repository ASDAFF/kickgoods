<?
IncludeModuleLangFile(__FILE__);

if(class_exists("bizsolutions_sms")) return;

class bizsolutions_sms extends CModule
{
    var $MODULE_ID = "bizsolutions.sms";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    var $errors;

    function bizsolutions_sms()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->PARTNER_NAME = GetMessage("COMPANY_NAME");
            $this->PARTNER_URI = "http://bizsolutions.ru";
        }
        else {
            $this->MODULE_VERSION = SMS_MODULE_VERSION;
            $this->MODULE_VERSION_DATE = SMS_MODULE_VERSION_DATE;
        }

        $this->MODULE_NAME = GetMessage("SMS_MODULE_INSTALL_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("SMS_MODULE_INSTALL_DESCRIPTION");
    }

    function DoInstall() {

        global $APPLICATION;
        global $DOCUMENT_ROOT;
        $POST_RIGHT = $APPLICATION->GetGroupRight("bizsolutions.sms");

        if($POST_RIGHT == "W") {

            RegisterModule($this->MODULE_ID);
            CModule::IncludeModule($this->MODULE_ID);

            $this->InstallEvents();
            $this->InstallFiles();
        }
    }

    function DoUninstall() {
        global $APPLICATION;

        $POST_RIGHT = $APPLICATION->GetGroupRight("bizsolutions.sms");
        if($POST_RIGHT == "W")
        {
            UnRegisterModule($this->MODULE_ID);
			COption::RemoveOption("bizsolutions.sms");
            $this->UnInstallEvents();
            $this->UnInstallFiles();
        }
    }


    function InstallEvents() {
        RegisterModuleDependences("main", "OnBeforeEventAdd", "bizsolutions.sms", "CSmsBizEvents", "Events");

        if (COption::GetOptionString("main", "vendor") == '1c_bitrix_portal')
        {
            RegisterModuleDependences('tasks', 'OnTaskAdd', 'bizsolutions.sms', 'CSmsBizEvents', 'TaskActionAdd', 10001);
            RegisterModuleDependences('tasks', 'OnTaskUpdate', 'bizsolutions.sms', 'CSmsBizEvents', 'TaskActionUpdate', 10001);
            RegisterModuleDependences('tasks', 'OnBeforeTaskDelete', 'bizsolutions.sms', 'CSmsBizEvents', 'TaskActionDelete', 10001);
        }

        include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/install/events.php");

        $eventsCreator = new EventsCreator();
        $eventsCreator->installEvents();
        return true;
    }

    function UnInstallEvents() {
        UnRegisterModuleDependences("main", "OnBeforeEventAdd", "bizsolutions.sms", "CSmsBizEvents", "Events");

        if (COption::GetOptionString("main", "vendor") == '1c_bitrix_portal')
        {
            UnRegisterModuleDependences('tasks', 'OnTaskAdd', 'bizsolutions.sms', 'CSmsBizEvents', 'TaskAction');
            UnRegisterModuleDependences('tasks', 'OnTaskUpdate', 'bizsolutions.sms', 'CSmsBizEvents', 'TaskAction');
            UnRegisterModuleDependences('tasks', 'OnBeforeTaskDelete', 'bizsolutions.sms', 'CSmsBizEvents', 'TaskAction');
        }

        include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/install/events.php");
        $eventsCreator = new EventsCreator();
        $eventsCreator->uninstallEvents();
        return true;
    }

    function InstallFiles($arParams = array())
    {
        global $APPLICATION;
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", false, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/", false, true);
        return true;
    }

    function UnInstallFiles() {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        DeleteDirFilesEx("/bitrix/js/bizsolutions.sms");
        return true;
    }
}