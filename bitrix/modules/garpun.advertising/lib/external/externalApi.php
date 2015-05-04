<?php

namespace garpun_advertising\External;

class ExternalApi {

    static function registration($fields) {
        $fieldsAuth = Array(
            "login" => $fields["EMAIL"],
            "password" => $fields["PASSWORD"],
            "userName" => $fields["USER_NAME"],
            "userSurname" => $fields["USER_NAME"],
            "phone" => $fields["PHONE"],
            "accountName" => $fields["ACCOUNT_NAME"] . " (BITRIX)",
        );

        $answerRegistration = Messenger::exec('account', $fieldsAuth, "POST");
        if ($answerRegistration->isSuccess()) {
            $answerAuth = ExternalApi::auth($fields["EMAIL"], $fields["PASSWORD"], Array(
                        "USER_NAME" => $fields["USER_NAME"],
                        "ACCOUNT_NAME" => $fields["ACCOUNT_NAME"],
                        "EXTERNAL_ID" => $answerRegistration->getAnswer("id"),
                        "EXTERNAL_ADHANDS_ID" => $answerRegistration->getAnswer("adHandsId"),
            ));
            if ($answerAuth === true) {
                return true;
            } else {
                return $answerAuth;
            }
        } else {
            return $answerRegistration->getErrorString();
        }
    }

    static public function auth($login, $password, $addParams = array()) {
        $answerAuthorize = Messenger::exec('token', Array("login" => $login, "password" => $password));
        if (!$answerAuthorize->isSuccess()) {
            return $answerAuthorize->getErrorString();
        } else {

            $userSearch_o = \garpun_advertising\UserTable::getList(Array("select" => Array("ID"), "filter" => Array("LOGIN" => $login)));

            $user_id = false;
            if ($el = $userSearch_o->Fetch()) {
                $user_id = $el["ID"];
                $updateArray = Array("TOKEN" => $answerAuthorize->getAnswer("token"));
                $user_o = \garpun_advertising\UserTable::Update($user_id, $updateArray);
                if (!$user_o->isSuccess()) {
                    return $user_o->getErrorMessages();
                } else {
                    return true;
                }
            } else {

                if (!isset($addParams["EXTERNAL_ID"]) || !isset($addParams["EXTERNAL_ID"])) {
                    $accountInfo = \garpun_advertising\External\Messenger::exec(Array("account"), Array('token' => $answerAuthorize->getAnswer("token")), "GET");

                    if (!$accountInfo->isSuccess()) {
                        return $accountInfo->getErrorMessages();
                    } else {

                        $addParams["EXTERNAL_ID"] = $accountInfo->getAnswer("id");

                        $addParams["EXTERNAL_ADHANDS_ID"] = $accountInfo->getAnswer("adHandsId");
                        $addParams["USER_NAME"] = $accountInfo->getAnswer("userName");
                    }
                }

                global $USER;
                $fields = Array(
                    "NAME" => isset($addParams["USER_NAME"]) ? $addParams["USER_NAME"] : "Unknown",
                    "LOGIN" => $login,
                    "TOKEN" => $answerAuthorize->getAnswer("token"),
                    "EXTERNAL_ID" => isset($addParams["EXTERNAL_ID"]) ? $addParams["EXTERNAL_ID"] : 0,
                    "EXTERNAL_ADHANDS_ID" => isset($addParams["EXTERNAL_ADHANDS_ID"]) ? $addParams["EXTERNAL_ADHANDS_ID"] : 0,
                    "USER_ID" => $USER->GetID(),
                );
                $user_o = \garpun_advertising\UserTable::Add($fields);
                if (!$user_o->isSuccess()) {
                    return $user_o->getErrorMessages();
                } else {
                    // echo 20;
                    $user_id = $user_o->getId();
                    $newProjectAnswer = \garpun_advertising\External\ExternalApi::createProject(Array(
                                "NAME" => ((isset($addParams["ACCOUNT_NAME"]) && !empty($addParams["ACCOUNT_NAME"])) ? $addParams["ACCOUNT_NAME"] : \COption::GetOptionString("main", "site_name"))), Array("USER_ID" => $user_id));
                   
                    if ($newProjectAnswer === true) {
                        return true;
                    } else {
                        return $newProjectAnswer;
                    }
                }
            }
        }
    }

