<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);



require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

\Bitrix\Main\Loader::includeModule("garpun.advertising");

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(garpun_advertising::getModulePath() . "/admin/menu.php");
Loc::loadMessages(garpun_advertising::getModulePath() . "/admin/algoritm_edit.php");

\Bitrix\Main\Loader::includeModule("garpun.advertising");
if (isset($_REQUEST["ALGORITM_ID"])) {
    $algoritmId = intval($_REQUEST["ALGORITM_ID"]);


    $arAlgoritm = \garpun_advertising\AlgoritmTable::getById($algoritmId)->fetch();
    ?>
    <?
    if ($arAlgoritm) {
        ?>
<div class="adm-detail-title-view-tab">
    <a href="<?=  htmlspecialcharsbx($_REQUEST["HREF"]) ?>" class="adm-btn"
        <? if (intval($arAlgoritm["AGENT2_ID"])) {
            ?>onclick="return confirm('<?= htmlspecialcharsbx(GetMessage('GARPUN_ADVERTISING_ALGORITM_RELOAD_YML_PROGRESS_ALERT')) ?>')"<? }
        ?>
           title="<?= GetMessage('GARPUN_ADVERTISING_ALGORITM_EDIT_RELOAD_YML') ?>">
               <?= GetMessage('GARPUN_ADVERTISING_ALGORITM_EDIT_RELOAD_YML') ?>
        </a>

        <?
        if (intval($arAlgoritm["AGENT2_ID"])) {
            ?><span class="notetext" style="margin-left: 20px;display: inline-block"><?php
                echo (GetMessage('GARPUN_ADVERTISING_ALGORITM_RELOAD_YML_PROGRESS', Array("#AGENT2_ID#" => $arAlgoritm["AGENT2_ID"])));
                ?></span><?
        }
        ?>
</div>
    <?
    }
}
?>