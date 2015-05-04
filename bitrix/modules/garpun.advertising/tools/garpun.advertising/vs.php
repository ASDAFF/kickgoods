<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);
define('NO_AGENT_CHECK', true);


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

\Bitrix\Main\Loader::includeModule("garpun.advertising");
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(garpun_advertising::getModulePath() . "/admin/menu.php");
Loc::loadMessages(garpun_advertising::getModulePath() . "/admin/algoritm_edit.php");

\Bitrix\Main\Loader::includeModule("garpun.advertising");
if (isset($_REQUEST["ENGINE"])) {
    $referencesList = Array(
        "ENGINE_SETTING" => Array(Array("external-accounts"), "engine", true),
    );
    $engineCode = htmlspecialcharsbx($_REQUEST["ENGINE"]);
    $references = \garpun_advertising\External\ExternalApi::getReferencesArray($referencesList);
    $externalArr = $references["ENGINE_SETTING"];
?><td class="garpun_advertising_name"><?= GetMessage("GARPUN_ADVERTISING_FILE_VS_" . $engineCode) ?></td><?
    if (isset($externalArr[$engineCode]) && !empty($externalArr[$engineCode])) {
        ?>
        
        <td>
            <select name="ENGINE_SETTING[<?= $engineCode ?>][ID]" <?= $disString_name ?> >
                <option value="" ><?=GetMessage("GARPUN_ADVERTISING_FILE_ENGINE_SETTINGS_NO")?></option>
                <?
                  $showFirst=true;
                if (is_array($externalArr[$engineCode])) {
                    foreach ($externalArr[$engineCode] as $g) {
                        if ($engineCode !== $g["engine"]) {
                            continue;
                        }
                        ?><option value="<?= $g["id"] ?>" <?
                        if ($showFirst) {
                            $showFirst=false;
                            echo " selected='' ";
                        }
                        ?> ><?= $g["title"] . " " . $g["name"] ?></option><?
                            }
                        }
                        ?>
            </select>
        </td>

        <td>

            <input type="text" name="ENGINE_SETTING[<?= $engineCode ?>][PRICE]" value="<?= $row["ENGINE_SETTING"][$engineCode]["PRICE"] ?>"> <?= GetMessage("GARPUN_ADVERTISING_ALGORITM_EDIT_IN_ACCOUNT_PRICE_".$engineCode) ?>
        </td>
        <?
    } else {
        ?><td colspan="2" id="engine_td_<?=$engineCode?>">
            <script>
                window.setTimeout(function() {
                    jsAjaxUtil.InsertDataToNode("/bitrix/tools/garpun.advertising/garpun.advertising_vs.php?ENGINE=<?= $engineCode ?>",
        "engine_<?= $engineCode ?>", true);

                }, 1000 * 10);
                 

            </script>
           <div class="bx-core-waitwindow" style="position:static"><?= GetMessage("GARPUN_ADVERTISING_FILE_EX_ACCOUNT_LIST_EMPTY") ?></div>

        </td><?
    }
}
?>