    static function createProject($fields, $addParams) {
        $fieldsProject = Array(
            "name" => $fields["NAME"],
        );
        $answerProject = Messenger::exec('projects', $fieldsProject);
        if (!$answerProject->isSuccess()) {
            return $answerProject->getErrorString();
        } else {
            $fieldsSql = Array(
                "EXTERNAL_ID" => $answerProject->getAnswer("id"),
                "USER_ID" => $addParams["USER_ID"],
                "NAME" => $answerProject->getAnswer("name"),
            );
            $project_o = \garpun_advertising\ProjectTable::add($fieldsSql);
            if (!$project_o->isSuccess()) {
                return $project_o->getErrorMessages();
            } else {
                return true;
            }
        }
        return true;
    }

    static function addAlgoritm($fields) {
        $fieldsAlgoritm = Array(
            "type" => $fields["TYPE_ALGORITM"],
            "name" => $fields["NAME"],
            "sourceURL" => $fields["PATH"],
            "geo" => $fields["GEO"],
            "updateSchedule" => Array($fields["UPDATE_TIME"]),
        );

        if (isset($fields["UPDATE_TIME"]) && !empty($fields["UPDATE_TIME"])) {
            $fieldsAlgoritm["updateSchedule"] = $fields["UPDATE_TIME"];
        } else {
            $fieldsAlgoritm["updateSchedule"] = "never";
        }
        if (is_array($fields["ENGINE_SETTING"])) {
            foreach ($fields["ENGINE_SETTING"] as $engine => $engineSettings) {
                if ($engineSettings["ID"])
                    $fieldsAlgoritm["engineSettings"][] = Array(
                        "engine" => $engine,
                        "externalAccountId" => $engineSettings["ID"],
                        "defaultClickPrice" => $engineSettings["PRICE"],
                    );
            }
        }
        $answerAlgoritm = Messenger::exec(Array('projects', $fields["EXTERNAL_PROJECT_ID"], 'algorithms',), $fieldsAlgoritm);
        return $answerAlgoritm;
    }

    static function updateAlgoritm($id, $fields) {

        $fieldsAlgoritm = \garpun_advertising\External\ExternalApi::getReference(Array("projects", $fields["PROJECT_EXTERNAL_ID"], "algorithms", $id));
        if (!is_array($fieldsAlgoritm)) {
            return $fieldsAlgoritm;
        }


        if (isset($fields["GEO"])) {
            $fieldsAlgoritm["geo"] = $fields["GEO"];
        }

        if (isset($fields["UPDATE_TIME"]) && !empty($fields["UPDATE_TIME"])) {
            $fieldsAlgoritm["updateSchedule"] = $fields["UPDATE_TIME"];
        }
        $engineSettingsCustom = Array();
        if (is_array($fieldsAlgoritm["engineSettings"])) {
            foreach ($fieldsAlgoritm["engineSettings"] as $g) {
                $engineSettingsCustom[$g["engine"]] = $g;
            }
        }

        if (is_array($fields["ENGINE_SETTING"])) {
            foreach ($fields["ENGINE_SETTING"] as $engine => $engineSettings) {
                if($engineSettings["ID"])
                if (isset($engineSettingsCustom[$engine])) {
                    if (isset($engineSettings["ID"])) {
                        $engineSettingsCustom[$engine]["externalAccountId"] = $engineSettings["ID"];
                    }
                    if (isset($engineSettings["PRICE"])) {
                        $engineSettingsCustom[$engine]["defaultClickPrice"] = $engineSettings["PRICE"];
                    }
                } else {
                    $engineSettingsCustom[$engine] = Array(
                        "engine" => $engine,
                        "externalAccountId" => $engineSettings["ID"],
                        "defaultClickPrice" => $engineSettings["PRICE"],
                    );
                }
            }
        }


        if (!empty($fieldsAlgoritm["engineSettings"])) {
            $fieldsAlgoritm["engineSettings"] = Array();
            foreach ($engineSettingsCustom as $newEngine) {
                $fieldsAlgoritm["engineSettings"][] = $newEngine;
            }
        }
        $answerAlgoritm = Messenger::exec(Array('projects', $fields["PROJECT_EXTERNAL_ID"], 'algorithms', $id), $fieldsAlgoritm, "PUT");
        if ($answerAlgoritm->isSuccess()) {
            return true;
        } else {
            return $answerAlgoritm->getErrorString();
        }
    }

