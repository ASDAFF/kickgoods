<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);
define('NO_AGENT_CHECK', true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
if (check_bitrix_sessid()) {
    echo "<script type=\"text/javascript\">\n";

    $bNoTree = True;
    $bIBlock = false;
    $IBLOCK_ID = intval($_REQUEST['IBLOCK_ID']);
    if ($IBLOCK_ID > 0) {
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        $rsIBlocks = CIBlock::GetByID($IBLOCK_ID);
        if ($arIBlock = $rsIBlocks->Fetch()) {
            $bRightBlock = CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_admin_display");
            if ($bRightBlock) {
                echo "window.parent.Tree=new Array();";
                echo "window.parent.Tree[0]=new Array();";

                $bIBlock = true;
                $db_section = CIBlockSection::GetList(array("LEFT_MARGIN" => "ASC"), array("IBLOCK_ID" => $IBLOCK_ID));
                while ($ar_section = $db_section->Fetch()) {
                    $bNoTree = False;
                    if (intval($ar_section["RIGHT_MARGIN"]) - intval($ar_section["LEFT_MARGIN"]) > 1) {
                        ?>window.parent.Tree[<? echo intval($ar_section["ID"]); ?>]=new Array();<?
                    }
                    ?>window.parent.Tree[<? echo intval($ar_section["IBLOCK_SECTION_ID"]); ?>][<? echo intval($ar_section["ID"]); ?>]=Array('<? echo CUtil::JSEscape(htmlspecialcharsbx($ar_section["NAME"])); ?>', '');
                        <?
                }
                ?> 
                    
                    
                    
                window.parent.PropertiesPropducts= new Array();
                window.parent.PropertiesScu= new Array();
                <?
                $db_properties = CIBlockProperty::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => $IBLOCK_ID));
                while ($ar_property = $db_properties->Fetch()) {                     
                    
                    ?>window.parent.PropertiesPropducts.push({"NAME":'<?echo CUtil::JSEscape(htmlspecialcharsbx( $ar_property["NAME"] . "[{$ar_property["ID"]}]")) ?>',"ID":"<?=$ar_property["ID"]?>"});                          
                        <?
                }
                $f = \CCatalog::GetByID($IBLOCK_ID);
                $SCU_ID = ($f["OFFERS_IBLOCK_ID"]) ? $f["OFFERS_IBLOCK_ID"] : false;
                if ($SCU_ID) {
                    /**/
                    ?>window.parent.PropertiesScu= new Array();<?
                    $db_properties = CIBlockProperty::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => $SCU_ID));                    
                    while ($ar_property = $db_properties->Fetch()) {
                        ?>window.parent.PropertiesScu.push({"NAME":'<?echo CUtil::JSEscape(htmlspecialcharsbx( $ar_property["NAME"] . "[{$ar_property["ID"]}]")) ?>',"ID":"<?=$ar_property["ID"]?>"});                          
<?
                    }

                    /**/
                }
            }
        }
    }
    if ($bNoTree && !$bIBlock) {
        echo "window.parent.buildNoMenu();";
    } else {
        echo "window.parent.buildMenu();";
    }

    echo "</script>";
}
?>