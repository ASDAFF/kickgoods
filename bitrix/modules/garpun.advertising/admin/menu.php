<?

if ($USER->IsAdmin()) {
    IncludeModuleLangFile(__FILE__);
    \Bitrix\Main\Loader::IncludeModule("garpun.advertising");  
    $mainUrl=(garpun_advertising\Save::issetUser())?"garpun.advertising_index.php?lang=" . LANG:"garpun.advertising_registration.php";
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "garpun.advertising",
        "sort" => 1,
        "text" => GetMessage("GARPUN_ADVERTISING_TEXT"),
        "title" => GetMessage("GARPUN_ADVERTISING_TITLE"),
        "icon" => "garpun_advertising_menu_icon_index",
        "page_icon" => "garpun_advertising_page_icon",
        "items_id" => "menu_garpun_advertising",
        "url" => $mainUrl,
        "more_url" => array('garpun.advertising_index.php','garpun.advertising_registration.php',"garpun.advertising_pay.php"),
    );
    
    
if(garpun_advertising\Save::getUser()){
    $aMenu["items"][] = array(
        "text" => GetMessage("GARPUN_ADVERTISING_EXT_LIST"),
        "items_id" => "menu_garpun_advertising_ext_list",
        "icon" => "form_menu_icon",
        "url" => "garpun.advertising_ex_system.php?lang=" . LANG,
        "page_icon" => "form_page_icon",
        "title" => GetMessage("GARPUN_ADVERTISING_ALGORITM_LIST")
    );
    $one = array(
        "text" => GetMessage("GARPUN_ADVERTISING_ALGORITM_LIST"),
        "items_id" => "menu_garpun_advertising_list",
        "icon" => "form_menu_icon",
        "url" => "garpun.advertising_algoritm_list.php?lang=" . LANG,
        "page_icon" => "form_page_icon",
        "title" => GetMessage("GARPUN_ADVERTISING_ALGORITM_LIST")
    );


    

    $mores = Array();
    $rowO = \garpun_advertising\AlgoritmTable::getList(Array( 
                "order" => Array("NAME" => "ASC"),
                "select" => Array("ID", "NAME",)));
    $mores[] = $url = "garpun.advertising_algoritm_edit.php?action=new&lang=" . LANG;
   
    $rows=\garpun_advertising\Save::getUsersAlgoritmList();
    foreach ($rows as $row) {
        $mores[] = $url = "garpun.advertising_algoritm_edit.php?ID={$row["ID"]}&action=edit&PROJECT_ID={$row["PROJECT_ID"]}&lang=" . LANG;
        $one["items"][] = array(
            "text" => $row["NAME"],
            "url" => $url,
            "items_id" => "menu_garpun_advertising_list{$row["ID"]}",
            "icon" => "form_menu_icon",
            "page_icon" => "form_page_icon",
            "title" => GetMessage("GARPUN_ADVERTISING_ALGORITM_EDIT"),
        );
    }
    $one["more_url"] = $mores;
    $aMenu["items"][] = $one;

}
    return $aMenu;
}

return false;
?>