<?php

namespace garpun_advertising;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type as FieldType;

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

class ProjectTable extends Entity\DataManager {

    public static function getFilePath() {
        return __FILE__;
    }

    public static function getTableName() {
        return 'R_PROJECT';
    }

    public static function getMap() {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_PROJECT_ID'),
            ),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_PROJECT_NAME'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'EXTERNAL_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_PROJECT_EXTERNAL_ID'),
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_PROJECT_USER_ID'),
            ),
            'DATE_CREATE' => array(
                'data_type' => 'DateTime',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_PROJECT_DATE_CREATE'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'MODIFY' => array(
                'data_type' => 'DateTime',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_PROJECT_MODIFY'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'ALGORITM' => array(
                'data_type' => 'garpun_advertising\Algoritm',
                'reference' => array('=this.ID' => 'ref.PROJECT_ID'),
            ),
            'USER' => array(
                'data_type' => 'garpun_advertising\User',
                'reference' => array('=this.USER_ID' => 'ref.ID'),
            ),
        );
    }

    public static function validateNotNull() {
        return array(
            new Entity\Validator\Length(null, 255),
        );
    }

    public static function validateSiteId() {
        return array(
            new Entity\Validator\Length(null, 5),
        );
    }

     public static function Add($fields) {
        
        
        $now= new FieldType\DateTime();
        if (!isset($fields["DATE_CREATE"])){
            $fields["DATE_CREATE"]=$now;
        }
        
        if (!isset($fields["MODIFY"])){
            $fields["MODIFY"]=$now;
        }
        return parent::add($fields);
        
    }
    
    public static function Update($fields) {
        $now= new FieldType\DateTime();
        unset($fields["DATE_CREATE"]);        
        
        if (!isset($fields["MODIFY"])){
            $fields["MODIFY"]=$now;
        }
        return parent::Update($fields);
        
    }
    
     public static function Delete($projectId) {
        $algorithms= \garpun_advertising\AlgoritmTable::getList(Array("filter"=>Array("PROJECT_ID"=>$projectId),"select"=>Array("ID")));
        
        while($arAlgorithm=$algorithms->Fetch()){
            $algorithmDel=  \garpun_advertising\AlgoritmTable::delete($arAlgorithm["ID"]);
            if(!$algorithmDel->isSuccess()){
                return $algorithmDel;
            }
        }
        return parent::delete($projectId);
        
    }
    
    
}
