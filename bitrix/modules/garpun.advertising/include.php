<?php

if (date_default_timezone_get() == "" or date_default_timezone_get() == "UTC") {
    date_default_timezone_set('Europe/Moscow');
}
$arClasses = array(
    "garpun_advertising" => "install/index.php",
    "garpun_advertising\AlgoritmTable" => "lib/algoritm.php",
    "garpun_advertising\IblockTable" => "lib/iblock.php",
    "garpun_advertising\SectionTable" => "lib/section.php",
    "garpun_advertising\ProjectTable" => "lib/project.php",
    "garpun_advertising\PropertyTable" => "lib/property.php",
    "garpun_advertising\UserTable" => "lib/user.php",
    "garpun_advertising\External\ExternalApi" => "lib/external/externalApi.php",
    "garpun_advertising\External\Messenger" => "lib/external/messenger.php",
    "garpun_advertising\External\ExternalRequestResult" => "lib/external/externalRequestResult.php",
    "garpun_advertising\FileCreator" => "classes/fileCreator.php",
    "garpun_advertising\Sender" => "classes/sender.php",
    "garpun_advertising\Save" => "classes/save.php",
);
CModule::AddAutoloadClasses("garpun.advertising", $arClasses);

\garpun_advertising\External\Messenger::init("https://amp.garpun.com/api/v#VERSION#/#TYPE#", "173edaf533ced162865eec98bff37514", 1);

if (isset($_REQUEST["GarpunLogout"]) && $_REQUEST["GarpunLogout"] == "yes") {
    \garpun_advertising\UserTable::Logout();
}

$filePath = Array();
$filePath["tmp"] = $_SERVER["DOCUMENT_ROOT"] . COption::GetOptionString("garpun.advertising", "file_path") . COption::GetOptionString("garpun.advertising", "file_tmp_add") . "export_yml_#ALGORITM_ID#_#HASH#.xml";
$filePath["ready"] = $_SERVER["DOCUMENT_ROOT"] . COption::GetOptionString("garpun.advertising", "file_path") . "export_yml_#ALGORITM_ID#.xml";
include __DIR__ . "/functions.php";

$domain=\garpun_advertising\getDomainName();

\garpun_advertising\FileCreator::init($filePath, $domain);

