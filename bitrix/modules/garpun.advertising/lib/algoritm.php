<?php

namespace garpun_advertising;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class GarpunFile
 *
 * Fields:
 * <ul> 
 * <li> ID int mandatory
 * <li> TIME datetime
 * <li> CREATE datetime
 * <li> SITE string(255) mandatory
 * <li> TYPE string(1) mandatory
 * <li> PATH string(255) mandatory 
 * </ul>
 *
 * @package Bitrix\Iblock
 */

class AlgoritmTable extends Entity\DataManager {

    public static function getFilePath() {
        return __FILE__;
    }

    public static function getTableName() {
        return 'R_ALGORITM';
    }

    public static function getMap() {
        return array(
             'EXTERNAL_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_EXTERNAL_ID'),
            ),
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_ID'),
            ),
            'PROJECT_ID' => array(
                'data_type' => 'integer',
                 'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_PROJECT_ID'),
            ),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_NAME'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'PATH' => array(
                'data_type' => 'string',
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_PATH'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'TIME' => array(
                'data_type' => 'datetime',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_TIME'),
            ),
            'DATE_CREATE' => array(
                'data_type' => 'datetime',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_DATE_CREATE'),
            ),
            'STATE' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_STATE'),
                'values' => array('N', 'D', "R", "S"),
            ),
            'TYPE' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_TYPE'),
                'values' => array('C'/* custom */, 'Y'/* YML */),
            ),
            'AGENT_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_AGENT'),
            ),
            'AGENT2_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_AGENT2'),
            ),
            'TMP_HASH' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_TMP'),
            ),
            'IBLOCK' => array(
                'data_type' => 'garpun_advertising\Iblock',
                'reference' => array('=this.ID' => 'ref.ID_FILE'),
            ),
            'PROPERTY' => array(
                'data_type' => 'garpun_advertising\Property',
                'reference' => array('=this.ID' => 'ref.ID_ALGORITM'),
            ),
            'PROJECT' => array(
                'data_type' => 'garpun_advertising\Project',
                'reference' => array('=this.PROJECT_ID' => 'ref.ID'),
            ),
        );
    }

    public static function delete($id) {
        $res = \garpun_advertising\AlgoritmTable::getById($id);
        if ($r = $res->Fetch()) {
            if (!empty($r["AGENT_ID"])) {
                \CAgent::Delete($r["AGENT_ID"]);
            }
        }
        $res = \garpun_advertising\PropertyTable::getList(Array("filter" => Array("ID_ALGORITM" => $id), "select" => Array("ID")));
        while ($r = $res->Fetch()) {
            $k = \garpun_advertising\PropertyTable::delete($r["ID"]);
            if (!$k->isSuccess()) {
                return $k;
            }
        }
        
        $res = \garpun_advertising\IblockTable::getList(Array("filter" => Array("ID_ALGORITM" => $id), "select" => Array("ID")));
        while ($r = $res->Fetch()) {
            $k = \garpun_advertising\IblockTable::delete($r["ID"]);
            if (!$k->isSuccess()) {
                return $k;
            }
        }
        return parent::delete($id);
    }


    static function addExternal($fields) {
        $fieldsAlgoritm = Array(
            "type",
            "projectId",
            "name",
            "sourceURL",
            "geo",
            "engineSettings",
            "updateSchedule",
        );
        $returnAnswer = new \Bitrix\Main\Entity\AddResult();


        $externalAlgoritm = Messenger::exec('algoritm', $fieldsAlgoritm);
        if ($externalAlgoritm->isSuccess()) {
            $external_id = $externalAlgoritm->getAnswer("id");
            $returnAnswer->setId($external_id);
        } else {
            $returnError = new \Bitrix\Main\Entity\EntityError($externalAlgoritm->getErrorString(), $externalAlgoritm->getAnswer("code"));
            $returnAnswer->addError($returnError);
            return $returnAnswer;
        }
    }

    public static function validateNotNull() {
        return array(
            new Entity\Validator\Length(null, 255),
        );
    }

    public static function validatePath() {
        return array("",
            new Entity\Validator\Length(5, 255),
        );
    }

    public static function validateSiteId() {
        return array(
            new Entity\Validator\Length(null, 5),
        );
    }

}
