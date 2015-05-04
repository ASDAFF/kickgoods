<?if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "yandext"?><? $strReferer1 = htmlspecialchars($_GET["referer1"]); ?><?if (!isset($_GET["referer2"]) || strlen($_GET["referer2"]) <= 0) $_GET["referer2"] = "";?><? $strReferer2 = htmlspecialchars($_GET["referer2"]); ?><? header("Content-Type: text/xml; charset=windows-1251");?><? echo "<"."?xml version=\"1.0\" encoding=\"windows-1251\"?".">"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="2015-05-01 16:58">
<shop>
<name>KICKGOODS</name>
<company>KICKGOODS</company>
<url>http://www.kickgoods.ru</url>
<platform>1C-Bitrix</platform>
<currencies>
<currency id="RUB" rate="1"/>
</currencies>
<categories>
<category id="9">Товары интернет-магазинов</category>
<category id="10" parentId="9">2015.kickgoods.ru</category>
<category id="19" parentId="9">www.kickgoods.ru</category>
<category id="11">Услуги</category>
<category id="1">Все товары</category>
<category id="15" parentId="1">3d-печать</category>
<category id="21" parentId="1">Apple Watch</category>
<category id="13" parentId="1">На главной</category>
<category id="18" parentId="1">Спорт и здоровье</category>
<category id="12" parentId="1">Стилусы</category>
<category id="20" parentId="1">Стилусы серии Adonit Jot</category>
<category id="14" parentId="1">Часы и браслеты</category>
</categories>
<offers>
</offers>
</shop>
</yml_catalog>
