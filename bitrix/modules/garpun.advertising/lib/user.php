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


class UserTable extends Entity\DataManager {

    public static function getFilePath() {
        return __FILE__;
    }

    public static function getTableName() {
        return 'R_USER';
    }

    public static function getMap() {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_ID'),
            ),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_NAME'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'LOGIN' => array(
                'data_type' => 'string',
                //'primary' => true,
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_LOGIN'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'TOKEN' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_TOKEN'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'DATE_CREATE' => array(
                'data_type' => 'DateTime',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_DATE_CREATE'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
              'MODIFY' => array(
                'data_type' => 'DateTime',
                'required' => true,
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_MODIFY'),
                'validation' => array(__CLASS__, 'validateNotNull'),
            ),
            'EXTERNAL_ID' => array(
                'data_type' => 'integer',                                
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_EXTERNAL_ID'),
            ),
            'EXTERNAL_ADHANDS_ID' => array(
                'data_type' => 'integer',                                
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_EXTERNAL_ADHANDS_ID'),
            ),
            'USER_ID' => array(
                'data_type' => 'integer',                
                'title' => Loc::getMessage('GARPUN_ADVERTISING_R_USER_USER_ID'),
            ),
            'PROJECT' => array(
                'data_type' => 'garpun_advertising\Project',
                'reference' => array('=this.ID' => 'ref.USER_ID'),
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
    
    public static function Update($user_id,$fields) {
        $now= new FieldType\DateTime();
        unset($fields["DATE_CREATE"]);        
        
        if (!isset($fields["MODIFY"])){
            $fields["MODIFY"]=$now;
        }
        return parent::Update($user_id,$fields);
        
    }

    public static function Delete($userId) {
        $projects= \garpun_advertising\ProjectTable::getList(Array("filter"=>Array("USER_ID"=>$userId),"select"=>Array("ID")));
        
        while($arProjects=$projects->Fetch()){
            $projectDel=\garpun_advertising\ProjectTable::delete($arProjects["ID"]);
            if(!$projectDel->isSuccess()){
                return $projectDel;
            }
        }
        return parent::delete($userId);
        
    }
    
    
    public static function Logout() {
        global $DB;
        $DB->Query("UPDATE ".  mysql_real_escape_string(UserTable::getTableName())." SET TOKEN=''");
    }
    
}
