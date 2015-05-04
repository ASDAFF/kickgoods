<?

global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang . "/lang/", "/install/index.php"));

Class garpun_advertising extends CModule {

    const MODULE_ID = 'garpun.advertising';

    var $MODULE_ID = 'garpun.advertising';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function __construct() {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("GARPUN_ADVERTISING_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("GARPUN_ADVERTISING_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("GARPUN_ADVERTISING_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("GARPUN_ADVERTISING_PARTNER_URI");
    }

    function InstallFiles() {
        
        $place = __DIR__ . '/admin/';
        $d = CopyDirFiles($place, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/admin/", true, true);
        
        $placeJS = __DIR__ . '/js';
        CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/js/");
        CopyDirFiles($placeJS, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/js", true, true);

        
        CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tools/" . self::MODULE_ID . "/");
        $place = __DIR__ . '/tools/' . self::MODULE_ID . "/";
        CopyDirFiles($place, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . self::MODULE_ID . "/", true, true);


        $placeTheme = __DIR__ . '/themes';
        CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/themes");
        $d = CopyDirFiles($placeTheme, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/themes", true, true);


        return true;
    }

    function UnInstallFiles() {
       
        DeleteDirFiles(__DIR__ . '/admin/',$_SERVER['DOCUMENT_ROOT']."/bitrix/admin");
        
        DeleteDirFilesEx("/bitrix/tools/garpun.advertising/");
        DeleteDirFilesEx("/bitrix/js/garpun.advertising");
        $themeTool = __DIR__ . '/themes/.default';
        DeleteDirFiles($themeTool, $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default");
        $themeTool = __DIR__ . '/themes/.default/icons';
        DeleteDirFiles($themeTool, $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default/icons");

        $placeJS = __DIR__ . '/js/garpun.advertising/';
        DeleteDirFilesEx($placeJS);
        return true;
    }

    function InstallDB() {
        global $DB, $DBType, $APPLICATION;
        $place = __DIR__ . '/db/';
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;

        $rez = $DB->RunSQLBatch($place . $DBType . "/install.sql");
        $this->errors = $rez;

        if ($this->errors !== false) {
            $APPLICATION->ThrowException(implode(",", $this->errors));
            return false;
        }
        return true;
    }

    function UnInstallDB() {

        global $DB, $DBType, $APPLICATION;
        $place = __DIR__ . '/db/';

        $rez = $DB->RunSQLBatch("$place" . $DBType . "/uninstall.sql");
        $this->errors = $rez;
        if ($this->errors !== false) {
            $APPLICATION->ThrowException(implode(",", $this->errors));
            return false;
        }
        return true;
    }

    function DoInstall() {
        global $APPLICATION;

        $this->InstallFiles();
        $d = $this->InstallDB();

        if (!$d) {
            return false;
        }
        RegisterModule(self::MODULE_ID);
    }

    function DoUninstall() {

        \Bitrix\Main\Loader::IncludeModule("garpun.advertising");
        global $APPLICATION;

        global $APPLICATION, $step, $obModule;
        $step = IntVal($step);
        if ($step < 2)
            $APPLICATION->IncludeAdminFile(GetMessage("GARPUN_ADVERTISING_MODULE_NAME"), __DIR__ . "/unstep1.php");
        else {
            $d = true;
            if (!isset($_REQUEST["neverUpdate"]) || $_REQUEST["neverUpdate"] != "Y") {
                $this->errors[] = GetMessage("GARPUN_ADVERTISING_DELETE_ERROR");
                $d = false;
            } else {
//external-info
                $algoritms = \garpun_advertising\Save::getUsersAlgoritmList();
                $update["UPDATE_TIME"] = "never";
                foreach ($algoritms as $algoritm) {
                    $answer = \garpun_advertising\External\ExternalApi::updateAlgoritm($algoritm["EXTERNAL_ID"], array_merge($algoritm, $update)
                    );
                    if ($answer !== true) {
                        $this->errors[] = $answer;
                        $d = false;
                    }
                }

                if ($d) {
                    UnRegisterModule(self::MODULE_ID);
                    $this->UnInstallFiles();
                    $d = $this->UnInstallDB();
                }
            }
            if ($this->errors !== false) {
                $APPLICATION->ThrowException(implode(",", $this->errors));
                return false;
            }
        }
    }

    public static function getModulePath() {
        return __DIR__ . "/..";
    }

    public static function getModuleInstallFolderPath() {
        return __DIR__;
    }

    public static function getModuleBXPath() {
        $rootPath = __DIR__ . "/..";
        $bxPath = $_SERVER["DOCUMENT_ROOT"];
        $rootPath = str_replace(Array("//", "\\",), "/", $rootPath);

        return str_replace($bxPath, "", $rootPath);
    }

    public function copyFiles($placeFrom, $placeTo, $nocopy = Array(".", "..", "menu.php")) {

        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . "/" . $placeFrom)) {
            if ($dir = opendir($p)) {
                while (false !== ( $el = readdir($dir))) {

                    if (!in_array($el, $nocopy)) {
                        $filePath = $placeTo . "/" . self::MODULE_ID . "_" . $el;
                        $fileConent = '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . $placeFrom . "/" . $el . '");?>';
                        $f = @fopen($filePath, 'w');
                        if ($f) {
                            fwrite($f, $fileConent);
                            fclose($f);
                        }
                    }
                }
                closedir($dir);
            }
        }
    }

}

?>
