<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bizsolutions.sms/classes/general/CSmsBizEvents.php");

class DbWrapper extends CSmsBizEvents {

}

global $SMSBiz;
if (!is_object($SMSBiz))
    $SMSBiz = new DbWrapper();

?>