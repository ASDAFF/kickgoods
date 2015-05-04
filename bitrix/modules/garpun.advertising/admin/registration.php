<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?><script type="text/javascript" src="/bitrix/js/garpun.advertising/admin.js"></script>
<script type="text/css"  src="/bitrix/js/garpun.advertising/admin.css" ></script>
<link rel="stylesheet" type="text/css" href="/bitrix/js/garpun.advertising/admin.css" />

<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . "/menu.php");
$APPLICATION->SetTitle(GetMessage("FORM_REGISTRATION"));
$aTabs = array(
    array("DIV" => "general", "TAB" => GetMessage("FORM_REGISTRATION"),
    )
        ,);


$user = CUser::GetById($USER->GetId())->Fetch();
$phone = "";
if (isset($_POST["PHONE"])) {
    $phone = $_POST["PHONE"];
} elseif (!empty($user["WORK_PHONE"])) {
    $phone = $user["WORK_PHONE"];
} elseif (!empty($user["PERSONAL_PHONE"])) {
    $phone = $user["PERSONAL_PHONE"];
}

$fields_registration_template = array(
    array("USER_NAME", GetMessage("GARPUN_ADVERTISING_REG_USER_NAME"), array("TYPE" => "text", "VALUE" => isset($_POST["USER_NAME"]) ? $_POST["USER_NAME"] : $USER->GetFirstName(), "REQUIRED" => "Y",)),
    array("EMAIL", GetMessage("GARPUN_ADVERTISING_REG_EMAIL"), array("TYPE" => "email", "VALUE" => isset($_POST["EMAIL"]) ? $_POST["EMAIL"] : $USER->getEmail(), "REQUIRED" => "Y",)),
    array("ACCOUNT_NAME", GetMessage("GARPUN_ADVERTISING_REG_ACCOUNT_NAME"), array("TYPE" => "text", "VALUE" => isset($_POST["ACCOUNT_NAME"]) ? $_POST["ACCOUNT_NAME"] : COption::GetOptionString("main", "site_name"), "REQUIRED" => "Y",)),
    array("PHONE", GetMessage("GARPUN_ADVERTISING_REG_PHONE"), array("TYPE" => "text", "VALUE" => $phone, "REQUIRED" => "Y",)),
    array("PASSWORD", GetMessage("GARPUN_ADVERTISING_REG_PASSWORD"), array("TYPE" => "text", "VALUE" => isset($_POST["PASSWORD"]) ? $_POST["PASSWORD"] : randString(6), "REQUIRED" => "Y",)),
    array("PROMO", GetMessage("GARPUN_ADVERTISING_REG_PROMO"), array("TYPE" => "text", "VALUE" => isset($_POST["PROMO"]) ? $_POST["PROMO"] : "", "REQUIRED" => "N"
        ,"AFTER_TEXT"=>'<br /> <a href="http://garpun.com/partnerskaja-programma/?utm_source=bitrix_app&utm_campaign=start_page&utm_medium=cpm" target="_blank" style="
           
           "><br/>'.GetMessage("GARPUN_ADVERTISING_REG_PARTNER").'</a>')),
);


$errors = Array();

if (isset($_REQUEST["USER_NAME"]) && !garpun_advertising\Save::getUser()) {

    $fields = Array();
    foreach ($fields_registration_template as $field) {
        if (!isset($_REQUEST[$field[0]])) {
            $errors[] = Loc::getMessage("GARPUN_ADVERTISING_ERROR_FIELD") . " '" . $field[1] . "'";
        } elseif (isset($field[2]["REQUIRED"]) && $field[2]["REQUIRED"] == "Y" && empty($_REQUEST[$field[0]])) {
            $errors[] = Loc::getMessage("GARPUN_ADVERTISING_ERROR_REQUIRED_FIELD") . " '" . $field[1] . "'";
        }
        $fields[$field[0]] = $_REQUEST[$field[0]];
    }
    if (empty($errors)) {
        $regResult = garpun_advertising\External\ExternalApi::registration($fields);
        //var_dump($regResult);
        if ($regResult !== true) {
            $_regResult = str_replace(Loc::getMessage("ERROR_REPLACE"), '', $regResult);
            $_errors = explode(',', $_regResult);
            foreach ($_errors as $_error) {
                $error = Loc::getMessage(trim($_error));
                if (!empty($error)) {
                    $errors[] = $error;
                } else {
                    $errors[] = $_error;
                }
            }
        } else {
            LocalRedirect("/bitrix/admin/garpun.advertising_ex_system.php?lang=" . LANG);
        }
    }
}



if (garpun_advertising\Save::getUser()) {
    $errors[] = Loc::getMessage("GARPUN_ADVERTISING_ALLREADY_AUTH");
}
if (!empty($errors)) {
    CAdminMessage::ShowMessage(join("\n", $errors));
}
CJSCore::Init(array("jquery"));
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>

<?
echo Loc::getMessage("GARPUN_ADVERTISING_LOGO_FILE"
        , \garpun_advertising\External\ExternalApi::prepareHeaderUrl(\garpun_advertising\Save::getUser("EXTERNAL_ADHANDS_ID")))
?>

<form method="post" action="<?= $APPLICATION->GetCurPage() ?>" enctype="multipart/form-data" name="reg_form" id="registrationForm">

    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <div class="garpun_advertising_content_form garpun_advertising_reg_form">
        <a href="garpun.advertising_index.php" class="garpun_advertising_logon adm-btn adm-btn-save"><?= GetMessage("GARPUN_ADVERTISING_FOR_REGISTERED") ?></a>
        <hr>
        <h4><?= GetMessage("GARPUN_ADVERTISING_REGISTRATION") ?></h4>
        <div class="garpun_advertising_block_form">                

<?= \garpun_advertising\printFormFields($fields_registration_template); ?>
        </div>
        <div class="garpun_advertising_clear"></div>
       

        <input  name="reg" type="submit" size="50" class="garpun_advertising_registration adm-btn-save" value="<?= GetMessage("GARPUN_ADVERTISING_REG_BUTTON") ?>" >

    </div>        

    <div class="garpun_advertising_block_description">
        <div class="garpun_advertising_description_garpun_company">
<?= GetMessage("GARPUN_ADVERTISING_REG_WHY_GARPUN") ?>

        </div>
        <div class="garpun_advertising_clear"></div>
    </div>
    <div class="garpun_advertising_clear"></div>
<? ?>



<? $tabControl->End(); ?>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $("form#registrationForm").bind("submit", function() {
            $("form#registrationForm input[type=submit]").prop("disabled", true);
        });
    });
</script>
<?
include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/garpun.advertising/util/gtm.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");


?>
