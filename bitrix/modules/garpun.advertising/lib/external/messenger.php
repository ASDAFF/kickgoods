<?php

namespace garpun_advertising\External;

class Messenger {

    static private $character = "UTF-8";
    static private $urlTemplate = false;
    static private $appId = false;
    static private $version = false;
    static $debug = false;

    function init($urlTemplate, $appId, $version) {
        Messenger::$version = $version;
        Messenger::$appId = $appId;
        Messenger::$urlTemplate = $urlTemplate;
    }

    static private function getUrl($type, $protocol = false) {
        if (is_array($type)) {
            $type = join("/", $type);
        }
        $url = "";


        return $url . str_replace(Array("#VERSION#", "#TYPE#"), Array(Messenger::$version, $type), Messenger::$urlTemplate);
    }

    static function exec($type, $vars = Array(), $method = "POST") {

        if (Messenger::$debug) {
            switch ($type) {
                case 'token': return new ExternalRequestResult(json_encode(Array("token" => "14605216801f522f9f3684b418546651")));
                    break;
                case 'projects': return new ExternalRequestResult(json_encode(Array("125")));
                    break;
            }
        } else {
            return Messenger::exec_do($type, $vars, $method);
        }
    }

    static function prepareUrl($type, $getParams = false, $protocol = false) {
        $url = Messenger::getUrl($type, $protocol);

        $userInfo = \garpun_advertising\Save::getUserSendInfo();
        $getParams["appId"] = Messenger::$appId;

        if ($userInfo) {
            $getParams = array_merge($userInfo, $getParams);
        }

        if (is_array($getParams)) {
            $url.="?" . http_build_query($getParams);
        }
        return $url;
    }

    static function iconv($params, $toBack = false) {
        if (LANG_CHARSET == Messenger::$character) {
            return $params;
        } elseif (!$toBack) {
            $from = LANG_CHARSET;
            $to = Messenger::$character;
        } else {
            $to = LANG_CHARSET;
            $from = Messenger::$character;
        }
        $params = $GLOBALS["APPLICATION"]->ConvertCharsetArray($params, $from, $to);
        return $params;
    }

    static function exec_do($type, $vars = Array(), $method = "POST") {
        if (function_exists('curl_init')) {
            $old_vars = $vars;
            $vars = Messenger::iconv($vars);

            $ch = curl_init();
            $getArr = Array();
            if ($method == "GET" && !empty($vars)) {
                $getArr = $vars;
            }
            $url = Messenger::prepareUrl($type, $getArr);

            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($vars));
            }

            if ($method == "PUT" || $method == "DELETE") {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($vars));
            }

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10 * 1000);
            $result_string = curl_exec($ch);
           // var_dump($vars,$result_string);
            if ($result_string === false) {
                return new ExternalRequestResult(false);
            }
            curl_close($ch);
            $result = json_decode($result_string, true);
            $result = Messenger::iconv($result, true);
        } else {
            $vars = Messenger::iconv($vars);
            $getArr = array();

            if ($method == "GET") {
                $getArr = $vars;
                $url = Messenger::prepareUrl($type, $getArr);
                $result_string = @file_get_contents($url);
                if ($result_string) {
                    $result = json_decode($result_string, true);
                    $result = Messenger::iconv($result, true);
                } else {
                    $result = false;
                }
            } elseif ($method == "POST" || $method == "PUT" || $method == "DELETE") {
                $url = Messenger::prepareUrl($type, $getArr);
                $postdata = json_encode($vars);
                $opts = array('http' =>
                    array(
                        'method' => $method,
                        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                        'content' => $postdata,
                        'timeout' => 10 * 1000,
                        'follow_location' => 1,
                        'ignore_errors' => 1,
                    ),
                    'ssl' => array('verify_peer' => false),
                );

                $context = stream_context_create($opts);
                $result_string = @file_get_contents($url, false, $context);

                if ($result_string) {
                    $result = json_decode($result_string, true);
                    $result = Messenger::iconv($result, true);
                } else {
                    $result = false;
                }
            }
        }
        return new ExternalRequestResult($result);
    }

    static function sipmleGetCurl($url) {
        if (function_exists('curl_init')) {

            $vars = Messenger::iconv($vars);

            $ch = curl_init();
            $getArr = Array();

            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            $result_string = curl_exec($ch);

            curl_close($ch);
            $result = json_decode($result_string, true);
            $result = Messenger::iconv($result, true);
        }
        else{
            //var_dump(105,$url);
            if(!(strpos($url,"http://")===0||strpos($url,"https://")===0)){
                $url="http://".$url;
            }
           
            $result = @file_get_contents($url);
            if ($result) {
                $result = json_decode($result, true);
                $result = Messenger::iconv($result, true);
            }
        }
        return $result;
    }

}
