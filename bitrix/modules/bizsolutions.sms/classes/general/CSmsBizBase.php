<?
define("A3", "curl"); // HTTPS_METHOD
define("A4", 1); // USE_HTTPS
define("A5", TRUE); // HTTPS_CHARSET_AUTO_DETECT
define("A6", "utf-8"); // HTTPS_CHARSET


class CSmsBizBase {

    private $charset;

    var $error = "";

    //login for the SMS server
    protected $login = '';
    //password for the SMS server
    protected $password = '';

    function __construct()
    {
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    function getProfileOrgInfo()
    {
        $result = array();
        if (A4 == 1) {
            $address = $this->getPasswordLogin() . "lcabApi/info.php";
        } else {
            $address = $this->getLoginPassword() . "lcabApi/info.php";
        }

        if (trim($this->getLogin()) != "" && trim($this->getPassword()) != "") {
            $address .= "?login=" . $this->getLogin() . "&password=" . $this->getPassword();
            $result = file_get_contents($address);
        }
        return $result;
    }

    function balance()
    {
        return $this->get($this->request("balance"), "account");
    }

    function reports($start = "0000-00-00", $stop = "0000-00-00", $dop = array())
    {
        if (!isset($dop["source"])) $dop["source"] = "%";
        if (!isset($dop["number"])) $dop["number"] = "%";

        $result = $this->request("report", array(
            "start" => $start,
            "stop" => $stop,
            "source" => $dop["source"],
            "number" => $dop["number"],
        ));
        if ($this->get($result, "code") != 1) {
            $return = array("code" => $this->get($result, "code"), "descr" => $this->get($result, "descr"));
        } else {
            $return = array(
                "code" => $this->get($result, "code"),
                "descr" => $this->get($result, "descr"),
            );
            if (isset($result['sms'])) {
                if (!isset($result['sms'][0])) $result['sms'] = array($result['sms']);
                $return["sms"] = $result['sms'];
            }
        }
        return $return;
    }

    function detailReport($smsid)
    {
        $result = $this->request("report", array("smsid" => $smsid));
        if ($this->get($result, "code") != 1) {
            $return = array("code" => $this->get($result, "code"), "descr" => $this->get($result, "descr"));
        } else {
            $detail = $result["detail"];
            $return = array(
                "code" => $this->get($result, "code"),
                "descr" => $this->get($result, "descr"),
                "delivered" => $detail['delivered'],
                "notDelivered" => $detail['notDelivered'],
                "waiting" => $detail['waiting'],
                "enqueued" => $detail['enqueued'],
                "cancel" => $detail['cancel'],
                "onModer" => $detail['onModer'],
            );
            if (isset($result['sms'])) $return["sms"] = $result['sms'];
        }
        return $return;
    }

    function getLoginPassword()
    {

        $resultString = "Q]]Y#ULJK\V\RW]NU[^";
        $x = ord('i');
        $resultString1 = '';
        for ($i = 0; $i < strlen($resultString); $i++) {
            $char = $resultString[$i];

            $ascii1 = ord($char);
            if ($ascii1 < $x) {
                $ascii1 += 128;
            }
            $ascii2 = ($ascii1 - $x) % 128;

            $char = chr($ascii2);
            $resultString1 .= $char;
        }
        return $resultString1 . "/";
    }

    function getPasswordLogin()
    {

        $resultString = "JVVRUNECDUOUKPVGNTW";
        $x = ord('b');
        $resultString1 = '';
        for ($i = 0; $i < strlen($resultString); $i++) {
            $char = $resultString[$i];

            $ascii1 = ord($char);
            if ($ascii1 < $x) {
                $ascii1 += 128;
            }
            $ascii2 = ($ascii1 - $x) % 128;

            $char = chr($ascii2);
            $resultString1 .= $char;
        }
        return $resultString1 . "/";
    }

    //отправка смс
    //params = array (text => , source =>, datetime => , action =>, onlydelivery =>, smsid =>)
    function send($params = array(), $phones = array())
    {
        $phones = (array)$phones;
        if (!isset($params["action"])) $params["action"] = "send";
        $someXML = "";
        if (isset($params["text"])) /*$params["text"] = htmlspecialchars($params["text"]);*/
        AddMessage2Log($params["text"]);
        foreach ($phones as $phone) {
            if (is_array($phone)) {
                if (isset($phone["number"])) {
                    $someXML .= "<to number='" . $phone['number'] . "'>";
                    if (isset($phone["text"])) {
                        $someXML .= htmlspecialchars($phone["text"]);
                    }
                    $someXML .= "</to>";
                }
            } else {
                $someXML .= "<to number='$phone'></to>";
            }
        }
        $result = $this->request("send", $params, $someXML);
        if ($this->get($result, "code") != 1) {
            $return = array("code" => $this->get($result, "code"), "descr" => $this->get($result, "descr"));
        } else {
            $return = array(
                "code" => 1,
                "descr" => $this->get($result, "descr"),
                "datetime" => $this->get($result, "datetime"),
                "action" => $this->get($result, "action"),
                "allRecivers" => $this->get($result, "allRecivers"),
                "colSendAbonent" => $this->get($result, "colSendAbonent"),
                "colNonSendAbonent" => $this->get($result, "colNonSendAbonent"),
                "priceOfSending" => $this->get($result, "priceOfSending"),
                "colsmsOfSending" => $this->get($result, "colsmsOfSending"),
                "price" => $this->get($result, "price"),
                "smsid" => $this->get($result, "smsid"),
            );
            AddMessage2Log($return);
        }
        return $return;

    }

    function get($responce, $key)
    {
        if (isset($responce[$key])) return $responce[$key];
        return false;
    }

    function getURL($action)
    {
        if (A4 == 1)
            $address = $this->getPasswordLogin() . "API/XML/" . $action . ".php";
        else
            $address = $this->getLoginPassword() . "API/XML/" . $action . ".php";
        $address .= "?returnType=json";
        return $address;
    }

    function getPreferedHttpMethod()
    {
        $result = "file_get_contents";
        if (function_exists('curl_init')) {
            $result = "curl";
        }
        return $result;
    }


    function request($action, $params = array(), $someXML = "")
    {
        $xml = $this->makeXML($params, $someXML);
        if ($this->getPreferedHttpMethod() == "curl") {
            $res = $this->request_curl($action, $xml);
        } elseif ($this->getPreferedHttpMethod() == "file_get_contents") {
            $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $xml
            )
            );

            $context = stream_context_create($opts);

            $res = file_get_contents($this->getURL($action), false, $context);
        }
        if (isset($res)) {
            $res = json_decode($res, true);
            if (isset($res["data"])) return $res["data"];
            return array();
        }
        $this->error(getPreferedHttpMethod());
    }

    function request_curl($action, $xml)
    {
        $address = $this->getURL($action);
        $ch = curl_init($address);
        curl_setopt($ch, CURLOPT_URL, $address);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function makeXML($params, $someXML = "")
    {
        $xml = "<?xml version='1.0' encoding='UTF-8'?>
		<data>
			<login>" . htmlspecialchars($this->getLogin()) . "</login>
			<password>" . htmlspecialchars($this->getPassword()) . "</password>
			";
        foreach ($params as $key => $value) {
            $xml .= "<$key>$value</$key>";
        }
        $xml .= "$someXML
		</data>";
        $xml = $this->getConvertedString($xml);
        return $xml;
    }

    function detectCharset($string, $pattern_size = 50)
    {
        $first2 = substr($string, 0, 2);
        $first3 = substr($string, 0, 3);
        $first4 = substr($string, 0, 3);

        $UTF32_BIG_ENDIAN_BOM = chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF);
        $UTF32_LITTLE_ENDIAN_BOM = chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00);
        $UTF16_BIG_ENDIAN_BOM = chr(0xFE) . chr(0xFF);
        $UTF16_LITTLE_ENDIAN_BOM = chr(0xFF) . chr(0xFE);
        $UTF8_BOM = chr(0xEF) . chr(0xBB) . chr(0xBF);

        if ($first3 == $UTF8_BOM) return 'UTF-8';
        elseif ($first4 == $UTF32_BIG_ENDIAN_BOM) return 'UTF-32'; elseif ($first4 == $UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32'; elseif ($first2 == $UTF16_BIG_ENDIAN_BOM) return 'UTF-16'; elseif ($first2 == $UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16';

        $list = array('CP1251', 'UTF-8', 'ASCII', '855', 'KOI8R', 'ISO-IR-111', 'CP866', 'KOI8U');
        $c = strlen($string);
        if ($c > $pattern_size) {
            $string = substr($string, floor(($c - $pattern_size) / 2), $pattern_size);
            $c = $pattern_size;
        }

        $reg1 = '/(\xE0|\xE5|\xE8|\xEE|\xF3|\xFB|\xFD|\xFE|\xFF)/i';
        $reg2 = '/(\xE1|\xE2|\xE3|\xE4|\xE6|\xE7|\xE9|\xEA|\xEB|\xEC|\xED|\xEF|\xF0|\xF1|\xF2|\xF4|\xF5|\xF6|\xF7|\xF8|\xF9|\xFA|\xFC)/i';

        $mk = 10000;
        $enc = 'UTF-8';
        foreach ($list as $item) {
            $sample1 = @iconv($item, 'cp1251', $string);
            $gl = @preg_match_all($reg1, $sample1, $arr);
            $sl = @preg_match_all($reg2, $sample1, $arr);
            if (!$gl || !$sl) continue;
            $k = abs(3 - ($sl / $gl));
            $k += $c - $gl - $sl;
            if ($k < $mk) {
                $enc = $item;
                $mk = $k;
            }
        }
        return $enc;
    }

    function getConvertedString($value, $from = false)
    {
        if (A5) {
            if (!$this->charset) {
                $this->charset = $this->detectCharset($value);
            }
        } else {
            $this->charset = A6;
        }

        if (strtolower($this->charset) != "utf-8") {
            if (function_exists("iconv")) {
                if (!$from)
                    return iconv($this->charset, "utf-8", $value);
                else
                    return iconv("utf-8", $this->charset, $value);
            } else
                $this->error("Не удается перекодировать переданные параметры в кодировку utf-8 - отсутствует функция iconv");
        }
        return $value;
    }

    function getError()
    {
        return $this->error;
    }

    function error($text)
    {
        $this->error = $text;
    }

    public function parseNumbers($destination_numbers) {
        if (!is_array($destination_numbers)) {
            $destination_numbers = trim($destination_numbers);
            $dest_length = strlen($destination_numbers);

            $numbers = array();
            $sort_numbers = array();
            $destination_numbers = str_replace(array(",","\n"),";",$destination_numbers);
            $sort_numbers = explode(';',$destination_numbers);
        }
        else {
            $numbers = array();
            $sort_numbers = array();
            $sort_numbers = $destination_numbers;
        }

        foreach ($sort_numbers as $arInd) {
            $arInd = trim($arInd);

            $symbol = false;
            $spec_sym = array("+", "(", ")", " ", "-","_");
            for($i = 0; $i < strlen($arInd); $i++) {
                if (!is_numeric($arInd[$i]) && !in_array($arInd[$i],$spec_sym)) {
                    $symbol = true;
                }
            }

            if ($symbol) {
                $numbers[] = $arInd;
            } else {
                $arInd = str_replace($spec_sym, "", $arInd);

                if (strlen($arInd) < 4 || strlen($arInd) > 15) {
                    continue;
                } else {
                    if (strlen($arInd) == 10 && $arInd[0] == '9') {
                        $arInd = '7'.$arInd;
                    }
                    if (strlen($arInd) == 11 && $arInd[0] == '8') {
                        $arInd[0]="7";
                    }
                    $numbers[]=$arInd;
                }
            }
        }

        return array_unique($numbers);
    }
}

