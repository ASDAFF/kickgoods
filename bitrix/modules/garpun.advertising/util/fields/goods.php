<?

use Bitrix\Main\Localization\Loc;





$dir_module = str_replace(Array($_SERVER["DOCUMENT_ROOT"], "/util/fields"), "", __DIR__);
Loc::loadMessages(__FILE__);






$arIBlockIDs = array();
$rsCatalogs = CCatalog::GetList(
                array(), array('!PRODUCT_IBLOCK_ID' => 0), false, false, array('PRODUCT_IBLOCK_ID')
);
while ($arCatalog = $rsCatalogs->Fetch()) {
    $arCatalog['PRODUCT_IBLOCK_ID'] = intval($arCatalog['PRODUCT_IBLOCK_ID']);
    if (0 < $arCatalog['PRODUCT_IBLOCK_ID'])
        $arIBlockIDs[$arCatalog['PRODUCT_IBLOCK_ID']] = true;
}
$rsCatalogs = CCatalog::GetList(
                array(), array('PRODUCT_IBLOCK_ID' => 0), false, false, array('IBLOCK_ID')
);
while ($arCatalog = $rsCatalogs->Fetch()) {
    $arCatalog['IBLOCK_ID'] = intval($arCatalog['IBLOCK_ID']);
    if (0 < $arCatalog['IBLOCK_ID'])
        $arIBlockIDs[$arCatalog['IBLOCK_ID']] = true;
}
if (empty($arIBlockIDs))
    $arIBlockIDs[-1] = true;


echo GetIBlockDropDownListEx(
        isset($row["IBLOCK"]["ID_IBLOCK"]) ? $row["IBLOCK"]["ID_IBLOCK"] : 0, 'IBLOCK_TYPE_ID', 'IBLOCK_ID', array(
    'ID' => array_keys($arIBlockIDs), 'ACTIVE' => 'Y',
    'CHECK_PERMISSIONS' => 'Y', 'MIN_PERMISSION' => 'W'
        ), "ClearSelected(); BX('id_ifr').src='/bitrix/tools/garpun.advertising/garpun.advertising_util.php" . "?IBLOCK_ID=0&'+'" .
        bitrix_sessid_get() . "';", "ClearSelected(); BX('id_ifr').src='/bitrix/tools/garpun.advertising/garpun.advertising_util.php" . "?IBLOCK_ID='+this[this.selectedIndex].value+'&'+'" . bitrix_sessid_get() . "';", 'class="adm-detail-iblock-types"' . $disString, 'class="adm-detail-iblock-list"' . $disString
);
?>

<script type="text/javascript">
    var TreeSelected = new Array();
    var PropSelected = new Array();
<?
$intCountSelected = 0;
if (isset($row["SECTION"])) {

    foreach ($row["SECTION"] as $oneKey) {
        ?>TreeSelected[<? echo $intCountSelected ?>] = <? echo ((int) is_array($oneKey) ? $oneKey["ID_SECTION"] : $oneKey); ?>;
        <?
        $intCountSelected++;
    }
}
$intCountSelected = 0;
if (isset($row["PROPERTY"])) {
    foreach ($row["PROPERTY"] as $oneKey) {
        ?>PropSelected[<? echo $intCountSelected ?>] = <? echo intval($oneKey); ?>;
        <?
        $intCountSelected++;
    }
}
?>
            function ClearSelected()
            {
                TreeSelected = new Array();
            }
