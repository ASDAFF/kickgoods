<?
$Button_code = "SOURCE";

if (false) {
    ?><table><?
    }
    if (!isset($stateList[$row["STATE"]][$Button_code]["VISIBLE"]) || $stateList[$row["STATE"]][$Button_code]["VISIBLE"] == "Y") {
        $disString = "";
        if ($stateList[$row["STATE"]][$Button_code]["READONLY"] == "Y") {
            $disString = " disabled=\"\"";
        }
        ?>

        <tr id="tr_SOURCE">
            <td colspan="2">  
                <input type="hidden" id="TYPE_VALUE" name="TYPE" <?= $disString ?>  value="<?
                if ($row["TYPE"] == "C") {
                    echo "C";
                } else {
                    echo "Y";
                }
                ?>" >  
                <label class="garpun_advertising_title_algoritm"><?= GetMessage("GARPUN_ADVERTISING_FILE_SOURCE") ?></label>
                <?
                $aSourceTabs = array(
                    array("DIV" => "source_YS", "TAB" => GetMessage("GARPUN_ADVERTISING_FILE_SOURCE_Y"), "ONSELECT" => "BX('TYPE_VALUE').value='Y'"),
                    array("DIV" => "source_CS", "TAB" => GetMessage("GARPUN_ADVERTISING_FILE_SOURCE_C"), "ONSELECT" => "BX('TYPE_VALUE').value='C'"),
                );

                $tabSourceControl = new CAdminViewTabControl("tabControlSource", $aSourceTabs, false);
                $tabSourceControl->Begin();
                $tabSourceControl->BeginNextTab();
                ?>

                <div class="garpun_advertising_attention_text bordered_block">
                    <?= GetMessage("GARPUN_ADVERTISING_FILE_SOURCE_Y_ATTENTION") ?>   
                </div>
                <div>


                    <input type="text" id="<?= $Button_code ?>" placeholder="<? echo GetMessage("GARPUN_ADVERTISING_FILE_SOURCE_Y_PLACEHOLDER") ?>"   <?= $disString ?> name="PATH" size="30" value="<?= $row["PATH"] ?>"  class="garpun_advertising_design_width">
                    <input type="button" <?= $disString ?>  value="<? echo GetMessage("GARPUN_ADVERTISING_FILE_SOURCE_Y_OPEN") ?>" OnClick="BtnClick()">
                    <?=
                    CAdminFileDialog::ShowScript
                            (
                            Array(
                                "event" => "BtnClick",
                                "arResultDest" => array("FORM_NAME" => "file_edit_form", "FORM_ELEMENT_NAME" => "$Button_code"),
                                "arPath" => "/",
                                "select" => 'F', // F - file only, D - folder only
                                "operation" => 'S',
                                "showUploadTab" => true,
                                "showAddToMenuTab" => false,
                                "fileFilter" => 'xml',
                                "allowAllFiles" => true,
                                "SaveConfig" => true,
                            )
                    );
                    ?></div>
                <?
                $tabSourceControl->BeginNextTab();
                ?>  <div class="garpun_advertising_attention_text bordered_block">

                    <? if ($row["TYPE"] == "C" && intval($row["ID"]) > 0) { ?>                        



                        <div  id="garpun_advertising_reloadXML">


                        </div><? }
                    ?>

                    <?= GetMessage("GARPUN_ADVERTISING_FILE_SOURCE_C_ATTENTION") ?>
                    <script>
                        garpun_advertising_reloadXml();
                        function garpun_advertising_reloadXml() {
                            jsAjaxUtil.InsertDataToNode("/bitrix/tools/garpun.advertising/garpun.advertising_reloadXml.php?ALGORITM_ID=<?= $row["ID"] ?>&HREF=<?= urlencode($APPLICATION->GetCurPageParam("action=reloadXML&" . bitrix_sessid_get(), array("action"))) ?>",
                                    "garpun_advertising_reloadXML", true);

                        }

                        window.setInterval(function() {
                            garpun_advertising_reloadXml();
                        }, 1000 * 10);


                    </script>
                </div><?
                if (CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")) {
                    ?><div><? include('goods.php'); ?></div><?
                }
                $tabSourceControl->End();
                ?>




            </td>     
        </tr>
        <?
    }