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

class IblockTable extends Entity\DataManager {

    public static function getFilePath() {
        return __FILE__;
    }

    public static function getTableName() {
        return 'R_ALGORITM_IBLOCK';
    }

    public static function getMap() {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_IBLOCK_ID'),
            ),
            'ID_ALGORITM' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_IBLOCK_ID_FILE'),
            ),
            'ID_IBLOCK' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_IBLOCK_ID_SECTION'),
            ),
            'SECTION' => array(
                'data_type' => 'garpun_advertising\Section',
                'reference' => array('=this.ID' => 'ref.ID_IBLOCK')
            ),
        );
    }

    public static function delete($id) {        
        $section_o = \garpun_advertising\SectionTable::getList(Array(
                    "filter" => Array("ID_IBLOCK" => $id),
                    "select" => Array("ID",)));
        while ($s = $section_o->Fetch()) {
            $resultSection = \garpun_advertising\SectionTable::delete($s["ID"]);
            if (!$resultSection->isSuccess()) {
                $result=new Entity\DeleteResult();
                $result->addErrors($resultSection->getErrors());
                return $result;
            }
        }
        return parent::delete($id);
    }

}