    static function createDump($algId) {
        $answerDump = Messenger::exec(Array('algorithms', $algId, "dumps"), Array(), "POST");
        if ($answerDump->isSuccess()) {
            return true;
        } else {
            return $answerDump->getErrorString();
        }
    }

    static function getLastDump($algId) {
        $answerDump = Messenger::exec(Array('algorithms', $algId, "dumps"), Array(), "GET");

        if ($answerDump->isSuccess()) {
            $dumps = $answerDump->getAnswer();
            if (count($dumps) > 1) {
                $lastDump = $dumps[0];
                for ($i = 1; $i < count($dumps); $i++) {
                    if ($dumps[$i]["id"] > $lastDump["id"]) {
                        $lastDump = $dumps[$i];
                    }
                }
            } else {
                $lastDump = current($dumps);
            }
            return $lastDump;
        } else {
            return $answerDump->getErrorString();
        }
    }

    static function alogoritmStart($algId, $externalProjectId) {
        $answerDump = Messenger::exec(Array("projects", $externalProjectId, 'algorithms', $algId, "start"), Array(), "POST");
        if ($answerDump->isSuccess()) {
            return true;
        } else {
            return $answerDump->getErrorString();
        }
    }

    static function getReference($cod) {
        $answer = \garpun_advertising\External\Messenger::exec($cod, Array(), "GET");
        if ($answer->isSuccess()) {
            $mas = $answer->GetData();
            return $mas;
        } else {
            return $answer->getErrorString();
        }
    }

    static function getReferencesArray($referencesArrayList) {
        $referencesArray = Array();
        if (is_array($referencesArrayList)) {
            foreach ($referencesArrayList as $name => $ar) {
                $answer = \garpun_advertising\External\ExternalApi::getReference($ar[0]);
                if (is_array($answer)) {
                    $referencesArray[$name] = $answer;
                } else {
                    return $answer;
                }
                if ($ar[1]) {
                    $referencesArray[$name] = \garpun_advertising\keyArrayCreator($answer, $ar[1], $ar[2]);
                }
            }
        }

        return $referencesArray;
    }

