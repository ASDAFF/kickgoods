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

class SectionTable extends Entity\DataManager {

   
    public static function getFilePath() {
        return __FILE__;
    }

    public static function getTableName() {
        return 'R_ALGORITM_IBLOCK_SECTION';
    }

    public static function getMap() {
        return array(      
             'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_IBLOCK_SECTION_ID'),
            ),
            'ID_IBLOCK' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_IBLOCK_SECTION_ID_IBLOCK'),                
            ),            
            'ID_SECTION' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_FILE_IBLOCK_SECTION_ID_SECTION'),
            ),
        );
    }
   

    
}