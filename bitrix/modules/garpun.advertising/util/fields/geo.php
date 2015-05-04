<?
$Button_code = "GEO";
$disString = "";
if ($stateList[$row["STATE"]][$Button_code]["READONLY"] == "Y") {
    $disString = " disabled=\"\"";
}

if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    ?><tr id="tr_VS">
        <td  colspan="2">
            <label class="garpun_advertising_title_algoritm"><?= GetMessage("GARPUN_ADVERTISING_FILE_GEO"); ?></label>

   
            <select class="garpun_advertising_geo_selector" multiple=""  name="<?= $Button_code ?>[]" 
                    data-no_results_text="<?= GetMessage("GARPUN_ADVERTISING_FILE_GEO_NOTFOUND"); 
            ?>"  data-default_single_text="<?=GetMessage("GARPUN_ADVERTISING_FILE_GEO_SETTING");?>" 
            data-default_multiple_text="<?=GetMessage("GARPUN_ADVERTISING_FILE_GEO_SETTING");?>"
            > 
                <?
                if (is_array($references["GEO"])) {
                 
                    foreach (Array("ALL", "RU", "RU-MOW", "RU-SPE") as $code) {
                        $g = $references["GEO"][$code];
                        ?><option value="<?= $g["code"] ?>" <? if (is_Array($row["GEO"])&&in_array($g["code"], $row["GEO"])) {
                echo " selected='' ";
            } ?> ><?= $g["title"] ?></option><?
                        unset($references["GEO"][$code]);
                    }
$volume=Array();
$edition=Array();
                    foreach ($references["GEO"] as $key => $r) {
                        $volume[$key] = $r['title'];
                        $edition[$key] = $r['code'];
                    }
                    array_multisort($volume, SORT_STRING,$edition , SORT_ASC);
                    foreach ($volume as $k=>$g) {
                        
                        ?><option value="<?= $k ?>" <? if (is_array($row["GEO"])&&in_array($k, $row["GEO"])) {
                echo " selected='' ";
            } ?> ><?= $g ?></option><?
                    }
                }
                ?>
            </select>

        </td>
    </tr>    <?
}



  
