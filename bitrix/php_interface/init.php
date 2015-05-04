<?

###

define("CATALOG_SERVICES_ID","2114");



### Events

AddEventHandler("main","OnEpilog","ERROR_404");
AddEventHandler("main","OnEpilog","META_ROBOTS");
AddEventHandler("main","OnEpilog","META_OG");

function ERROR_404()
{
	global $APPLICATION;

	if (defined("ERROR_404") && ERROR_404 == "Y")
	{
		$APPLICATION->SetTitle("404 &#150; Страница не найдена");
		CHTTP::SetStatus("404 Not Found");
	}
}

function META_ROBOTS()
{
	global $APPLICATION;

	$NOINDEX = $APPLICATION->GetProperty("NOINDEX");

	if ($NOINDEX == "Y")
	{
		$CONTENT = "noindex, nofollow";
	}
	else
	{
		$CONTENT = "all";
	}

	$APPLICATION->SetPageProperty("robots",$CONTENT);
}

function META_OG()
{
	global $APPLICATION;

	$og_url = $APPLICATION->GetProperty("og_url");
	if (!empty($og_url))
	{
		$APPLICATION->AddHeadString('<meta property="og:url" content="'.$og_url.'" />',true);
	}

	$og_title = $APPLICATION->GetProperty("og_title");
	if (!empty($og_title))
	{
		$APPLICATION->AddHeadString('<meta property="og:title" content="'.$og_title.'" />',true);
	}
	
	$og_description = $APPLICATION->GetProperty("og_description");
	if (!empty($og_description))
	{
		$APPLICATION->AddHeadString('<meta property="og:description" content="'.$og_description.'" />',true);
	}
	
	$og_image = $APPLICATION->GetProperty("og_image");
	if (!empty($og_image))
	{
		$APPLICATION->AddHeadString('<meta property="og:image" content="'.$og_image.'" />',true);
	}
}



###

function SHOW_TITLE_H1()
{
	global $APPLICATION;

	$NOT_SHOW_TITLE_H1 = $APPLICATION->GetProperty("NOT_SHOW_TITLE_H1");

	if ($NOT_SHOW_TITLE_H1 != "Y")
	{
		$CLASS_PAGE_TITLE = $APPLICATION->GetProperty("CLASS_PAGE_TITLE");
		
		$TITLE_H1 = '<h1 class="page-title '.$CLASS_PAGE_TITLE.'">'.$APPLICATION->GetTitle("").'</h1>';
	}

	return $TITLE_H1;
}



### Functions

function object2array($object)
{
	if (!is_object($object) && !is_array($object))
	{
		return $object;
	}

	if (is_object($object))
	{
		$object = get_object_vars($object);
	}

	return array_map("object2array",$object);
}

function print_array($array,$return=false)
{
	if (sizeof($array) > 0)
	{
		$HTML = '<pre>';
		$HTML .= print_r($array,true);
		$HTML .= '</pre>';
	}

	if ($return)
	{
		return $HTML;
	}
	else
	{
		echo $HTML;
	}
}

function print_object($object)
{
	if (sizeof($object) > 0)
	{
		echo '<pre>';
		var_dump($object);
		echo '</pre>';
	}
}



//-- Добавление обработчика события —— Добавил Данияр 12 апреля 2015

AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");

//-- Собственно обработчик события

function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
  $arOrder = CSaleOrder::GetByID($orderID);
  
  //-- получаем телефоны и адрес
  $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
  $phone="";
  $index = ""; 
  $country_name = "";
  $city_name = "";  
  $address = "";
  while ($arProps = $order_props->Fetch())
  {
    if ($arProps["CODE"] == "PHONE")
    {
       $phone = htmlspecialchars($arProps["VALUE"]);
    }
    if ($arProps["CODE"] == "LOCATION")
    {
        $arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
        $country_name =  $arLocs["COUNTRY_NAME_ORIG"];
        $city_name = $arLocs["CITY_NAME_ORIG"];
    }

    if ($arProps["CODE"] == "INDEX")
    {
      $index = $arProps["VALUE"];   
    }

    if ($arProps["CODE"] == "ADDRESS")
    {
      $address = $arProps["VALUE"];
    }
  }

  $full_address = $index."".$country_name.", ".$city_name.", ".$address;

  //-- получаем название службы доставки
  $arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
  $delivery_name = "";
  if ($arDeliv)
  {
    $delivery_name = $arDeliv["NAME"];
  }

  //-- получаем название платежной системы   
  $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
  $pay_system_name = "";
  if ($arPaySystem)
  {
    $pay_system_name = $arPaySystem["NAME"];
  }

  //-- добавляем новые поля в массив результатов
  $arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"]; 
  $arFields["PHONE"] =  $phone;
  $arFields["DELIVERY_NAME"] =  $delivery_name;
  $arFields["PAY_SYSTEM_NAME"] =  $pay_system_name;
  $arFields["FULL_ADDRESS"] = $full_address;
}

//-- Добавление обработчика события —— Добавил Данияр 12 апреля 2015

?>