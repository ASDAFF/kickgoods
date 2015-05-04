<?
$Button_code_name = "VS_NAME";
$disString_name = "";
if ($stateList[$row["STATE"]][$Button_code_name]["READONLY"] == "Y") {
    $disString_name = " disabled=\"\"";
}

$Button_code_price = "VS_PRICE";
$disString_price = "";
if ($stateList[$row["STATE"]][$Button_code_price]["READONLY"] == "Y") {
    $disString_price = " disabled=\"\"";
}


if (false) {
    ?><table><?
    }
    if (!isset($stateList[$row["STATE"]][$Button_code_name]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code_name]["VISIBLE"] == "Y") {
        ?><tr id="tr_VS">
            <td colspan="2">
                <label class="garpun_advertising_title_algoritm"><?= GetMessage("GARPUN_ADVERTISING_FILE_VS_NOTE") ?></label>


                <table class="garpun_advertising_block_system">
                    <tr>
                        <th><?= GetMessage("GARPUN_ADVERTISING_FILE_EX_NAME") ?></th>
                        <th><?= GetMessage("GARPUN_ADVERTISING_FILE_EX_ACCOUNT") ?></th>
                        <th><?= GetMessage("GARPUN_ADVERTISING_FILE_VS_COAST");
        ?></th>
                    </tr>
                    <?
                    $externalCreditnalsList = $references["CREDENTIALS"];
                    $externalArr = $references["ENGINE_SETTING"];
                    $emptyListEngine = Array();
                    krsort($externalCreditnalsList, SORT_STRING);
                    //var_dump($externalCreditnalsList);
                    foreach ($externalCreditnalsList as $account) {                      
                        
                        if(!is_null($row["ENGINE_SETTING"])&& !isset($row["ENGINE_SETTING"][$account["engine"]])||!in_array($account["engine"],Array("yandex","google"))){
                            
                                        continue;
                                    }
                        ?>
                        <tr class="garpun_advertising_engine" id="engine_<?= $account["engine"] ?>">
                            <td class="garpun_advertising_name"><?= GetMessage("GARPUN_ADVERTISING_FILE_VS_" . $account["engine"]) ?></td>
                            <?
                            if ($account["status"]) {
                                if (isset($externalArr[$account["engine"]]) && !empty($externalArr[$account["engine"]])) {
                                  //  var_dump($row["ENGINE_SETTING"],$row["ENGINE_SETTING"][$account["engine"]]);
                                    
                                    
                                    $showFirst=false;
                                    if(is_null($row["ENGINE_SETTING"])){
                                        $showFirst=true;
                                        
                                    }
                                    
                                 //   var_dump($showFirst);
                                    ?>

                                    <td>
                                        <select name="ENGINE_SETTING[<?= $account["engine"] ?>][ID]" <?= $disString_name ?> >
                                            <option value="" ><?=GetMessage("GARPUN_ADVERTISING_FILE_ENGINE_SETTINGS_NO")?></option>
                                            
                                            <?
                                            if (is_array($externalArr[$account["engine"]])) {
                                                foreach ($externalArr[$account["engine"]] as $g) {
                                                    /**/
                                                    
                                                    
                                                    
                                                    /**/
                                                    
                                                    if ($account["engine"] !== $g["engine"]) {
                                                        continue;
                                                    }
                                                    ?><option value="<?= $g["id"] ?>" <?
                                                    if ($g["id"] == $row["ENGINE_SETTING"][$account["engine"]]["ID"]||$showFirst) {
                                                        $showFirst=false;
                                                        echo " selected='' ";
                                                    }
                                                    ?> ><?= $g["title"] . " " . $g["name"] ?></option><?
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                        <?
                                        if($disString_name){
                                            ?><input type="hidden" name="ENGINE_SETTING[<?= $account["engine"] ?>][ID]" value="<?= $g["id"] ?>"><?
                                        }
                                        ?>
                                    </td>

                                    <td>

                                        <input type="text" name="ENGINE_SETTING[<?= $account["engine"] ?>][PRICE]" <?
                                        if($row["ENGINE_SETTING"][$account["engine"]]["ID"]<0){?> disabled="" <?}?> 
                                        value="<?= $row["ENGINE_SETTING"][$account["engine"]]["PRICE"] ?>"> <?= GetMessage("GARPUN_ADVERTISING_ALGORITM_EDIT_IN_ACCOUNT_PRICE_".$account["engine"]) ?>
                                    </td>
                                    <?
                                } else {
                                    $emptyListEngine = $account["engine"];
                                    ?><td colspan="2" id="engine_td_<?=$account["engine"]?>" >
                                        <script>
                                            window.setTimeout(function(){
                                                jsAjaxUtil.InsertDataToNode("/bitrix/tools/garpun.advertising/garpun.advertising_vs.php?ENGINE=<?=$account["engine"]?>",
                                                "engine_<?=$account["engine"]?>")
                                            },1000*10);
                                            
                                        </script>
                                       <div class="bx-core-waitwindow" style="position:static"><?= GetMessage("GARPUN_ADVERTISING_FILE_EX_ACCOUNT_LIST_EMPTY") ?></div>
                                        

                                    </td><?
                }
            } else {
                                ?><td colspan="2"><a href="/bitrix/admin/garpun.advertising_ex_system.php?lang=<?= LANG ?>" target="_blank"><?= GetMessage("GARPUN_ADVERTISING_FILE_EX_ON") ?></a></td><? }
                            ?>
                        </tr>
                    <? } ?>

                </table>            

            </td>
        </tr>    <?
                }
                if (false) {
                    ?></table><?
    }


    
    
    