    static function prepareHeaderUrl($clientId) {
        if (!\garpun_advertising\Save::getUser()) {
            return Array(
                "#REDTEXT#" => "",
                "#SUBTEXT#" => "",
                "#SUBTEXT_LINK#" => "",
                "#LOGOUT#" => "",
                "#SUBTEXT_ACCOUNT_ID#" => "",
                "#SUBTEXT_EMAIL#" => "",
            );
        }

        $url = "bill.garpun.com/clients/$clientId/playment-date?product=garpun";
        $result = \garpun_advertising\External\Messenger::sipmleGetCurl($url);
        

        $date = false;
        $now = new \DateTime();
        $needPay = false;
        $deadlineDate = false;
        if (is_array($result)) {
            $date = $result["payment-date"];
            $tariffId = $result["tariffId"];

            $deadlineDate = new \DateTime($date);

            $needPay = false;
            $interval = $now->diff($deadlineDate);
            if ($interval->format("%r1") < 0) {
                $needPay = true;
            }
            $UtimeDeadlineDate = $deadlineDate->format("U");
            $formatTimeDeadlineDate = ConvertTimeStamp($UtimeDeadlineDate, "SHORT", "ru");

            if ($needPay) {
                \garpun_advertising\External\ExternalRequestResult::$needPay = true;
                if ($tariffId != 5) {
                    $fields = Array(
                        "#REDTEXT#" => GetMessage("GARPUN_ADVERTISING_MENU_NEED_TO_PAY"),
                        "#SUBTEXT#" => GetMessage("GARPUN_ADVERTISING_MENU_PAY_TO", Array("#DATE#" => $formatTimeDeadlineDate)),
                    );
                } else {

                    $fields = Array(
                        "#REDTEXT#" => GetMessage("GARPUN_ADVERTISING_MENU_NEED_TO_PAY_FREE"),
                        "#SUBTEXT#" => GetMessage("GARPUN_ADVERTISING_MENU_PAY_TO_FREE", Array("#DATE#" => $formatTimeDeadlineDate)),
                    );
                }
            } else {
                if ($tariffId != 5) {
                    $fields = Array(
                        "#REDTEXT#" => "",
                        "#SUBTEXT#" => GetMessage("GARPUN_ADVERTISING_MENU_PAY_TO", Array("#DATE#" => $formatTimeDeadlineDate)),
                    );
                } else {
                    $fields = Array(
                        "#REDTEXT#" => "",
                        "#SUBTEXT#" => GetMessage("GARPUN_ADVERTISING_MENU_PAY_TO_FREE", Array("#DATE#" => $formatTimeDeadlineDate)),
                    );
                }
            }
        } else {
            $fields = Array(
                "#REDTEXT#" => "",
                "#SUBTEXT#" => GetMessage("GARPUN_ADVERTISING_MENU_PAY_OUT_OF_SERVICE_DATE"),
            );
        }
        $fields["#SUBTEXT_LINK#"] = GetMessage("GARPUN_ADVERTISING_LOGO_FILE_SUBTEXT_LINK");
        $fields["#LOGOUT#"] = GetMessage("GARPUN_ADVERTISING_LOGOUT");
        $fields["#SUBTEXT_EMAIL#"] = \garpun_advertising\Save::getUser("LOGIN");
        $fields["#SUBTEXT_ACCOUNT_ID#"] = GetMessage("GARPUN_ADVERTISING_LOGO_FILE_ACCOUNT_ID", Array("#SUBTEXT_ACCOUNT_ID#" => \garpun_advertising\Save::getUser("EXTERNAL_ID")));
        return $fields;
    }

    static function getPaymentLink($clientId, $tariffId, $amount) {

        $url = "bill.garpun.com/clients/$clientId/invoice/payment?product=garpun&amount=$amount&tariffId=$tariffId";
        $result = \garpun_advertising\External\Messenger::sipmleGetCurl($url);
        if (is_array($result)) {
            return $result["payment-url"];
        }

        return false;
    }

    public static function createDumpAndStart($algId, $external_id, $external_project_id) {
        $errors = Array();
        $dumpCreate = \garpun_advertising\External\ExternalApi::createDump($external_id);
        $algoritm_o = \garpun_advertising\AlgoritmTable::Update($algId, Array("EXTERNAL_ID" => $external_id));
        if ($dumpCreate !== true) {
            $algoritm_o = \garpun_advertising\AlgoritmTable::Update($algId, Array("STATE" => "N"));
            $errors[] = $dumpCreate;
        } else {
            $AlgoritmExternalAnswer = \garpun_advertising\External\ExternalApi::alogoritmStart($external_id, $external_project_id);
            if (is_string($AlgoritmExternalAnswer)) {
                $errors[] = $AlgoritmExternalAnswer;
            } else {
                return true;
            }
        }
        return $errors;
    }

    public static function notNeedPay($show = false) {

        if (\garpun_advertising\External\ExternalRequestResult::$needPay) {
            if ($show) {
                \CAdminMessage::ShowMessage(GetMessage("GARPUN_ADVERTISING_NEED_PAY_ERROR"));
            }
            return false;
        }
        return true;
    }

}
