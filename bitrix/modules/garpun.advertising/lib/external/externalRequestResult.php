<?php

namespace garpun_advertising\External;

IncludeModuleLangFile(__FILE__);

class ExternalRequestResult extends \Bitrix\Main\Entity\Result {

    static public $needPay;
    static public $globalError;

    public function __construct($answer) {


        parent::__construct();
        self::$globalError = false;

        global $APPLICATION;
        if ($answer === false) {
            $error = new \Bitrix\Main\Entity\EntityError(GetMessage("GARPUN_ADVERTISING_EXTERNAL_TIMEOUT_ERROR"), 700);
            self::$globalError = true;

            $this->addError($error);
        } elseif ($answer === null) {
            $error = new \Bitrix\Main\Entity\EntityError(GetMessage("GARPUN_ADVERTISING_EXTERNAL_UNKNOWN_ERROR"), 750);
            self::$globalError = true;

            $this->addError($error);
        } else {
            if (!is_array($answer)) {
                $answer = Array($answer);
            }

            $this->setData($answer);
            if (isset($answer["error"])) {
                $error = new \Bitrix\Main\Entity\EntityError($answer["error"]["details"], $answer["error"]["code"]);
                $this->addError($error);
            }
        }
    }

    public function getAnswer($arCode = false) {
        if ($arCode) {
            $a = $this->getData();
            return $a[$arCode];
        }
        return $this->getData();
    }

    public function getErrorString() {
        return join(",", $this->getErrorMessages());
    }

}
