<?
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;

define("NO_KEEP_STATISTIC", true);
define('NO_AGENT_CHECK', true);
define("NO_AGENT_STATISTIC", true);
define('DisableEventsCheck', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!$USER->isAdmin()) die('Go away!!!');

if (!Loader::includeModule('pull'))
{
    die('P&P module isn\'t installed');
}

$devices = array();
$res_devices = CPullPush::GetList();
while ($device = $res_devices->Fetch())
{
    $devices[] = $device;
}
$push_status = CPullOptions::GetPushStatus();

$request = Bitrix\Main\Context::getCurrent()->getRequest();

if($request->isPost() && check_bitrix_sessid())
{
    if ($request['action'] == 'send_message')
    {
        $send_result = array();
        foreach ($request['device_ids'] as $key => $value)
        {
            $arMessages[] = array(
                        "USER_ID" => $value['user_id'],
                        "TITLE" => 'Push test script',
                        "APP_ID"=> $value['app_id'],
                        "MESSAGE" => $request['message'],
                    );
            $pushMe = new CPushManager();
            $result = $pushMe->SendMessage($arMessages);
            if ($result)
            {
                $send_result[] = array(
                        'USER_ID' => $value['user_id'],
                        'APP_ID' => $value['app_id'],
                        'RESULT' => 'OK'
                    );
            }
            else
            {
                $send_result[] = array(
                        'USER_ID' => $value['user_id'],
                        'APP_ID' => $value['app_id'],
                        'RESULT' => 'FAIL'
                    );
            }
        }
        Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
        die(Json::encode($send_result));
    }
    elseif($request['action'] == 'get_status')
    {
        Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
        die(Json::encode($push_status));
    }
    elseif ($request['action'] == 'delete_me')
    {
        unlink(__FILE__);
        if(!file_exists(__FILE__))
        {
            Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
            die(Json::encode(true));
        }
        else
        {
            Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
            die(Json::encode(false));
        }
    }
}
?>
<!DOCTYPE html>
<html>
    
    <head>
        <meta charset=<?=LANG_CHARSET?>>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <script type="text/javascript" src="/bitrix/js/main/core/core.js"></script>
        <script type="text/javascript" src="/bitrix/js/main/core/core_ajax.js"></script>
        <title>Push Test Script</title>
        <style>
            @import url(http://fonts.googleapis.com/css?family=Lato:300,400,700);
            *, *:after, *:before { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; }
            body, html { font-size: 100%; padding: 0; margin: 0; height: 100%;}
            body {
                background-color: #fff;
                color: #89867e;
                font-family: 'Lato', Calibri, Arial, sans-serif;
            }
            a {
                color: #888;
                text-decoration: none;
            }

            a:hover,
            a:active {
                color: #333;
            }
            header {
                margin: 0 auto;
                padding: 2em;
                text-align: center;
            }
            header h1 {
                font-size: 2.625em;
                line-height: 1.3;
                margin: 0;
                font-weight: 300;
            }
            section {
                margin: 0 auto;
                padding: 2em 3em;
                text-align: center;
                color: #fff;
                background: #0e83cd;
                min-height: 100%;
                position: relative;
            }
            section div {
                margin-bottom: 2em;
            }
            textarea {
                min-width: 200px;
                min-height: 100px;
                border: 3px solid #fff;
                background: none;
                color: #fff;
                padding: 1em;
                font-weight: 700;
            }
            textarea:focus {
                border: 3px solid #fff;
                outline: none;
                -moz-appearance: none;
            }
            select {
                border: 3px solid #fff;
                background: none;
                color: #fff;
                padding: 1em;
                font-weight: 700;
            }
            select {
                border: 3px solid #fff;
                outline: none;
                -moz-appearance: none;
            }
            .btn {
                border: none;
                font-family: inherit;
                font-size: inherit;
                color: inherit;
                background: none;
                cursor: pointer;
                padding: 25px 80px;
                display: inline-block;
                margin: 15px 30px;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 700;
                outline: none;
                position: relative;
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                transition: all 0.3s;
                border: 3px solid #fff;
                color: #fff;
            }
            .btn:hover,
            .btn:active {
                color: #0e83cd;
                background: #fff;
            }
            .delete {
                position: absolute;
                top: -1.5em;
                right: 2em;
                width: 3em;
                height: 3em;
                background-color: #fff;
                color: #0e83cd;
                line-height: 2.8em;
                border-radius: 1.5em;
                border: .2em solid #0e83cd;
                font-weight: 700;
                cursor: pointer;
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                transition: all 0.3s;
            }
            .delete:hover {
                background-color: #f06060;
                color: #fff;
                border-color: #fff;
                box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
                -webkit-box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
                -moz-box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
            }
            .status {
                position: absolute;
                top: -1.5em;
                left: 2em;
                width: 3em;
                height: 3em;
                background-color: #fff;
                color: #0e83cd;
                line-height: 2.8em;
                border-radius: 1.5em;
                font-weight: 700;
                cursor: pointer;
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                transition: all 0.3s;
                border: .2em solid #fff;
            }
            .status:hover {
                border-color: #fff;
                box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
                -webkit-box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
                -moz-box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
            }
            .status span {
                position: absolute;
                background-color: #fff;
                min-width: 15em;
                top: 2.8em;
                left: 2.5em;
                border-radius: .5em;
                display: none;
                padding: .5em;
                color: #fff;
            }
            .status:hover span {
                display: block;
                box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
                -webkit-box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
                -moz-box-shadow: 0 0 .2em .2em rgba(50, 50, 50, 0.2);
            }
            .status.green span {
                background-color: #2ac56c;
            }
            .status.red span {
                background-color: #f06060;
            }
            .green {
                background-color: #fff;
                color: #2ac56c;
                border-color: #2ac56c;
            }
            .green:hover {
                background-color: #2ac56c;
                color: #fff;
            }
            .red {
                background-color: #fff;
                color: #f06060;
                border-color: #f06060;
            }
            .red:hover {
                background-color: #f06060;
                color: #fff;
            }
        </style>
    </head>
    <body>
    <header>
        <h1>PUSH-message test script</h1>
    </header>
    <section>
        <div>
            Choose the devices:
            <br/>
            <select id="devices" multiple name="devices" size="5">
                <?
                foreach ($devices as $key => $value)
                {
                    echo '<option value="'.$value['USER_ID'].'" data-app-id="'.$value['APP_ID'].'">'.$value['DEVICE_TYPE'].' '.$value['APP_ID'].' USER_ID='.$value['USER_ID'].'</option>';
                }
                ?>
            </select>
        </div>
        <div>
            Message text: <br/>
            <textarea id="message_text"></textarea><br/>
            <button class="btn" onclick="send_message()">Send PUSH-message</button>
        </div>
        <div id="result">
            
        </div>
        <span class="delete" onclick="deleteme()">X</span>
        <span id="status" class="status <?=($push_status?"green":"red")?>" onclick="getstatus()">
            <?=($push_status?"V":"X")?>
            <span id="status_info">
                <?=($push_status?"The PUSH messages are enabled in the module settings.":"The PUSH messages aren't enabled in the module settings.")?>
            </span>
        </span>
    </section>
    
        
    </body>
    <script type="text/javascript">
        BX.message(
             {
                bitrix_sessid: "<?=bitrix_sessid();?>"
             }
            );
        function send_message ()
        {
            if (BX('message_text').value == '')
            {
                alert('The message field should be set'); 
                return;
            }
            var selected_devices = BX('devices').selectedOptions;
            if (selected_devices.length == 0)
            {
                alert('Please select a device'); 
                return; 
            }
            var dev = [];
            for (var i = selected_devices.length - 1; i >= 0; i--)
            {
                console.log(selected_devices[i]);
                dev.push({
                    user_id: selected_devices[i].value,
                    app_id: selected_devices[i].getAttribute('data-app-id')
                });
            };
            if (dev.length <= 0) console.log('bad');
            var postData = {
                action: "send_message",
                device_ids: dev,
                message: BX('message_text').value,
                sessid: BX.bitrix_sessid()
            };

            BX.ajax({
                    url: document.location.href,
                    data: postData,
                    method: 'POST',
                    dataType: 'json',
                    timeout: 30,
                    start: true,
                    cache: false,
                    onsuccess: function(result){
                        res = BX('result');
                        console.log(result);
                        for(var key in result) {
                           console.log(result[key]);
                           resp = BX.create('DIV', {
                                html: 'The message to the device (USER_ID: '+result[key].USER_ID+' APP_ID: '+result[key].APP_ID+'): '+result[key].RESULT
                            });
                            res.appendChild(resp);
                        }
                    },
                });
        }
        function deleteme()
        {
            if (confirm('The script will be deleted. Are you sure?')) {
                var postData = {
                    action: "delete_me",
                    sessid: BX.bitrix_sessid()
                };
                BX.ajax({
                    url: document.location.href,
                    data: postData,
                    method: 'POST',
                    timeout: 30,
                    start: true,
                    cache: false,
                    onsuccess: function(result){
                        if (result=='true')
                        {
                            document.location.reload();
                        }
                        else
                        {
                            alert('Error deleting the script');
                        }
                    },
                });
            }
        }
        function getstatus()
        {
            var postData = {
                action: "get_status",
                sessid: BX.bitrix_sessid()
            };
            BX.ajax({
                url: document.location.href,
                data: postData,
                method: 'POST',
                timeout: 30,
                start: true,
                cache: false,
                onsuccess: function(result){
                    if (result=='true')
                    {
                        BX('status').innerHTML = 'V<span id="status_info">The PUSH messages are enabled in the module settings.</span>';
                        BX.addClass(BX('status'), 'green');
                        BX.removeClass(BX('status'), 'red');
                    }
                    else
                    {
                        BX('status').innerHTML = 'X<span id="status_info">The PUSH messages aren\'t enabled in the module settings.</span>';
                        BX.addClass(BX('status'), 'red');
                        BX.removeClass(BX('status'), 'green');
                    }
                },
            });
        }
    </script>
</html>