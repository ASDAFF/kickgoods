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

$SMSBiz->setLogin(COption::GetOptionString("bizsolutions.sms", "LOGIN"));
$SMSBiz->setPassword(COption::GetOptionString("bizsolutions.sms", "PASSWORD"));

$report = $SMSBiz->reports("1970-01-01", date('Y-m-d', time()));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?=bitrix_sessid_post()?>
<?

$aTabs = array(
    array("DIV"=>"edit1", "TAB"=>GetMessage("TAB_DELIVERED"), "ICON"=>"", "TITLE"=>GetMessage("TAB_DELIVERED")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>

<? $tabControl->Begin(); ?>
<? $tabControl->BeginNextTab();?>

    <tr>
        <td>
            <table border=1 cellpadding="10px" cellspacing=0>
                <tr height="40">
                    <th width="140"><?=GetMessage("TAB_REPORT_DATE_LABEL")?></th>
                    <th><?=GetMessage("TAB_REPORT_SOURCE_LABEL")?></th>
                    <th><?=GetMessage("TAB_REPORT_TEXT_LABEL")?></th>

                    <th><?=GetMessage("TAB_REPORT_COUNT_ALL_LABEL")?></th>
                    <th width="20" title="<?=GetMessage("TAB_REPORT_COUNT_DELIVERED_LABEL")?>"><?=GetMessage("TAB_REPORT_COUNT_DELIVERED_LABEL_SHORT")?></th>
                    <th width="20" title="<?=GetMessage("TAB_REPORT_COUNT_NOT_DELIVERED_LABEL")?>"><?=GetMessage("TAB_REPORT_COUNT_NOT_DELIVERED_LABEL_SHORT")?></th>
                    <th width="20" title="<?=GetMessage("TAB_REPORT_COUNT_WAITING_LABEL")?>"><?=GetMessage("TAB_REPORT_COUNT_WAITING_LABEL_SHORT")?></th>
                    <th width="20" title="<?=GetMessage("TAB_REPORT_COUNT_ENQUEUED_LABEL")?>"><?=GetMessage("TAB_REPORT_COUNT_ENQUEUED_LABEL_SHORT")?></th>
                    <th><?=GetMessage("TAB_REPORT_PAYMENT_LABEL")?></th>
                </tr>
                <?if($report != false && $report["sms"] != false):?>
                    <?foreach($report["sms"] as $reportItem):?>
                        <tr border=1>
                            <td><?=$reportItem["datetime"]?></td>
                            <td><?=$reportItem["source"]?></td>
                            <td><?=$reportItem["text"]?></td>

                            <td><?=$reportItem["allCol"]?></td>
                            <td align="center"><?=$reportItem["deliveredCol"]?></td>
                            <td align="center"><?=$reportItem["notDeliveredCol"]?></td>
                            <td align="center"><?=$reportItem["waitingCol"]?></td>
                            <td align="center"><?=$reportItem["enqueuedCol"]?></td>
                            <td align="center"><?=$reportItem["payment"]?></td>
                        </tr>
                    <?endforeach;?>
                <?endif;?>
            </table>
        </td>
    </tr>
<? $tabControl->End(); ?>


<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>