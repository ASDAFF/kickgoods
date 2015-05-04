<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
CJSCore::Init(array("jquery"));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?><script type="text/javascript" src="/bitrix/js/garpun.advertising/admin.js"></script>

<style type="text/css">
    @import url("/bitrix/js/garpun.advertising/admin.css");       
</style>


<?
\Bitrix\Main\Loader::IncludeModule("garpun.advertising");

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . "/menu.php");
$APPLICATION->SetTitle(GetMessage("GARPUN_ADVERTISING_TEXT"));
$errors = Array();


if (isset($_REQUEST["LOGIN"], $_REQUEST["PASSWORD"], $_REQUEST["auth"]) && !empty($_REQUEST["LOGIN"]) && !empty($_REQUEST["PASSWORD"]) && !garpun_advertising\Save::getUser()) {
    $answer = \garpun_advertising\External\ExternalApi::auth($_POST["LOGIN"], $_POST["PASSWORD"]);
    if ($answer !== true) {
        $errors = $answer;
    }
}

if (garpun_advertising\Save::getUser()) {
    $externalCreditnalsList = \garpun_advertising\External\ExternalApi::getReference("credentials");
    if (!is_array($externalCreditnalsList)) {
        $errors[] = $externalCreditnalsList;
    } else {
        $enableCredentials = false;
        foreach ($externalCreditnalsList as $engine) {
            if ($engine["status"]) {
                $enableCredentials = true;
            }
        }
        if ($enableCredentials) {
            LocalRedirect("/bitrix/admin/garpun.advertising_algoritm_list.php?lang=" . LANG);
        } else {
            LocalRedirect("/bitrix/admin/garpun.advertising_ex_system.php?lang=" . LANG);
        }
    }
}
    
if (!empty($errors)) {
    
    if(is_array($errors)){
        $errors=join($errors,"\n");
    }
    echo CAdminMessage::ShowMessage($errors);
}


$fields_auth_template = array(
    array("LOGIN", GetMessage("GARPUN_ADVERTISING_AUTH_LOGIN"), array("TYPE" => "text", "VALUE" => isset($_POST["LOGIN"]) ? $_POST["LOGIN"] : "", "REQUIRED" => "Y",)),
    array("PASSWORD", GetMessage("GARPUN_ADVERTISING_AUTH_PASSWORD"), array("TYPE" => "password", "VALUE" => isset($_POST["PASSWORD"]) ? $_POST["PASSWORD"] : "", "REQUIRED" => "Y",)),
);




$aTabs = array(
    array("DIV" => "general", "TAB" => GetMessage("FORM_AUTH")),
);

echo Loc::getMessage("GARPUN_ADVERTISING_LOGO_FILE", \garpun_advertising\External\ExternalApi::prepareHeaderUrl(\garpun_advertising\Save::getUser("EXTERNAL_ADHANDS_ID")));
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>


<form method="post" action="<?= $APPLICATION->GetCurPage() ?>" enctype="multipart/form-data" name="sbxd_form" id="sbxd_form">
    <?
    $tabControl->Begin();
    

    
    
    $tabControl->BeginNextTab();
    ?>

    <div class="garpun_advertising_content_form">
        <?
        if ($user = garpun_advertising\Save::getUser()) {
            ?> <? echo Loc::getMessage("GARPUN_ADVERTISING_AUTH_USER_HALLO"); ?> <?
            echo $user["LOGIN"];
        } else {
            ?>
            <div class="garpun_advertising_block_form">
                <? \garpun_advertising\printFormFields($fields_auth_template); ?>
            </div>
            <a href="garpun.advertising_registration.php" class="garpun_advertising_link_btn"><?= GetMessage("GARPUN_ADVERTISING_AUTH_LINK_REGISTRATION") ?></a>
            <input type="submit" size="30" maxlength="255" value="<?= GetMessage("GARPUN_ADVERTISING_AUTH_AUTH"); ?>" name="auth">
            <div class="garpun_advertising_clear"></div>            
            <div class="garpun_advertising_clear"></div>

        <? } ?>
    </div>        
    <div class="garpun_advertising_clear"></div>
    <? $tabControl->End(); ?>
</form>
<?
include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/garpun.advertising/util/gtm.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");



?>