</script>
<?
if ($intCountSelected) {
    if (is_array($V)) {
        foreach ($V as &$oneKey) {
            ?><input type="hidden" value="<? echo intval($oneKey); ?>" name="SECTION[]" id="oldV<? echo intval($oneKey); ?>"><?
        }
    }
}
?><div id="tree"></div>
<script type="text/javascript">
    clevel = 0;
    function delOldV(obj)
    {
        if (!!obj)
        {
            var intSelKey = BX.util.array_search(obj.value, TreeSelected);
            if (obj.checked == false)
            {
                if (-1 < intSelKey)
                {
                    TreeSelected = BX.util.deleteFromArray(TreeSelected, intSelKey);
                }

                var objOldVal = BX('oldV' + obj.value);
                if (!!objOldVal)
                {
                    objOldVal.parentNode.removeChild(objOldVal);
                    objOldVal = null;
                }
            }
            else
            {
                if (-1 == intSelKey)
                {
                    TreeSelected[TreeSelected.length] = obj.value;
                }
            }
        }
    }


    function printOption(values, propSelected) {
        var bufferArray = "";
        for (p in values) {
            bufferArray += "<option value='" + values[p]["ID"] + "' ";
            if (BX.util.in_array(values[p]["ID"], propSelected)) {
                selected = true;
                bufferArray += "selected=''";
            }

            bufferArray += " >" + values[p]["NAME"] + "</option>";
        }
        return bufferArray
    }

    function printOneSelect(nameSelect, PropertiesScu, PropertiesPropducts, PropSelected) {

        var buffer = '<?= GetMessage("GARPUN_ADVERTISING_I__GOODS_BREND"); ?>';

        buffer += "<select name='" + nameSelect + "' <?= CUtil::JSEscape($disString) ?> >";
        buffer += "<option value='0' "

        buffer += " >" + '<?= GetMessage("GARPUN_ADVERTISING_I__GOODS_NOT_SELECTED"); ?>' + "</option>";
        if (PropertiesPropducts.length) {
            buffer += "<optgroup label='<?= CUtil::JSEscape(GetMessage("GARPUN_ADVERTISING_I__GOODS_PROPERTY_PRODUCTS")) ?>'>"
            buffer += printOption(PropertiesPropducts, PropSelected);
            buffer += " </optgroup> ";
        }

        if (PropertiesScu.length) {
            buffer += "<optgroup label='<?= CUtil::JSEscape(GetMessage("GARPUN_ADVERTISING_I__GOODS_PROPERTY_SCU")) ?>'>"
            buffer += printOption(PropertiesScu, PropSelected);
            buffer += " </optgroup> "
        }


        buffer += "</select>";
        return buffer;
    }

    function printSelect(nameSelect, values, selectArray) {
        var bufferArray = "";
        var selected = false;
        if (!selectArray) {
            selectArray = [];
        }


        for (p in values) {
            bufferArray += "<option value='" + values[p]["ID"] + "' ";
            if (BX.util.in_array(values[p]["ID"], selectArray)) {
                selected = true;
                bufferArray += "selected=''";
            }

            bufferArray += " >" + values[p]["NAME"] + "</option>";
        }

        var buffer = "<select name='" + nameSelect + "' multiple='multiple'  <?= CUtil::JSEscape($disString) ?> >";
        buffer += "<option value='0' "
        if (!selected) {
            buffer += "selected=''";
        }

        buffer += " >" + '<?= GetMessage("GARPUN_ADVERTISING_I__GOODS_NOT_SELECTED"); ?>' + "</option>";
        buffer += bufferArray + "</select>";
        return buffer;
    }

    function buildNoMenu()
    {
        var buffer;
        buffer = '<?= GetMessage("GARPUN_ADVERTISING_I__GOODS_TOP"); ?>';
        BX('tree', true).innerHTML = buffer;
        BX.closeWait();
    }

    function buildMenu()
    {
        var i;
        var buffer;
        var imgSpace;
        buffer = '<table border="0" cellspacing="0" cellpadding="0">';
        buffer += '<tr>';
        buffer += '<td colspan="2" valign="top" align="left"><input <?= $disString ?> type="checkbox" name="SECTION[]" value="0" id="v0"' + (BX.util.in_array(0, TreeSelected) ? ' checked' : '') + ' onclick="delOldV(this);"><label for="v0"><font class="text"><b><? echo CUtil::JSEscape(GetMessage("GARPUN_ADVERTISING_GOODS_ALL_GROUP")); ?></b></font></label></td>';
        buffer += '</tr>';
        for (i in Tree[0])
        {
            if (!Tree[i])
            {
                space = '<input <?= $disString ?> type="checkbox" name="SECTION[]" value="' + i + '" id="V' + i + '"' + (BX.util.in_array(i, TreeSelected) ? ' checked' : '') + ' onclick="delOldV(this);"><label for="V' + i + '"><font class="text">' + Tree[0][i][0] + '</font></label>';
                imgSpace = '';
            }
            else
            {
                space = '<input <?= $disString ?> type="checkbox" name="SECTION[]" value="' + i + '"' + (BX.util.in_array(i, TreeSelected) ? ' checked' : '') + ' onclick="delOldV(this);"><a href="javascript: collapse(' + i + ')"><font class="text"><b>' + Tree[0][i][0] + '</b></font></a>';
                imgSpace = '<img src="/bitrix/images/catalog/load/plus.gif" width="13" height="13" id="img_' + i + '" OnClick="collapse(' + i + ')">';
            }

            buffer += '<tr>';
            buffer += '<td width="20" valign="top" align="center">' + imgSpace + '</td>';
            buffer += '<td id="node_' + i + '">' + space + '</td>';
            buffer += '</tr>';
        }

        buffer += '</table>';
        buffer += '<table><tr><td>';
        buffer += printOneSelect("PROPERTIES_SCU[]", PropertiesScu, PropertiesPropducts, PropSelected);
        buffer += '</td></tr></table>';
        BX('tree', true).innerHTML = buffer;
        BX.adminPanel.modifyFormElements('yandex_setup_form');
        BX.closeWait();
    }

    function collapse(node)
    {
        if (!BX('table_' + node))
        {
            var i;
            var buffer;
            var imgSpace;
            buffer = '<table border="0" id="table_' + node + '" cellspacing="0" cellpadding="0">';
            for (i in Tree[node])
            {
                if (!Tree[i])
                {
                    space = '<input <?= $disString ?>  type="checkbox" name="SECTION[]" value="' + i + '" id="V' + i + '"' + (BX.util.in_array(i, TreeSelected) ? ' checked' : '') + ' onclick="delOldV(this);"><label for="V' + i + '"><font class="text">' + Tree[node][i][0] + '</font></label>';
                    imgSpace = '';
                }
                else
                {
                    space = '<input <?= $disString ?> type="checkbox" name="SECTION[]" value="' + i + '"' + (BX.util.in_array(i, TreeSelected) ? ' checked' : '') + ' onclick="delOldV(this);"><a href="javascript: collapse(' + i + ')"><font class="text"><b>' + Tree[node][i][0] + '</b></font></a>';
                    imgSpace = '<img src="/bitrix/images/catalog/load/plus.gif" width="13" height="13" id="img_' + i + '" OnClick="collapse(' + i + ')">';
                }

                buffer += '<tr>';
                buffer += '<td width="20" align="center" valign="top">' + imgSpace + '</td>';
                buffer += '<td id="node_' + i + '">' + space + '</td>';
                buffer += '</tr>';
            }

            buffer += '</table>';
            BX('node_' + node).innerHTML += buffer;
            BX('img_' + node).src = '/bitrix/images/catalog/load/minus.gif';
        }
        else
        {
            var tbl = BX('table_' + node);
            tbl.parentNode.removeChild(tbl);
            BX('img_' + node).src = '/bitrix/images/catalog/load/plus.gif';
        }
        BX.adminPanel.modifyFormElements('yandex_setup_form');
    }
</script>
<iframe src="<?= "/bitrix/tools/garpun.advertising/garpun.advertising_util.php" ?>?IBLOCK_ID=<?= isset($row["IBLOCK"]["ID_IBLOCK"]) ? $row["IBLOCK"]["ID_IBLOCK"] : 0 ?>&<? echo bitrix_sessid_get(); ?>" id="id_ifr" name="ifr" style="display:none"></iframe>

