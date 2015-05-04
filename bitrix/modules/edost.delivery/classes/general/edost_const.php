<?
/*********************************************************************************
Константы модуля eDost (при обновлении данный файл не переписывается)
*********************************************************************************/

//define('DELIVERY_EDOST_WEIGHT_DEFAULT', '5000'); // вес в ГРАММАХ единицы товара по умолчанию (будет использоваться, если вес у товара не задан)

//define('DELIVERY_EDOST_WEIGHT_PROPERTY_NAME', 'WEIGHT'); // название свойства (PROPERTY) товара, в котором хранится вес
//define('DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE', 'G'); // 'KG' или 'G' - единица измерения свойства (PROPERTY) товара, в котором хранится вес

//define('DELIVERY_EDOST_VOLUME_PROPERTY_NAME', 'VOLUME'); // название свойства (PROPERTY) товара, в котором хранится объем 'VOLUME' (используется, когда габариты у товаров не заданы)
//define('DELIVERY_EDOST_VOLUME_PROPERTY_RATIO', 1000); // коэффициент перевода еденицы измерения объема в еденицу изерения габаритов (пример: коэффицент = 1000, если объем в метрах кубических, а габариты в миллиметрах)

// названия свойств (PROPERTY) товара, в которых хранятся габариты
//define('DELIVERY_EDOST_LENGTH_PROPERTY_NAME', 'LENGTH');
//define('DELIVERY_EDOST_WIDTH_PROPERTY_NAME', 'WIDTH');
//define('DELIVERY_EDOST_HEIGHT_PROPERTY_NAME', 'HEIGHT');

//define('DELIVERY_EDOST_FUNCTION', 'Y'); // 'Y' - подключить файл с пользовательскими функциями 'edost_function.php'
//define('DELIVERY_EDOST_BUYER_STORE', '2876A178=1,2441=2'); // привязка пунктов выдачи eDost к складам битрикса: 'код eDost'='код битрикса',... (пример: '1234A1234=1,100=2')
//define('DELIVERY_EDOST_IGNORE_ZERO_WEIGHT', 'Y'); // 'Y' - рассчитывать доставку, если в корзине есть товар с нулевым весом
//define('DELIVERY_EDOST_ORDER_LINK', '/personal/order/make'); // страница оформления заказа (для стилей и скрипта карты)

//define('DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT', 'Y'); // 'Y' - использовать вес главного товара, если у его товарного предложения вес не задан
//define('DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT', 'Y'); // 'Y' - использовать свойства (PROPERTY) главного товара (габариты, вес и объем)

define('DELIVERY_EDOST_WRITE_LOG', 0); // 1 - запись данных расчета в лог файл через функцию CDeliveryEDOST::__WriteToLog()
define('DELIVERY_EDOST_CACHE_LIFETIME', 18000); // кэш 5 часов = 60*60*5, кэш 1 день = 60*60*24*1
?>