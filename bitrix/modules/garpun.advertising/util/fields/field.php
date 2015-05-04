<?
$Button_code = "NAME";
$disString = "";
if ($stateList[$row["STATE"]][$Button_code]["READONLY"] == "Y") {
    $disString = " disabled=\"\"";
}
if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    ?>
    <tr id="tr_SOURCE_C">
        <td >

            <label class="garpun_advertising_title_algoritm"><?= GetMessage("GARPUN_ADVERTISING_FILE_EDIT_NAME") ?></label>    
            <input type="text" name="<?= $Button_code ?>" <?= $disString ?> value="<?= $row["NAME"] ?>" class="garpun_advertising_design_width" >
            <div style="position: relative">
                <div style="margin-left: 450px;position: absolute; top:-40px">   
                    <?
                    ?>
                </div>
            </div>

        </td>


    </tr> 
    <?
}
// /column NAME


/*   column SOURCE  */
include "source.php";
/*  / column SOURCE  */



/*   column TYPE_ALGORITM  */
$Button_code = "TYPE_ALGORITM";
if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    $disString = "";
    if ($stateList[$row["STATE"]][$Button_code]["READONLY"] == "Y") {
        $disString = " disabled=\"\"";
    }
    ?>

    <tr id="tr_TYPE_ALGORITM">
        <td colspan="2">
            <label class="garpun_advertising_title_algoritm item_company"><?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code); ?>
                <span class="garpun_advertising_attention_text" ><?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code . "_ATTENTION"); ?></span>
            </label>
            <? $typeArr = $references["ALGORITM_TYPE"]; ?>
            <select type="text" class="garpun_advertising_design_width" name="<?= $Button_code ?>" <?= $disString ?> value="<?= $row[$Button_code] ?>">
                <?
                if (is_array($typeArr)) {
                    foreach ($typeArr as $g) {
                        ?><option value="<?= $g["code"] ?>" <?
                        if ($g["code"] == $row[$Button_code]) {
                            echo " selected='' ";
                        }
                        ?> ><?= $g["title"] ?></option><?
                            }
                        }
                        ?>
            </select>
        </td>
    </tr> 


    <?
}
/*   /column TYPE_ALGORITM  */



/*   column GEO  */
include("geo.php");
/*   /column GEO  */

/*   column VS  */
include("vs.php");
/*   /column VS  */

$Button_code = "LOADER_N_TO_D";
if ($stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    ?>


    <br><br>
    <?
    include("loader.php");
    ?>

    <br><br>
    <?
}

/*  /status D */

/* column UPDATE_TIME  */
$Button_code = "UPDATE_TIME";
if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    $disString = "";
    if ($stateList[$row["STATE"]][$Button_code]["READONLY"] == "Y") {
        $disString = " disabled=\"\"";
    }
    ?>

    <tr>
        <td colspan="2" id="td_<?= $Button_code ?>">                

            <div class="garpun_advertising_block_finished">
                <div class="garpun_advertising_item">
                    <label><?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code) ?></label>
                    <ul>

                        <?
                        foreach ($references["UPDATE_TIME"] as $reference) {
                            ?>
                            <li><input type="radio" name="<?= $Button_code ?>" <?= $disString ?> id="id_<?= $reference["code"] ?>" value="<?= $reference["code"] ?>" <?
                    if ($reference["code"] == $row[$Button_code]) {
                        echo "checked=\"cheched\"";
                    }
                            ?> >
                                <label for="id_<?= $reference["code"] ?>"><?= $reference["title"] ?></label></li>
                        <? } ?>
                    </ul>

                </div>
            </div>
            <?
            echo BeginNote();
            echo GetMessage("GARPUN_ADVERTISING_FILE_UPDATE_TIME_NOTE");
            echo endNote();
            ?>
        </td>
    </tr>

    <?
}
/* /column UPDATE_TIME  */




/*  start_crusader  */
$tabControl->Buttons();

$Button_code = "BUTTON_R_TO_S"; //.$row["STATE"];
if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    ?>




    <input type="submit" class="adm-btn-save" id="ID_<?= $Button_code ?>"  name="STATE[S]"  value="<?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code) ?>"> 





    <?
}
/*  /start_crusader  */

/*  stop_crusader  */

$Button_code = "BUTTON_S_TO_R"; //.$row["STATE"];
if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    ?>




          <!--  <input type="submit"  id="ID_<?= $Button_code ?>"  name="STATE[R]"  value="<?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code) ?>">  -->



    <?
    $Button_code = "BUTTON_APPLAY";
    if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
        ?>
        <input type="submit" class="adm-btn-save"  id="ID_<?= $Button_code ?>"  name="STATE[<?= $row["STATE"] ?>]"  value="<?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code) ?>"> 
        <?
    }
    ?>



    <?
}

/*  /stop_crusader  */


$Button_code = "BUTTON_N_TO_D";
if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    ?>
    <input type="submit" class="adm-btn-save"  id="ID_<?= $Button_code ?>"  name="STATE[D]"  value="<?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code) ?>"> 
    <?
}


$Button_code = "BUTTON_D_TO_LIST";
if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
    ?>
    <input type="submit" class="adm-btn-save"  id="ID_<?= $Button_code ?>"  name="notsave"  value="<?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code) ?>"> 
    <?
}



/*
$Button_code = "BUTTON_CANCEL";
?>
<input type="submit"   id="ID_<?= $Button_code ?>"  name="notsave"  value="<?= GetMessage("GARPUN_ADVERTISING_FILE_" . $Button_code) ?>"> 
 */