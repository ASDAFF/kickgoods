<?php

namespace garpun_advertising;

class Save {

    static private $USER = false;
    static private $ALGORITMS = false;
    static private $PROJECTS = false;

    static function getUserSendInfo() {
        $u = Save::getUser();
        $info = Array();
        if ($u !== false)
            $info = Array(
                "token" => $u["TOKEN"],
                "login" => $u["LOGIN"],
            );
        return $info;
    }

    static function issetUser() {
        $isset = false;
        if (!Save::getUser()) {
            $user_info_o = \garpun_advertising\UserTable::getList(Array("select" => Array("NAME", "LOGIN", "TOKEN", "ID", "EXTERNAL_ID")));
            while ($u = $user_info_o->Fetch()) {
                $isset = true;
                if (!empty($u["TOKEN"])) {
                    Save::$USER = Array("LOGIN" => $u["LOGIN"], "TOKEN" => $u["TOKEN"], "ID" => $u["ID"], "NAME" => $u["NAME"], "EXTERNAL_ID" => $u["EXTERNAL_ID"]);
                }
            }

            return ($isset);
        } else {
            return true;
        }
    }

    static function getUser($param = false) {
        if (!Save::$USER) {
            $user_info_o = \garpun_advertising\UserTable::getList(Array("select" => Array("NAME", "LOGIN", "TOKEN", "ID", "EXTERNAL_ID", "EXTERNAL_ADHANDS_ID"), "filter" => Array("!TOKEN" => "")));
            if ($u = $user_info_o->Fetch()) {
                Save::$USER = Array("LOGIN" => $u["LOGIN"],
                    "TOKEN" => $u["TOKEN"],
                    "ID" => $u["ID"],
                    "NAME" => $u["NAME"],
                    "EXTERNAL_ID" => $u["EXTERNAL_ID"],
                    "EXTERNAL_ADHANDS_ID" => $u["EXTERNAL_ADHANDS_ID"]
                );
            }
        }
        if ($param === false) {
            return Save::$USER;
        } elseif (Save::$USER) {
            return Save::$USER[$param];
        }
        return false;
    }

    static function getUsersProjectList($one = false) {
        if (!Save::$PROJECTS) {
            $user = Save::getUser();
            $id = $user["ID"];
            $project_o = \garpun_advertising\ProjectTable::getList(Array("select" => Array("ID", "NAME", "EXTERNAL_ID"), "filter" => Array("USER_ID" => $id)));
            while ($u = $project_o->Fetch()) {
                Save::$PROJECTS[$u["ID"]] = Array("ID" => $u["ID"], "NAME" => $u["NAME"], "EXTERNAL_ID" => $u["EXTERNAL_ID"]);
       
            }
        }
       
        if ($one) {
            return current(Save::$PROJECTS);
        } else {
            return Save::$PROJECTS;
        }
    }

    static function getUsersAlgoritmList($params=false) {
        $user = Save::getUser();
        if (!$user)
            return false;
        $id = $user["ID"];


        if (!Save::$ALGORITMS) {
            Save::$ALGORITMS = Array();
            if (is_array($params)) {
                
            }

            $algoritm_list_o = \garpun_advertising\AlgoritmTable::GetList(Array("filter" => Array("PROJECT.USER_ID" => $id), "select" => Array(
                            "ID", "NAME", "EXTERNAL_ID", "PROJECT.EXTERNAL_ID", "PROJECT_ID")));
            while ($a = $algoritm_list_o->Fetch()) {
                Save::$ALGORITMS[] = Array("NAME" => $a["NAME"], "EXTERNAL_ID" => $a["EXTERNAL_ID"], "ID" => $a["ID"], "PROJECT_ID" => $a["PROJECT_ID"], "PROJECT_EXTERNAL_ID" => $a["GARPUN_ADVERTISING_ALGORITM_PROJECT_EXTERNAL_ID"]);
            }
        }
        return Save::$ALGORITMS;
    }

}