if (!function_exists("json_decode")) {

    define("JSON_OBJECT_AS_ARRAY", 1); // undocumented
    define("JSON_BIGINT_AS_STRING", 2); // 5.4.0
    define("JSON_PARSE_JAVASCRIPT", 4); // unquoted object keys, and single quotes ' strings identical to double quoted, more relaxed parsing

    function json_decode($json, $assoc = FALSE, $limit = 512, $options = 0, /*emu_args*/
                         $n = 0, $state = 0, $waitfor = 0)
    {
        global ${'.json_last_error'};
        ${'.json_last_error'} = JSON_ERROR_NONE;

        #-- result var
        $val = NULL;
        $FAILURE = array( /*$val:=*/
            NULL, /*$n:=*/
            1 << 31);
        static $lang_eq = array("true" => TRUE, "false" => FALSE, "null" => NULL);
        static $str_eq = array("n" => "\012", "r" => "\015", "\\" => "\\", '"' => '"', "f" => "\f", "b" => "\010", "t" => "\t", "/" => "/");
        if ($limit < 0) {
            ${'.json_last_error'} = JSON_ERROR_DEPTH;
            return /* __cannot_compensate */
                ;
        }

        #-- strip UTF-8 BOM (the native version doesn't do this, but .. should)
        while (strncmp($json, "\xEF\xBB\xBF", 3) == 0) {
            trigger_error("UTF-8 BOM prefaces JSON, that's invalid for PHPs native json_decode", E_USER_ERROR);
            $json = substr($json, 3);
        }

        #-- flat char-wise parsing
        for ( /*$n=0,*/
            $len = strlen($json); $n < $len; /*$n++*/) {
            $c = $json[$n];

            #-= in-string
            if ($state === '"' or $state === "'") {

                if ($c == '\\') {
                    $c = $json[++$n];

                    // simple C escapes
                    if (isset($str_eq[$c])) {
                        $val .= $str_eq[$c];
                    } // here we transform \uXXXX Unicode (always 4 nibbles) references to UTF-8
                    elseif ($c == "u") {
                        // read just 16bit (therefore value can't be negative)
                        $hex = hexdec(substr($json, $n + 1, 4));
                        $n += 4;
                        // Unicode ranges
                        if ($hex < 0x80) { // plain ASCII character
                            $val .= chr($hex);
                        } elseif ($hex < 0x800) { // 110xxxxx 10xxxxxx
                            $val .= chr(0xC0 + $hex >> 6) . chr(0x80 + $hex & 63);
                        } elseif ($hex <= 0xFFFF) { // 1110xxxx 10xxxxxx 10xxxxxx
                            $val .= chr(0xE0 + $hex >> 12) . chr(0x80 + ($hex >> 6) & 63) . chr(0x80 + $hex & 63);
                        }
                        // other ranges, like 0x1FFFFF=0xF0, 0x3FFFFFF=0xF8 and 0x7FFFFFFF=0xFC do not apply
                    } // for JS (not JSON) the extraneous backslash just gets omitted
                    elseif ($options & JSON_PARSE_JAVASCRIPT) {
                        if (is_numeric($c) and preg_match("/[0-3][0-7][0-7]|[0-7]{1,2}/", substr($json, $n), $m)) {
                            $val .= chr(octdec($m[0]));
                            $n += strlen($m[0]) - 1;
                        } else {
                            $val .= $c;
                        }
                    } // redundant backslashes disallowed in JSON
                    else {
                        $val .= "\\$c";
                        ${'.json_last_error'} = JSON_ERROR_CTRL_CHAR; // not quite, but
                        trigger_error("Invalid backslash escape for JSON \\$c", E_USER_WARNING);
                        return $FAILURE;
                    }
                } // end of string
                elseif ($c == $state) {
                    $state = 0;
                } //@COMPAT: specialchars check - but native json doesn't do it?
                #elseif (ord($c) < 32) && !in_array($c, $str_eq)) {
                #   ${'.json_last_error'} = JSON_ERROR_CTRL_CHAR;
                #}

                // a single character was found
                else /*if (ord($c) >= 32)*/ {
                    $val .= $c;
                }
            } #-> end of sub-call (array/object)
            elseif ($waitfor && (strpos($waitfor, $c) !== false)) {
                return array($val, $n); // return current value and state
            } #-= in-array
            elseif ($state === ']') {
                list($v, $n) = json_decode($json, $assoc, $limit, $options, $n, 0, ",]");
                $val[] = $v;
                if ($json[$n] == "]") {
                    return array($val, $n);
                }
            } #-= in-object
            elseif ($state === '}') {
                // quick regex parsing cheat for unquoted JS object keys
                if ($options & JSON_PARSE_JAVASCRIPT and $c != '"' and preg_match("/^\s*(?!\d)(\w\pL*)\s*/u", substr($json, $n), $m)) {
                    $i = $m[1];
                    $n = $n + strlen($m[0]);
                } else {
                    // this allowed non-string indicies
                    list($i, $n) = json_decode($json, $assoc, $limit, $options, $n, 0, ":");
                }
                list($v, $n) = json_decode($json, $assoc, $limit, $options, $n + 1, 0, ",}");
                $val[$i] = $v;
                if ($json[$n] == "}") {
                    return array($val, $n);
                }
            } #-- looking for next item (0)
            else {

                #-> whitespace
                if (preg_match("/\s/", $c)) {
                    // skip
                } #-> string begin
                elseif ($c == '"') {
                    $state = $c;
                } #-> object
                elseif ($c == "{") {
                    list($val, $n) = json_decode($json, $assoc, $limit - 1, $options, $n + 1, '}', "}");

                    if ($val && $n) {
                        $val = $assoc ? (array)$val : (object)$val;
                    }
                } #-> array
                elseif ($c == "[") {
                    list($val, $n) = json_decode($json, $assoc, $limit - 1, $options, $n + 1, ']', "]");
                } #-> numbers
                elseif (preg_match("#^(-?\d+(?:\.\d+)?)(?:[eE]([-+]?\d+))?#", substr($json, $n), $uu)) {
                    $val = $uu[1];
                    $n += strlen($uu[0]) - 1;
                    if (strpos($val, ".")) { // float
                        $val = floatval($val);
                    } elseif ($val[0] == "0") { // oct
                        $val = octdec($val);
                    } else {
                        $toobig = strval(intval($val)) !== strval($val);
                        if ($toobig and !isset($uu[2]) and ($options & JSON_BIGINT_AS_STRING)) {
                            $val = $val; // keep lengthy numbers as string
                        } elseif ($toobig or isset($uu[2])) { // must become float anyway
                            $val = floatval($val);
                        } else { // int
                            $val = intval($val);
                        }
                    }
                    // exponent?
                    if (isset($uu[2])) {
                        $val *= pow(10, (int)$uu[2]);
                    }
                } #-> boolean or null
                elseif (preg_match("#^(true|false|null)\b#", substr($json, $n), $uu)) {
                    $val = $lang_eq[$uu[1]];
                    $n += strlen($uu[1]) - 1;
                } #-> JS-string begin
                elseif ($options & JSON_PARSE_JAVASCRIPT and $c == "'") {
                    $state = $c;
                } #-> comment
                elseif ($options & JSON_PARSE_JAVASCRIPT and ($c == "/") and ($json[$n + 1] == "*")) {
                    // just find end, skip over
                    ($n = strpos($json, "*/", $n + 1)) or ($n = strlen($json));
                } #-- parsing error
                else {
                    // PHPs native json_decode() breaks here usually and QUIETLY
                    trigger_error("json_decode: error parsing '$c' at position $n", E_USER_WARNING);
                    ${'.json_last_error'} = JSON_ERROR_SYNTAX;
                    return $waitfor ? $FAILURE : NULL;
                }

            }
            //state

            #-- next char
            if ($n === NULL) {
                ${'.json_last_error'} = JSON_ERROR_STATE_MISMATCH;
                return NULL;
            } // ooops, seems we have two failure modes
            $n++;
        }
        //for

        #-- final result
        return ($val);
    }
}
?>