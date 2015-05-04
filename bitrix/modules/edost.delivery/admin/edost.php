<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/include.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/prolog.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/general/admin_tool.php'); // ������� ��� ������ � ��������

IncludeModuleLangFile(__FILE__);


if ($APPLICATION->GetGroupRight('sale') == 'D') {
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
	return;
}


//$develop = true; // ����� ���������� !!!!!
if (isset($develop)) require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/edost.delivery/admin/doc.php');


// ������ �� �������� ������
$setting_data = GetMessage('EDOST_ADMIN_SETTING');
$sign = GetMessage('EDOST_ADMIN_SIGN');
$info_107 = GetMessage('EDOST_ADMIN_107_INFO');
$flag = GetMessage('EDOST_ADMIN_ORDER_FLAG');
$button = GetMessage('EDOST_ADMIN_BUTTON');
$rename = GetMessage('EDOST_ADMIN_RENAME');
$warning = GetMessage('EDOST_ADMIN_WARNING');

$module_first_start = false; // ������ ������ (�� ������ �� ��� ������)
$warning_key = '';
$module_server_key = array('id', 'ps', 'host');


// �������� �������� ������
$ar = COption::GetOptionString('edost.delivery', 'document_setting', '');
$ar = ($ar != '' ? explode('|', $ar) : array());
$setting = array(
	'complete_status' => 'none', // ������ ����������� ������
	'cod' => '', // ��� ������� ������ ���������� �������� (�� ��������� '' - �� �����)
	'show_order_id' => 'Y', // �������� � ������-������� ���� ������ ����� ������
	'info_color' => 'BBB', // ���� ��������� ������ (����� ������)
	'insurance_107' => 'N', // �������� ����� ��� ����������� '�� ����������' (�� ��������� ����� ���������� ������ ��� �������)
	'browser' => 'ie', // 'ie', 'firefox', 'opera', 'chrome', 'yandex'
	'docs_disable' => '7a', // ��������������� ������ (����� ����������� ������ �������)
	'duplex' => 'Y', // 'Y' - 'back' �������� � �������� �������
	'show_status' => 'N,P', // ������� �������, ��������� ��� ������
	'show_allow_delivery' => '', // 'Y' - ���������� ������ ����������� � �������� ������
	'hide_deducted' => '', // 'Y' - �������� ����������� ������ (��� bitrix �� 12.5)
	'deducted' => '', // 'Y' - ����� ������ �������, ��������� �������� ������ (��� bitrix �� 12.5)
	'hide_unpaid' => 'Y', // 'Y' - �������� �� ���������� ������ (��� ����������� �������)
	'hide_without_doc' => 'N', // 'Y' - �������� ������ ��� ����������
	'duplex_x' => '0', // �������� ��� �������� ������� ������ �� ������������ � �����������
//	'window_mode' => '0', // '0' - ��������� ��������� ������� � ������ �����, '1' - ��������� � ����� ���� � �������� ������������, '2' - ������ ����� � ����� ���� � ��������� ������������
//	'history' => 0, // ���� ����, � ������� �������� ������ �� ������� ����������
//	'compact' => 'N', // ���������� ���������� ������� �� ����� (�������� ������)
);
$i = 0;
foreach ($setting as $k => $v) {
	$setting[$k] = (isset($ar[$i]) ? $ar[$i] : $v);
	$i++;
}
$show_status = ($setting['show_status'] != '' ? explode(',', $setting['show_status']) : array());
$docs_disable = ($setting['docs_disable'] != '' ? explode(',', $setting['docs_disable']) : array());
//echo '<br><b>setting:</b><pre style="font-size: 12px">'.print_r($setting, true).'</pre>';


// �������� ��������� �������� �� cookie
$ar = (isset($_COOKIE['edost_admin']) && $_COOKIE['edost_admin'] != '' ? explode('|', preg_replace("/[^0-9a-z_|-]/i", "", $_COOKIE['edost_admin'])) : array());
$setting_cookie = array(
	'filter_days' => '', // ������ ����������� �� ��������� 'filter_days' ����
	'docs_active' => '', // �������� ��������� ��� ������ ������
	'setting_active' => 'module', // �������� ��������� (module, paysystem, document)
	'setting_tariff_show' => 'N', // ������������� �������� ������� (Y, N)
);
$i = 0;
foreach ($setting_cookie as $k => $v) {
	$setting_cookie[$k] = (isset($ar[$i]) ? $ar[$i] : $v);
	$i++;
}
if (!isset($setting_data['filter_days'][$setting_cookie['filter_days']])) $setting_cookie['filter_days'] = 5;
$setting_cookie['docs_active'] = ($setting_cookie['docs_active'] != '' ? explode('-', $setting_cookie['docs_active']) : array());
//echo '<br><b>setting_cookie:</b><pre style="font-size: 12px">'.print_r($setting_cookie, true).'</pre>';


// �������� ����������� � ����������� �� ������ bitrix
$ar = explode('.', SM_VERSION);
$deducted_enabled = (isset($ar[1]) && ($ar[0] >= 14 || ($ar[0] == 12 && $ar[1] >= 5)) ? true : false); // �������� �� ����������� �������� ������� (��������� � bitrix 12.5)
$delivery2paysystem = (isset($ar[1]) && $ar[0] >= 14 ? true : false); // �������� �� ����������� �������� �������� � ������ (��������� � bitrix 14)
$location_pro = ($ar[0] >= 15 && method_exists('CSaleLocation', 'isLocationProMigrated') && CSaleLocation::isLocationProMigrated() ? true : false); // �������� �� �������������� 2.0 (��������� � bitrix 15)
if ($deducted_enabled && $setting['deducted'] == 'Y') {
	global $USER;
	$user_groups = $USER->GetUserGroupArray();
}


// ������� ������� ��������
$order_status = array('none' => $setting_data['status_no_change']);
$ar = CSaleStatus::GetList(array('SORT' => 'ASC'), array('LID' => LANGUAGE_ID), false, false, array('ID', 'NAME'));
while ($v = $ar->Fetch()) $order_status[$v['ID']] = str_replace(array('<', '>'), array('&lt;', '&gt;'), $v['NAME']);
if (!isset($order_status[$setting['complete_status']])) $setting['complete_status'] = 'none'; // ���������� ��������� �������, ���� ������ ������� �� ����������
//echo '<br><b>order_status:</b><pre style="font-size: 12px">'.print_r($order_status, true).'</pre>';


// ������� ������ ��������
$pay_system = array();
$ar = CSalePaySystem::GetList(array('SORT' => 'ASC', 'PSA_NAME' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array('ID', 'NAME', 'PSA_ACTION_FILE'));
while ($v = $ar->Fetch()) {
	$p = array('name' => str_replace(array('<', '>'), array('&lt;', '&gt;'), $v['NAME']));
    if (substr($v['PSA_ACTION_FILE'], -11) == 'edostpaycod') {
		if ($setting['cod'] === '') $setting['cod'] = $v['ID']; // ��������� �� ��������� ����������� ������� edost
		$p['cod'] = true;
	}
	$pay_system[$v['ID']] = $p;
}
//echo '<br><b>pay_system:</b><pre style="font-size: 12px">'.print_r($pay_system, true).'</pre>';


// ����� ��������
$bitrix_site = array('all' => array('ID' => 'all'));
$ar = CSite::GetList($p1 = 'sort', $p2 = 'asc', array());
while ($v = $ar->Fetch()) $bitrix_site[] = array('ID' => $v['ID'], 'name' => $v['NAME']);
//echo '<br><b>bitrix_site:</b><pre style="font-size: 12px">'.print_r($bitrix_site, true).'</pre>';


// �������� ��� ������ '������� �������� ������' (�������� � ����������� �� ��������)
$button_print = $button['print']['name'];
if ($setting['complete_status'] == 'none' && $setting['deducted'] == 'Y') $button_print .= $button['print']['deducted'];
else if ($setting['complete_status'] != 'none' && $setting['deducted'] != 'Y') $button_print .= $button['print']['status'].' ['.$order_status[$setting['complete_status']].']';
else if ($setting['complete_status'] != 'none' && $setting['deducted'] == 'Y') $button_print .= $button['print']['status_deducted'].' ['.$order_status[$setting['complete_status']].']';


// ������ �� POST � GET
$ajax = (isset($_POST['ajax']) && $_POST['ajax'] == 'Y' ? true : false);
$update_allow_delivery = (isset($_POST['update_allow_delivery']) && $_POST['update_allow_delivery'] == 'Y' ? true : false);
$update_status = (isset($_POST['update_status']) && $_POST['update_status'] == 'Y' ? true : false);
$print = (isset($_GET['mode']) ? true : false);

$ar = array('mode' => '', 'id' => '', 'doc' => '');
foreach ($ar as $k => $v) if ($print) $ar[$k] = (isset($_GET[$k]) ? $_GET[$k] : ''); else $ar[$k] = (isset($_POST[$k]) ? $_POST[$k] : '');
$mode = ($ar['mode'] != '' ? preg_replace("/[^a-z|_]/i", "", substr($ar['mode'], 0, 30)) : '');
$orders_id = ($ar['id'] != '' ? explode('|', preg_replace("/[^0-9|]/i", "", $ar['id'])) : false);
$docs_active = ($ar['doc'] != '' ? explode('|', preg_replace("/[^0-9a-z|]/i", "", $ar['doc'])) : false);

$setting_module_show = (isset($_GET['setting_module']) && $_GET['setting_module'] == 'Y' ? true : false);
if ($setting_module_show) $setting_cookie['setting_active'] = 'module';

$currency = CCurrencyLang::GetCurrencyFormat('RUB');
$decimals = $currency['DECIMALS'];


// �������� �������� ����������
$docs = array();
$cache = new CPHPCache();
if ($cache->InitCache((isset($_POST['update_docs']) && $_POST['update_docs'] == 'Y' ? 1 : 86400), 'sale|11.0.0|edost_delivery|doc', '/')) if (!isset($develop)) $docs = $cache->GetVars();
if (!is_array($docs) || count($docs) == 0) {
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/edost.delivery/classes/general/delivery_edost.php');
	if (isset($develop)) {
		$data = edost_class::ParseData($data, 'document');
		$docs = $data['data'];
	}
	else {
		$config = COption::GetOptionString('edost.delivery', 'module_setting', '');
		if ($config != '') $config = unserialize($config);
		if (!is_array($config) || count($config) == 0) $module_first_start = true;
		else foreach ($config as $v) {
			$s = explode(';', $v);
			$data = edost_class::RequestData('', $s[0], isset($s[1]) ? $s[1] : '', 'type=document&browser='.$setting['browser'].'&charset='.urlencode(SITE_CHARSET), 'document');
			if (isset($data['error'])) {
				if ($data['error'] == 12) $module_first_start = true;
			}
			else if (isset($data['data']) && count($data['data']) > 5) {
				$module_first_start = false;
				$docs = $data['data'];
				$cache->StartDataCache();
				$cache->EndDataCache($docs);
				break;
			}
		}
	}
}
$show = (count($docs) > 0 ? true : false);
//echo '<br><b>docs:</b><pre style="font-size: 12px">'.print_r($docs, true).'</pre>';


// �������� ���������� �� �������� "��������� �������� ����" - bitrix/admin/sale_report_edit.php
$ar = '';
$n = IntVal(COption::GetOptionInt('sale', 'reports_count'));
if (!($n > 0)) $ar = COption::GetOptionString('sale', 'reports');
else for ($i = 1; $i <= $n; $i++) $ar .= COption::GetOptionString('sale', 'reports'.$i);
$shop = unserialize($ar);

// ���������  �����������
$ar = array_fill_keys(array('INDEX', 'COMPANY_NAME', 'ADDRESS', 'CITY', 'INN', 'KSCH', 'RSCH_BANK', 'RSCH', 'BIK'), '');
foreach ($ar as $k => $v) $ar[$k] = (isset($shop[$k]['TYPE']) && $shop[$k]['TYPE'] == '' ? $shop[$k]['VALUE'] : '');
$shop_field = array(
	'company_name' => $ar['COMPANY_NAME'],
	'company_address' => $ar['ADDRESS'].($ar['ADDRESS'] != '' && $ar['CITY'] != '' ? ', ' : '').$ar['CITY'],
	'company_zip' => $ar['INDEX'],
	'company_inn' => $ar['INN'],
	'company_ksch' => $ar['KSCH'],
	'company_bank' => $ar['RSCH_BANK'],
	'company_rsch' => $ar['RSCH'],
	'company_bik' => $ar['BIK'],
);
$shop_warning = ($shop_field['company_name'] != '' && $shop_field['company_address'] != '' && $shop_field['company_zip'] != '' ? false : true);

// ���������� ������ �����������
$passport = explode('|', COption::GetOptionString('edost.delivery', 'document_passport', $setting_data['passport'][0]['default'].'|||||')); // �������|�����|�|����� ���� � �����|����� ���|��� �����
foreach ($passport as $k => $v) $shop_field['passport_'.$k] = $v;

// ����� ������� ����������
$ar = array_fill_keys(array('BUYER_COMPANY_NAME', 'BUYER_FIRST_NAME', 'BUYER_SECOND_NAME', 'BUYER_LAST_NAME', 'BUYER_ADDRESS', 'BUYER_CITY', 'BUYER_INDEX'), '');
foreach ($ar as $k => $v) $ar[$k] = (isset($shop[$k]['TYPE']) && $shop[$k]['TYPE'] == 'PROPERTY' ? $shop[$k]['VALUE'] : '');
$user_field_key = array(
	'company' => $ar['BUYER_COMPANY_NAME'],
	'name_1' => $ar['BUYER_FIRST_NAME'],
	'name_2' => $ar['BUYER_SECOND_NAME'],
	'name_3' => $ar['BUYER_LAST_NAME'],
	'address' => $ar['BUYER_ADDRESS'],
	'city' => $ar['BUYER_CITY'],
	'zip' => $ar['BUYER_INDEX'],
);
if ($user_field_key['name_1'] == '' && $user_field_key['name_2'] == '' && $user_field_key['name_3'] == '') $user_field_key['name_1'] = 'FIO';
if ($user_field_key['address'] == '') $user_field_key['address'] = 'ADDRESS';
if ($user_field_key['city'] == '') $user_field_key['city'] = 'CITY';
if ($user_field_key['zip'] == '') $user_field_key['zip'] = 'ZIP';


$ar = explode('_', $mode);
$module = (!$show || $setting_module_show || (isset($ar[1]) && ($ar[1] == 'module' || $ar[1] == 'paysystem')) ? true : false);
if ($module) {
	// �������� �������� ������ 'edost'
	$module_setting = array();
	$module_setting_id = false;
	$ar = CSaleDeliveryHandler::GetBySID('edost');
	while ($v = $ar->Fetch()) {
		$s = array('DBGETSETTINGS', 'DBSETSETTINGS', 'GETCONFIG', 'COMPABILITY', 'CALCULATOR');
		foreach ($s as $v2) unset($v[$v2]);

		foreach ($v['CONFIG']['CONFIG'] as $k2 => $v2) if (!isset($v2['VALUE']) || strlen($v2['VALUE']) == 0) $v['CONFIG']['CONFIG'][$k2]['VALUE'] = $v2['DEFAULT'];

		if (strlen($v['LID']) > 0) {
			if ($module_setting_id === false || ($module_setting[$module_setting_id]['ACTIVE'] != 'Y' && $v['ACTIVE'] == 'Y')) $module_setting_id = $v['LID'];
			$module_setting[$v['LID']] = $v;
		}
		else {
			$module_setting_id = 'all';
			$module_setting = array('all' => $v);
			break;
		}
	}
	if (count($module_setting) == 0) { echo 'ERROR: The "edost.delivery" module is not installed!'; die(); }

	// ��������� ��������� ����������, ���� ������� � ����������� ������ ��� ��� � ����
	foreach ($module_setting as $k => $v) if ($v['INSTALLED'] != 'Y') {
		$module_setting[$k]['NAME'] = '';
		$module_setting[$k]['DESCRIPTION'] = '';
		$module_setting[$k]['ACTIVE'] = 'N';
		$module_setting[$k]['SORT'] = 100;
	}

	// �������� �������, ���������� � ������ �������� eDost
	foreach ($module_setting as $k => $v) {
		$data = edost_class::RequestData('', $v['CONFIG']['CONFIG']['id']['VALUE'], $v['CONFIG']['CONFIG']['ps']['VALUE'], 'active=Y', 'delivery');
		if (isset($data['error'])) $data['error'] = CDeliveryEDOST::GetEdostError($data['error']);
		$module_setting[$k]['edost'] = $data;
	}

	// �������� �� ����� ������ ������ � �� ����� ������
	foreach ($module_setting as $k => $v) {
		$id_shop = 0;
		foreach ($v['PROFILES'] as $k2 => $v2) if ($k2 > $id_shop) $id_shop = $k2; // ������������ id ������ � ���� ��������

		if ($id_shop < DELIVERY_EDOST_TARIFF_COUNT*2) {
			// ���������� ����� ������ � ������ ��������, �� � �������� �������� ������ ������ ������� - ���������� �������� ����� ������ � ���� ��������
			$warning_key = 'tariff_update';
			$tariff = GetMessage('EDOST_DELIVERY_TARIFF');
			for ($i = $id_shop+1; $i <= DELIVERY_EDOST_TARIFF_COUNT*2; $i++) {
				$id = ceil($i/2);
				$insurance = ($i == $id*2 ? '_insurance' : '');
				$ar = $v['PROFILES'][0];
				$ar['TITLE'] = (isset($tariff['title'.$insurance][$id]) ? $tariff['title'.$insurance][$id] : '');
				$ar['DESCRIPTION'] = '';
				$module_setting[$k]['PROFILES'][$i] = $ar;
			}
		}
		else {
			$id_edost = 0;
			if (isset($v['edost']['data'])) foreach ($v['edost']['data'] as $k2 => $v2) if ($k2 > $id_edost) $id_edost = $k2; // ������������ id ������ � ������� edost
			if ($id_edost > $id_shop) {
				// ������� �� ������� edost ������, ��� � �������� - ���������� �������� ������
				$warning_key = 'module_update';
			}
		}
	}

	// ��������� �������� ������� � ������� edost
	if ($ajax && $mode == 'setting_module' && !empty($_POST['title'])) {
		// �������� �� ������ edost.catalogelivery ������ 1.1.0 - ���� ����������, ����� �������� ������� ��������� �� ������� ������� � '�� ����������' �� ��������
		$catalogdelivery_110 = false;
		if ($v = CModule::CreateModuleObject('edost.catalogdelivery')) if ($v->MODULE_VERSION === '1.1.0') $catalogdelivery_110 = true;

		foreach ($module_setting as $k => $v) if (isset($v['edost']['data']))
			foreach ($v['edost']['data'] as $k2 => $v2) if (isset($v['PROFILES'][$k2])) {
				$s = $v2['company'];
				if ($v2['name'] != '')
					if (!$catalogdelivery_110) $s .= ' ('.$v2['name'].')';
					else {
						$s2 = explode($sign['insurance'], $v2['name']);
						$s2 = trim($s2[0]);
						if ($s2 != '') $s .= ' ('.$s2.')';
						if ($v2['insurance'] == 1) $s .= ' '.$sign['insurance'];
					}
				$module_setting[$k]['PROFILES'][$k2]['TITLE'] = $s;
			}
	}

	// ������ ������� edost �� ���� ���������
	$ar = array();
	foreach ($module_setting as $k => $v) if (isset($v['edost']['data'])) foreach ($v['edost']['data'] as $k2 => $v2) $ar[$k2] = $v2;
	$module_setting['default'] = $module_setting[$module_setting_id];
	$module_setting['default']['edost']['data'] = $ar;

	// ���������� + ���������� �������� ������
	$module_tariff_count = 0;
	foreach ($module_setting as $k => $v) {
		$data = (isset($v['edost']['data']) ? $v['edost']['data'] : array());

		$n = count($data);
		if ($n > $module_tariff_count) $module_tariff_count = $n;
		if ($n > 1) {
			$ar = array();
			foreach ($data as $k2 => $v2) $ar[] = $v2['sort'];
			array_multisort($ar, SORT_ASC, SORT_NUMERIC, $data);
		}

		$ar = array(0 => '');
		foreach ($data as $k2 => $v2) $ar[$v2['profile']] = $v2;
		$module_setting[$k]['edost']['data'] = $ar;
	}

	// ����� �������� � ���������� ������
	$module_individual_server = true;
	if (!isset($module_setting['all']) && count($module_setting) > 2) {
		$ar = array('default');
		foreach ($module_setting as $k => $v) if ($k !== 'default') {
			$ar[] = $k;
			foreach ($module_setting as $k2 => $v2) if (!in_array($k2, $ar)) {
				foreach ($v2['PROFILES'] as $k3 => $v3) if ($v3['TITLE'] != $v['PROFILES'][$k3]['TITLE'] || $v3['DESCRIPTION'] != $v['PROFILES'][$k3]['DESCRIPTION']) $module_individual_server = false;
				foreach ($v2['CONFIG']['CONFIG'] as $k3 => $v3) if (!in_array($k3, array('id', 'ps', 'host')) && $v3['VALUE'] != $v['CONFIG']['CONFIG'][$k3]['VALUE']) $module_individual_server = false;
				if ($v2['NAME'] != $v['NAME'] || $v2['DESCRIPTION'] != $v['DESCRIPTION'] || $v2['SORT'] != $v['SORT']) $module_individual_server = false;
				if (!$module_individual_server) break;
			}
		}
	}
//	if (!$ajax) echo '<br><b>module_setting:</b><pre style="font-size: 12px">'.print_r($module_setting, true).'</pre>';

	// �������� �������� � ������
	if ($delivery2paysystem) foreach ($pay_system as $k => $p) {
		$p['delivery'] = array();
		$p['save'] = array();
		$p['edost'] = array();

		$radio = 'all';
		$ar = CSaleDelivery2PaySystem::GetList(array('PAYSYSTEM_ID' => $k));
		while ($v = $ar->Fetch()) {
			$delivery_id = $v['DELIVERY_ID'];
			$prifile_id = (isset($v['DELIVERY_PROFILE_ID']) && strlen($v['DELIVERY_PROFILE_ID']) > 0 ? $v['DELIVERY_PROFILE_ID'] : false);
			if ($prifile_id !== false) $delivery_id .= ':'.$prifile_id;

			$p['delivery'][] = $delivery_id;
			if ($v['DELIVERY_ID'] === 'edost') {
				$p['edost'][] = $delivery_id;

				if ($prifile_id === false) $radio = 'all';
				else if ($prifile_id == 'none') $radio = 'none';
				else $radio = 'list';
			}
			else $p['save'][] = $delivery_id;
		}
		$p['radio'] = $radio;

		$pay_system[$k] = $p;
	}
//	echo '<br><b>paysystem2delivery:</b><pre style="font-size: 12px">'.print_r($pay_system, true).'</pre>';
}


// ���������� �������� �������� � ������
if ($ajax && $mode == 'setting_paysystem_save') {
	foreach ($pay_system as $k => $v) {
		if (isset($v['cod'])) $s = 'all';
		else if (isset($_POST['paysystem_'.$k])) $s = $_POST['paysystem_'.$k];
		else continue;

		$ar = array();
		if ($s === 'all') $ar[] = 'edost';
		else if ($s === 'none' || $s === '') $ar[] = 'edost:none';
		else {
			$s = explode(',', $s);
			$data = (isset($module_setting['default']['edost']['data']) ? $module_setting['default']['edost']['data'] : array());
			if (count($s) == count($data)) $ar[] = 'edost';
			else foreach ($s as $k2 => $v2) $ar[] = 'edost:'.$v2;
		}

		if ($v['edost'] != $ar) {
			$ar = array_merge($v['save'], $ar);
			CSaleDelivery2PaySystem::UpdatePaySystem($k, $ar);
		}
	}

	die();
}


// ���������� �������� ������
if ($ajax && $mode == 'setting_module_save') {
//	echo '<br><b>_POST:</b><pre style="font-size: 12px">'.print_r($_POST, true).'</pre>';

	$individual = (isset($_POST['individual']) && $_POST['individual'] == 'Y' ? true : false);
	$individual_server = (isset($_POST['individual_server']) && $_POST['individual_server'] == 'Y' ? true : false);

	$ar = $module_setting;
	$module_setting = array();
	foreach ($bitrix_site as $site_key => $site) {
		$s = (isset($ar[$site['ID']]) ? $ar[$site['ID']] : $ar['default']);

		$s['NAME'] = trim($GLOBALS['APPLICATION']->ConvertCharset(isset($_POST['name_'.$site_key]) ? $_POST['name_'.$site_key] : '', 'utf-8', SITE_CHARSET));
		$s['DESCRIPTION'] = trim($GLOBALS['APPLICATION']->ConvertCharset(isset($_POST['description_'.$site_key]) ? $_POST['description_'.$site_key] : '', 'utf-8', SITE_CHARSET));
		$s['LID'] = ($site['ID'] === 'all' ? false : $site['ID']);
		$s['ACTIVE'] = (isset($_POST['active_'.$site_key]) && $_POST['active_'.$site_key] == 'Y' ? 'Y' : 'N');
		$s['SORT'] = (isset($_POST['sort_'.$site_key]) ? intval($_POST['sort_'.$site_key]) : '');
		$s['TAX_RATE'] = 0;
		$s['PROFILE_USE_DEFAULT'] = 'N';

		if (isset($s['LOGOTIP'])) unset($s['LOGOTIP']);

		$c = array();
		foreach ($s['CONFIG']['CONFIG'] as $k => $v) {
			$id = $k.'_'.$site_key;
			$v = (isset($_POST[$id]) ? $_POST[$id] : $v['VALUE']);
			if ($k == 'host' && $v != '') {
				$v = trim(strtolower($v));
				if (substr($v, 0, 7) == 'http://') $v = substr($v, 7);
				if (substr($v, 0, 4) == 'www.') $v = substr($v, 4);
			}
			$c[$k] = str_replace(';', '', $v);
		}
		$s['CONFIG'] = $c;

		foreach ($s['PROFILES'] as $k => $v) {
			$id = $k.'_'.$site_key;
			if (isset($_POST['title_'.$id])) {
				$v['TITLE'] = trim($GLOBALS['APPLICATION']->ConvertCharset($_POST['title_'.$id], 'utf-8', SITE_CHARSET));
				$v['DESCRIPTION'] = (isset($_POST['description_'.$id]) ? trim($GLOBALS['APPLICATION']->ConvertCharset($_POST['description_'.$id], 'utf-8', SITE_CHARSET)) : '');
			}

			$v['ACTIVE'] = 'Y';
			$v['TAX_RATE'] = 0;
			$v['RESTRICTIONS_DIMENSIONS_SUM'] = 0;
			$v['RESTRICTIONS_MAX_SIZE'] = 0;
			$v['RESTRICTIONS_WEIGHT'] = array(0);
			$v['RESTRICTIONS_SUM'] = array(0);
			$v['RESTRICTIONS_DIMENSIONS'] = array(0);

			$s['PROFILES'][$k] = $v;
    	}

		$module_setting[$site_key] = $s;

		if (!$individual) break;
	}
//	echo '<br><b>ar:</b><pre style="font-size: 12px">'.print_r($module_setting, true).'</pre>';

	if ($individual) {
		if ($individual_server) foreach ($module_setting as $site_key => $s) if ($site_key !== 'all') {
			foreach ($s['CONFIG'] as $k => $v) if (!in_array($k, $module_server_key)) $s['CONFIG'][$k] = $module_setting['all']['CONFIG'][$k];
			foreach ($s['PROFILES'] as $k => $v) $s['PROFILES'][$k] = $module_setting['all']['PROFILES'][$k];
			$s['NAME'] = $module_setting['all']['NAME'];
			$s['DESCRIPTION'] = $module_setting['all']['DESCRIPTION'];
			$s['SORT'] = $module_setting['all']['SORT'];
			$module_setting[$site_key] = $s;
		}
        unset($module_setting['all']);
	}

    $c = array();
	foreach ($module_setting as $s) {
		if ($s['ACTIVE'] == 'Y') {
			$ar = array();
			foreach (CDeliveryEDOST::$setting_key as $k => $v) $ar[$k] = (isset($s['CONFIG'][$k]) ? $s['CONFIG'][$k] : $v);
			$c[$s['LID'] !== false ? $s['LID'] : 'all'] = implode(';', $ar);
		}

		$APPLICATION->ResetException();
		CSaleDeliveryHandler::Set('edost', $s, $s['LID']);
		if ($ex = $APPLICATION->GetException()) echo '<br>'.$ex->GetString();
	}
	COption::SetOptionString('edost.delivery', 'module_setting', serialize($c));

	if (defined('DELIVERY_EDOST_ORDER_LINK')) COption::SetOptionString('edost.delivery', 'order_link', DELIVERY_EDOST_ORDER_LINK);

	die();
}




// �������� �������
if ($print || ($ajax && !$update_allow_delivery)) {
	$filter = array('ID' => is_array($orders_id) ? $orders_id : 0);
	$allow_delivery = false;
}
else {
	$filter = array('DATE_INSERT_FROM' => GetTime(time() - 86400*$setting_cookie['filter_days']));
	$allow_delivery = true;
}

$ar = CSaleOrder::GetList(array('ID' => 'ASC'), $filter, false, array('nTopCount' => 500), array());

$orders = array();
while ($v = $ar->Fetch()) {
	$edost_id = -1;
	$insurance = false;
	$delivery = explode(':', $v['DELIVERY_ID']);
	if (isset($delivery[1]) && $delivery[0] == 'edost') {
		$edost_id = ceil($delivery[1] / 2);
		$insurance = ($edost_id*2 == $delivery[1] ? true : false);
	}
	$cod = ($v['PAY_SYSTEM_ID'] == $setting['cod'] ? true : false);
	if ($cod) $insurance = true;

	if ($allow_delivery) {
		if ($v['CANCELED'] == 'Y') continue;
		if (!($v['ALLOW_DELIVERY'] == 'Y' || $setting['show_allow_delivery'] != 'Y')) continue;
		if (!$cod) if (!($v['PAYED'] == 'Y' || $setting['hide_unpaid'] != 'Y')) continue;
		if ($deducted_enabled) if (!($v['DEDUCTED'] != 'Y' || $setting['hide_deducted'] != 'Y')) continue;
		if (!in_array($v['STATUS_ID'], $show_status)) continue;
		if (!in_array($edost_id, array(1, 2))) continue;
	}

	$v['DELIVERY_EDOST_ID'] = $edost_id;
	$v['INSURANCE'] = ($insurance ? 'Y' : 'N');
	$v['COD'] = ($cod ? 'Y' : 'N');

	$orders[] = $v;
}

foreach ($orders as $order_key => $order) {
	$edost_id = $order['DELIVERY_EDOST_ID'];
	$insurance = ($order['INSURANCE'] == 'Y' ? true : false);
	$cod = ($order['COD'] == 'Y' ? true : false);

	// ��������� �������� ������ �������� �� ����
	if (intval($order['DELIVERY_ID']) > 0) {
		// ������������� ������ ��������
		$ar = CSaleDelivery::GetByID($order['DELIVERY_ID']);
		$s = $ar['NAME'];
	}
	else {
		// ������������������ ������ ��������
		$id = explode(':', $order['DELIVERY_ID']);
		if (isset($id[1])) {
			$db = CSaleDeliveryHandler::GetBySID($id[0]);
			if ($ar = $db->GetNext()) {
				$company = (isset($ar['NAME']) ? $ar['NAME'] : '');
				$s = (isset($ar['PROFILES'][$id[1]]['TITLE']) ? $ar['PROFILES'][$id[1]]['TITLE'] : '');
				$s = $company.($company != '' ? ' (' : '').$s.($company != '' ? ')' : '');
			}
		}
	}
	foreach ($rename as $v) $s = str_replace($v[0], $v[1], $s);
	$order['DELIVERY_NAME'] = $s;

	// ��������� �������� ������� ������ �� ����
	$s = $pay_system[$order['PAY_SYSTEM_ID']]['name'];
	foreach ($rename as $v) $s = str_replace($v[0], $v[1], $s);
	$order['PAY_SYSTEM_NAME'] = $s;

	// ����������� ������������ ������� ������
	$order['STATUS_NAME_SHORT'] = (strlen($order_status[$order['STATUS_ID']]) > 20 ? substr($order_status[$order['STATUS_ID']], 0, 20).'...' : $order_status[$order['STATUS_ID']]);

	// �������� ���� ���������� ������ �� ���� (25.01.2014) � ����� (10:45:00)
	$ar = explode(' ', $order['DATE_INSERT']);
	if (count($ar) == 2) $order['DATE_INSERT'] = $ar[0].'<br><span class="low">'.$ar[1].'</span>';


	// ��������� ������ ����������
	$ar = array();
	foreach ($docs as $doc) if (!empty($doc['mode'])) {
		if (is_array($docs_active)) {
			// ������ ������ (������ ���������� ������� � ����������)
			if (!in_array($doc['id'], $docs_active)) continue;
		}
		else {
			// ����� �� ���������� ������ � ����������
			if (in_array($doc['id'], $docs_disable)) continue;
			if (is_array($doc['delivery']) && !($edost_id >= 0 && in_array($edost_id, $doc['delivery']))) continue;
			if ($doc['id'] == '107' && $setting['insurance_107'] == 'Y') {
				if (!$insurance) continue;
			}
            else if ($doc['cod'] && !$cod) continue;
		}

		$ar[] = $doc['id'];
	}

	if ($allow_delivery && $setting['hide_without_doc'] == 'Y' && count($ar) == 0) {
		unset($orders[$order_key]);
		continue;
	}

	$order['DOCS'] = $ar;


	// �������� ������
	$props = array();
	$location = false;
	$ar = CSaleOrderPropsValue::GetOrderProps($order['ID']);
	while ($v = $ar->Fetch()) {
		$props[$v['CODE']] = $v['VALUE'];
		if ($v['TYPE'] == 'LOCATION' && $v['IS_LOCATION'] == 'Y') {
			$location = $v['VALUE'];
			if (!$location_pro) $location = intval($location);
		}
	}

	if (!empty($location)) {
		if ($location_pro) $location = CSaleLocation::getLocationIDbyCODE($location);
		if (!empty($location)) $location = CSaleLocation::GetByID($location);
	}
	$city = (isset($location['CITY_NAME']) ? $location['CITY_NAME'] : '');
	$region = (isset($location['REGION_NAME']) ? $location['REGION_NAME'] : '');
	$country = (isset($location['COUNTRY_NAME']) ? $location['COUNTRY_NAME'] : '');
//	echo '<br><b>CSaleLocation::GetByID</b> <pre style="font-size: 12px">'.print_r($location, true).'</pre>';

	// ������� �������� bitrix � �������� edost
	$region = $GLOBALS['APPLICATION']->ConvertCharset($region, LANG_CHARSET, 'windows-1251');
	$region_edost = array('�������� �������', '������������� �������', '������������ �������', '������������ �������', '�������� �������', '������������ �������', '������������� �������', '����������� �������', '����������� �������', '��������� ��', '���������� �������', '��������� �������', '���������-���������� ����������', '��������������� �������', '��������� �������', '���������-���������� ����������', '����������� �������', '��������� �������', '����������� �������', '���������� �������', '������� �������', '������������� �������', '�������� �������', '����������� �������', '���������� �������', '���������� �������', '������������� �������', '������������ �������', '������������� �������', '������ �������', '������������ �������', '��������� �������', '���������� �������', '��������� �������', '���������� ������', '���������� �����', '���������� ������������', '���������� �������', '���������� ��������', '���������� ���������', '���������� ��������', '���������� �������', '���������� ����', '���������� ����� ��', '���������� ��������', '���������� ���� (������)', '���������� �������� ������ - ������', '���������� ���������', '���������� ����', '���������� �������', '���������� �������', '��������� �������', '��������� �������', '����������� �������', '����������� �������', '������������ �������', '���������� �������', '���������� �������', '�������� �������', '������� �������', '�������� �������', '��������� �������', '���������� ����������', '����������� �������', '�����-���������� ��', '����������� �������', '��������� ����������', '��������� ����������', '����������� �������', '���������� ����', '���������� ����', '�����-�������� ��', '��������� ��', '��������� ��', '���������� �������� ������ - ������', '�������� ��', '�����-���������� ��');
	$region_bitrix = array('�������� ���', '������������� ���', '������������ ���', '������������ ���', '�������� ���', '������������ ���', '������������� ���', '����������� ���', '����������� ���', '��������� ����', '���������� ���', '��������� ���', '���������-���������� ����', '��������������� ���', '��������� ���', '���������-���������� ����', '����������� ���', '��������� ���', '����������� ���', '���������� ���', '������� ���', '������������� ���', '�������� ���', '����������� ���', '���������� ���', '���������� ���', '������������� ���', '������������ ���', '������������� ���', '������ ���', '������������ ���', '��������� ���', '���������� ���', '��������� ���', '������ ����', '����� ����', '������������ ����', '������� ����', '�������� ����', '��������� ����', '�������� ����', '������� ����', '���� ����', '����� �� ����', '�������� ����', '���� /������/ ����', '�������� ������ - ������ ����', '��������� ����', '���� ����', '������� ����', '���������� ���', '��������� ���', '��������� ���', '����������� ���', '����������� ���', '������������ ���', '���������� ���', '���������� ���', '�������� ���', '������� ���', '�������� ���', '��������� ���', '���������� ����', '����������� ���', '�����-���������� ���������� ����� - ���� ��', '����������� ���', '��������� ����', '��������� ����', '����������� ���', '���� ����', '����', '�����-�������� ���������� �����', '��������� ���������� �����', '��������� ���������� �������', '���������� �������� ������-������', '�������� ���������� �����', '�����-���������� ���������� �����');
	$i = array_search($region, $region_bitrix);
	if ($i !== false) $region = $region_edost[$i];
	$region = $GLOBALS['APPLICATION']->ConvertCharset($region, 'windows-1251', LANG_CHARSET);

	// ���� ��� ���������� ����������
	$field = array();

	$ar = $user_field_key;
	foreach ($ar as $k => $v) if ($v != '') $ar[$k] = (isset($props[$v]) ? $props[$v] : '');

	$s = $ar['name_3'].($ar['name_1'] != '' && $ar['name_3'] != '' ? ' ' : '').$ar['name_1'].($ar['name_2'] != '' && ($ar['name_1'] != '' || $ar['name_3']) ? ' ' : '').$ar['name_2'];
	$field['user_name'] = $s.($s != '' && $ar['company'] != '' ? ', ' : '').$ar['company'];

	$s = (strlen($ar['zip']) == 6 ? $ar['zip'] : '');
	$field['user_zip'] = $s;
	for ($i = 1; $i <= 6; $i++) $field['user_zip_'.$i] = ($s == '' ? 'n' : $s{$i-1});

	if ($city === $region) $region = '';
	if ($city != '' || $ar['city'] == $region || $ar['city'] == $country) $ar['city'] = '';
	else if (strpos($ar['city'], $region) !== false) $region = '';
	if (in_array($city, $sign['no_region'])) $region = '';

	$s = $ar['city'];
	$s .= ($s != '' && $city != '' ? ', ' : '').$city;
	if ($region != '') $s .= ($s != '' ? ' (' : '').$region.($s != '' ?	 ')' : '');
	$field['user_address_short'] = $s;

	$s = array();
	if ($ar['address'] != '') $s[] = $ar['address'];
	if ($ar['city'] != '') $s[] = $ar['city'];
	if ($city != '') $s[] = $city;
	if ($region != '') $s[] = $region;
	$field['user_address'] = str_replace(array(',', '.'), array(', ', '. '), implode(', ', $s));


	// ��������� ������ ��� ����������� �������� � ����������� �������
	$price = CCurrencyRates::ConvertCurrency($order['PRICE'], $order['CURRENCY'], 'RUB');
	$delivery_price = CCurrencyRates::ConvertCurrency($order['PRICE_DELIVERY'], $order['CURRENCY'], 'RUB');
	if (!$cod && $insurance) $price -= $delivery_price; // ������� �� ������ ��������� ��������, ���� ��� �������
	$price = round($price, $decimals);
	$price_format = number_format($price, $decimals, ',', '');

	$order['TOTAL_FORMATED'] = SaleFormatCurrency($order['PRICE'], 'RUB');
	$order['PRICE'] = $price;
	$order['PRICE_FORMATED'] = $price_format;
	$order['PRICE_DELIVERY_FORMATED'] = SaleFormatCurrency($delivery_price, 'RUB');

	$value = $value2 = $rub = $kop = '';
	if ($insurance) {
		$rub = floor($price);
		$kop = round(($price - $rub)*100);
		if ($kop < 1) $kop = '00';
		$value = $rub.' ('.Number2Word_Rus($rub, 'N').') '.$sign['rub'].' '.$kop . $sign['kop'];
		$value2 = Number2Word_Rus($rub, 'N') . $sign['rub'] . ' ' . $kop . $sign['kop'];
		$value = str_replace(' )', ')', $value);
	}
	$field['insurance_full'] = $value;
	$field['insurance'] = $rub;
	$field['insurance_v'] = ($rub != '' ? 'V' : '');

	$field['inventory_v'] = (in_array('107', $order['DOCS']) ? 'V' : '');

	$field['normal_v'] = ($rub == '' ? 'V' : '');

	if (!$cod) $value = $value2 = $rub = $kop = '';

	$field['cod_full'] = $value;
	$field['cod_full_string'] = $value2;
	$field['cod'] = $rub;
	$field['cod2'] = $kop;
	$field['cod_v'] = ($rub != '' ? 'V' : '');

	$order['FIELD'] = $field;


	// ������
	$ar = CSaleBasket::GetList(array('NAME' => 'ASC', 'ID' => 'ASC'), array('ORDER_ID' => $order['ID']), false, false, array('ID', 'NAME', 'PRODUCT_ID', 'QUANTITY', 'DELAY', 'CAN_BUY', 'PRICE', 'WEIGHT'));

	$items = array();
	$items_list = array();
	$items_list_short = array();
	$i = 0;
	$hint = false;
	while ($v = $ar->Fetch()) if ($v['CAN_BUY'] == 'Y' && $v['DELAY'] == 'N' && isset($v['QUANTITY']) && $v['QUANTITY'] > 0) {
		$i++;
		$items[] = $v;

		$s = $v['NAME'];
		$n = ($v['QUANTITY'] > 1 ? 15 : 20);
		if (function_exists('mb_strlen') && function_exists('mb_substr')) {
			if (mb_strlen($v['NAME'], SITE_CHARSET) > $n) {
				$s = mb_substr($v['NAME'], 0, $n-2, SITE_CHARSET).'...';
				$hint = true;
			}
		}
		else if (strlen($v['NAME']) > $n) {
			$s = substr($v['NAME'], 0, $n-2).'...';
			$hint = true;
		}

		$items_list[] .= str_replace(array('"', "'"), array('&quot;', '&quot;'), $i.'. '.$v['NAME'].($v['QUANTITY'] > 1 ? ' (<b>'.intval($v['QUANTITY']).$sign['quantity'].'</b>)' : '').' - '.SaleFormatCurrency($v['PRICE'], 'RUB'));
		$items_list_short[] .= $i.'. '.$s.($v['QUANTITY'] > 1 ? ' (<b>'.intval($v['QUANTITY']).$sign['quantity'].'</b>)' : '').' - '.SaleFormatCurrency($v['PRICE'], 'RUB');
	}

	$n = count($items);
	if ($n > 0) {
		if ($hint || $n > 3) $order['HINT'] = implode('<br>', $items_list);
		$s = ($n > 3 ? '<br>... '.$sign['total2'].' '.draw_string('item2', $n) : '');
		if ($n > 3) array_splice($items_list_short, 2);
		$order['ITEMS_STRING'] = implode('<br>', $items_list_short).$s;

		// ������������� ��������� ������ �� ������� (����� ����� � ����� ��������� � ����������� ���������)
		$p = 0;
		$items_count = 0;
		for ($i = 0; $i < count($items); $i++) {
			$items_count += $items[$i]['QUANTITY'];
			$items[$i]['PRICE_MODIFIED'] = round($items[$i]['QUANTITY']*$items[$i]['PRICE'], $decimals);
			$p += $items[$i]['PRICE_MODIFIED'];
		}
		$order['ITEMS_COUNT'] = $items_count;
		$n = ceil(($price - $p) / $n);
		if ($n > 1) $n--;

		$p = 0;
		for ($i = 0; $i < count($items); $i++) {
			$items[$i]['PRICE_MODIFIED'] += $n;
			$p += $items[$i]['PRICE_MODIFIED'];
		}
		$items[0]['PRICE_MODIFIED'] += $price - $p;

		for ($i = 0; $i < count($items); $i++)
			$items[$i]['PRICE_MODIFIED_FORMATED'] = number_format($items[$i]['PRICE_MODIFIED'], $decimals, ',', '');
	}
	$order['ITEMS'] = $items;


	$orders[$order_key] = $order;
}
//echo '<br><b>orders:</b><pre style="font-size: 12px">'.print_r($orders, true).'</pre>';




// �������� � ����������� �� ������
if ($print) {
	if (count($orders) == 0) die();

	$mode = explode('|', $mode);
	if (!isset($mode[1])) $mode[1] = 'normal';

	$pages = array();
    $order_count = 0;
	foreach ($orders as $order) {
		$edost_id = $order['DELIVERY_EDOST_ID'];
		$insurance = ($order['INSURANCE'] == 'Y' ? true : false);
		$cod = ($order['COD'] == 'Y' ? true : false);

		$field = array_merge($shop_field, $order['FIELD']);

		// ����� ������ � ���� ���������
		if ($setting['show_order_id'] == 'Y') $s = $sign['order'].(strlen($order['ID']) == 6 ? '0' : '').$order['ID']; else $s = '';
		$field['id'] = $s;
		$field['info_color'] = ' color: #'.$setting['info_color'].';';

		// �������� �������� ��� �����
		$s = '';
		if (in_array($edost_id, array(1, 2))) {
			$s = ($edost_id == 1 ? $info_107[1] : $info_107[0]);
			if ($insurance) $s .= ' '.($cod ? $info_107[3] : $info_107[2]);
		}
		$field['107_info'] = $s;

		// ������� "��������� ��������� ��������", ���� � ���������� �� ����� ����� ���������� �����
		$field['cash'] = ($shop_field['company_rsch'] == '' ? 'V' : '');


		// ����� � ������� ��� ������
		$field_key = array_keys($field);
		for ($i = 0; $i < count($field_key); $i++) $field_key[$i] = '%'.$field_key[$i].'%';
//		echo '<br><b>props:</b><pre style="font-size: 12px">'.print_r($field, true).'</pre>';


		// ���������� �������
		$add_order = false;
		foreach ($order['DOCS'] as $doc_key) for ($q = 1; $q <= $docs[$doc_key]['quantity']; $q++) {
			$doc = $docs[$doc_key];
			if (!($doc['mode'] == $mode[1] || ($doc['mode'] == 'duplex' && ($mode[1] == 'front' || $mode[1] == 'back')))) continue;
			$add_order = true;

			// ���������� �����
			$page = ($doc['mode'] == 'duplex' && $mode[1] == 'back' ? $doc['data2'] : $doc['data']);
			draw_field($field_key, $field, $doc, $page);

			// �������� ��� ������������� ����������
			$x = $doc['size'][2] + $setting['duplex_x'];
			$page = str_replace('%left%', $x, $page);

			// �������� �� ������� � ������ ������ �������
			if (!isset($docs[$doc['id'].'_item'])) {
				$pages[] = array('size' => $doc['size'], 'data' => $page);
				continue;
			}

			// ���������� ������ �������
			$p = $page;
			$item_i = 0;
			$item_s = '';
            $item_doc = $docs[$doc['id'].'_item'];
			$top = 0;
			$list = 1;

			$n = count($order['ITEMS']) - 1;
			for ($i = 0; $i <= $n; $i++) {
				$item_i++;

				$s = $item_doc['data'];

				$f = array(
					'item_top' => $top,
					'item_i' => $i + 1,
					'item_name' => $order['ITEMS'][$i]['NAME'],
					'item_quantity' => intval($order['ITEMS'][$i]['QUANTITY']) . $sign['quantity'],
					'item_price' => $order['ITEMS'][$i]['PRICE_MODIFIED_FORMATED'] . $sign['rub'],
				);

				$f_key = array_keys($f);
				for ($i2 = 0; $i2 < count($f_key); $i2++) $f_key[$i2] = '%'.$f_key[$i2].'%';

				draw_field($f_key, $f, $item_doc, $s, true);
				$item_s .= $s;

				$top += $item_doc['size'][1];

				if ($i == $n || $item_i >= $item_doc['size'][0]) {
					$p = str_replace('%items_table%', $item_s, $p);
					$p = str_replace('%list%', ($i != $n || $list != 1 ? $sign['list'] . $list : ''), $p);
					$p = str_replace('%items_total%', ($i == $n ? draw_string('item', $order['ITEMS_COUNT']).', '.$order['PRICE_FORMATED'] . $sign['rub'] : ''), $p);

					if ($i != $n) {
						$pages[] = array('size' => $doc['size'], 'data' => $p);

						$p = $page;
						$item_i = 0;
						$item_s = '';
						$top = 0;
						$list++;
					}
				}
			}

			$pages[] = array('size' => $doc['size'], 'data' => $p);
		}

		if ($add_order) $order_count++;
	}
//	echo '<br><b>props:</b><pre style="font-size: 12px">'.print_r($pages, true).'</pre>';

	// 'back' �������� � �������� �������
	if ($mode[1] == 'back' && $setting['duplex'] == 'Y') $pages = array_reverse($pages);

	// ������������� ������� �� ���������
	$s = '';
	$y = 0;
	$n = count($pages) - 1;
	$page_count = ($n >= 0 ? 1 : 0);
	for ($i = 0; $i <= $n; $i++) {
		$y += $pages[$i]['size'][1];
                                                                    //296
		if ($i == $n || ($i != $n && $y + $pages[$i+1]['size'][1] < 290)) $s2 = '';
		else {
			$page_count++;
			$s2 = ' page-break-after: always;';
			$y = 0;
		}

		$s .= str_replace('%page-break%', $s2, $pages[$i]['data']);
	}

	// ���������� html ��������
	$body = $docs['body']['data'];
	$body = str_replace('%charset%', SITE_CHARSET, $body);
	$body = str_replace('%mode%', isset($docs[$mode[1]]['data']) ? $docs[$mode[1]]['data'] : '', $body);
	$body = str_replace('%browser%', isset($docs[$setting['browser']]['data']) ? $docs[$setting['browser']]['data'] : '', $body);
	$body = str_replace('%order%', $order_count, $body);
	$body = str_replace('%page%', $page_count, $body);
	$body = str_replace('%data%', $s, $body);

    echo $body;

	die();
}




// ���������� �������� ������
if ($ajax && $mode == 'setting_document_save') {
	foreach ($setting as $k => $v) if (isset($_POST[$k])) $setting[$k] = $_POST[$k];
	$setting['duplex_x'] = str_replace(',', '.', $setting['duplex_x']) + 0;
	COption::SetOptionString('edost.delivery', 'document_setting', implode('|', $setting));

	$passport = array();
	for ($i = 0; $i < 6; $i++) $passport[] = (isset($_POST['passport_'.$i]) ? $GLOBALS['APPLICATION']->ConvertCharset($_POST['passport_'.$i], 'utf-8', SITE_CHARSET) : '');
	COption::SetOptionString('edost.delivery', 'document_passport', implode('|', $passport));

	die();
}




// �������� �������
$history = array();
$history_cache = new CPHPCache();
$history_cache_id = 'sale|11.0.0|edost_delivery|history';
$history_cache_time = 86400*15;
if ($history_cache->InitCache($history_cache_time, $history_cache_id, '/')) $history = $history_cache->GetVars();


// �������� ������ ������� ��������� ��� ��������� ������� + ��������� ������� ����� ������ + ���������
if ($ajax && $mode == 'print') {
	if (count($orders) == 0) die();

	$error = array();
	$print_mode = array();
	$orders_id = array();
	foreach ($orders as $order) if (count($order['DOCS']) > 0) {
		$orders_id[] = $order['ID'];

		foreach ($order['DOCS'] as $v) {
			if ($docs[$v]['mode'] == 'duplex') {
				$print_mode['front'] = 'front';
				$print_mode['back'] = 'back';
			}
			else $print_mode[$docs[$v]['mode']] = $docs[$v]['mode'];
		}

		if ($update_status) {
			// ��������� ������� ������
			if ($setting['complete_status'] != 'none' && $setting['complete_status'] != $order['STATUS_ID']) {
				if (!CSaleOrder::StatusOrder($order['ID'], $setting['complete_status'])) $error[] = $order['ID'];
			}

			// �������� ������
			if ($deducted_enabled && $setting['deducted'] == 'Y' && $order['DEDUCTED'] != 'Y') {
				if (CSaleOrder::CanUserChangeOrderFlag($order['ID'], 'PERM_DEDUCTION', $user_groups)) if (!CSaleOrder::DeductOrder($order['ID'], 'Y')) $error[] = $order['ID'];
			}
		}
	}


	// ���������� ������������� ������� � �������
	$n = count($orders_id);
	if ($n > 0) {
		$first = -1;
		foreach ($history as $k => $v) {
			if ($first == -1) $first = $k;
			if ($v['id'] === $orders_id) {
				unset($history[$k]);
				break;
			}
		}

		$s = '';
		$i = 0;
		foreach ($orders_id as $v) {
			$i++;
			if ($i > 3 && $n > 5) {
	            $s .= ', ... ('.$sign['total'].' '.draw_string('order', $n).')';
				break;
			}
			$s .= ($s != '' ? ', ' : '').$v;
		}

		if (count($history) >= 20 && $first >= 0) unset($history[$first]);

		$history[] = array(
			'date' => ConvertTimeStamp(time(), 'FULL'),
			'name' => $sign['order'] . $s,
			'mode' => 'print',
			'id' => $orders_id,
			'doc' => $docs_active,
		);

		if (!$history_cache->InitCache(1, $history_cache_id, '/')) {
			$history_cache->StartDataCache();
			$history_cache->EndDataCache($history);
		}
	}


	// ����� � ������������ � json
	$ar = array(
		'"error": "'.implode('|', $error).'"',
		'"id": "'.implode('|', $orders_id).'"',
		'"mode": "'.implode('|', $print_mode).'"',
	);
	if (is_array($docs_active)) $ar[] = '"doc": "'.implode('|', $docs_active).'"';

	echo '{'.implode(', ', $ar).'}';

	die();
}




// ---------------------------------------------------------------------------




if (!$ajax) {
	$APPLICATION->SetTitle(GetMessage('EDOST_ADMIN_TITLE'));
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
}


if (!$ajax && !$show) { ?>
<div class="adm-info-message" style="display: block"><?=($warning['warning'].($module_first_start ? $warning['start'] : $warning['error']))?></div>
<? }

if ($warning_key != '') { ?>
<div class="adm-info-message" style="display: block"><?=($warning['warning'].$warning[$warning_key])?></div>
<? } ?>

<? if (!$ajax) { ?>
<style>
	div.checkbox label, div.checkbox input { vertical-align: middle; }

	span.low { color: #888; }
	table.standard a { text-decoration: none; }
	table.standard { border-collapse: collapse; border-color: #888; border-style: solid; border-width: 1px; }
	table.standard td { vertical-align: top; }
	tr.slim td { border-width: 1px 0px; }
	span.button { color: #00F; }

	div.menu { float: right; width: 200px; background: #F5F5F5; padding: 3px; text-align: center; font-size: 18px; font-weight: bold; border: 1px solid #A4B9CC; cursor: pointer; color: #5F6674; }
	div.menu:hover { background: #FFF; }

	div.head { margin-top: 20px; color: #08C; font-size: 16px; font-weight: bold; }
	div.delimiter { width: 900px; margin: 5px 0 5px 0; border-width: 1px 0 0 0; border-color: #CCC; border-style: solid; }

	span.error { padding-top: 5px; color: #F00; font-weight: bold; font-size: 14px; }
	span.note { color: #888; vertical-align: middle; }

	div.checkbox input[type="checkbox"]:checked + label { color: #000; }
	div.checkbox input[type="checkbox"] + label { color: #888; }
	div.checkbox input[type="checkbox"]:checked + label.blue { color: #00F; }
	div.checkbox input[type="checkbox"] + label.blue { color: #88F; }
	div.checkbox input[type="checkbox"]:checked + label.green { color: #080; }
	div.checkbox input[type="checkbox"] + label.green { color: #8B8; }
	div.checkbox input[type="checkbox"]:checked + label.orange { color: #E60; }
	div.checkbox input[type="checkbox"] + label.orange { color: #B98; }

	div.radio input[type="radio"]:checked + span.red { color: #A00; }
	div.radio input[type="radio"] + span.red { color: #888; }
	div.radio input[type="radio"]:checked + span.green { color: #080; }
	div.radio input[type="radio"] + span.green { color: #888; }
	div.radio input[type="radio"]:checked + span.normal { color: #000; }
	div.radio input[type="radio"] + span.normal { color: #888; }

	tr.active { background: #F0FBF0; }
	tr.normal { background: none; }

	div.link { cursor: pointer; font-size: 13px; font-weight: bold; }
	span.link { cursor: pointer; font-size: 13px; font-weight: bold; }

	div.on { color: #F00; background: #FEE; border: 1px solid #F00; }
	div.off { color: #AAA; }

	div.checkbox input.normal { background: #FFF; border: 1px solid #A4B9CC; border-radius: 0; box-shadow: none; vertical-align: baseline; padding: 0px 4px; height: 21px; }
</style>

<script type="text/javascript">

	function edost_UpdateCookie(name, value) {

		var key = [<?=("'".implode("', '", array_keys($setting_cookie))."'")?>];

		var ar = document.cookie.match('(^|;) ?edost_admin=([^;]*)(;|$)');
		ar = (ar ? decodeURIComponent(ar[2]) : '');
		ar = ar.split('|');

		var s = '';
		for (var i = 0; i < key.length; i++) {
			if (i > 0) s += '|';
			if (name == key[i]) s += value;
			else if (ar[i] != undefined) s += ar[i];
		}

		document.cookie = 'edost_admin=' + s + '; path=/; expires=Thu, 01-Jan-2016 00:00:01 GMT';

	}

	function edost_UpdateActive(id, active, checked) {

		if (active == undefined) active = false;
		if (checked == undefined) checked = '';

		if (id == 'all') {
			var ar = BX.findChildren(BX('order_table'), {'tag': 'input', 'type': 'checkbox'}, true);
			for (var i = 0; i < ar.length; i++) edost_UpdateActive(ar[i].id, false, checked);
		}
		else {
			var E = BX(id);
			if (active) E.checked = (E.checked ? false : true);
			if (checked != '') E.checked = (checked == 'Y' ? true : false);
			BX(id + '_tr').className = (E.checked ? 'slim active' : 'slim normal');
		}

	}

	function edost_GetChecked(name, separator) {

		if (separator == undefined) separator = '|';

		var s = '';
		var ar = BX.findChildren(BX(name), {'tag': 'input', 'type': 'checkbox'}, true);
		for (var i = 0; i < ar.length; i++) if (ar[i].checked) {
			var id = ar[i].id;
			id = id.split('_');
			if (id[1] == undefined || id[1] == '') continue;
			id = id[1];

			s += (s != '' ? separator : '') + id;
		}

		return s;

	}

	function edost_SetData(mode, id) {

		var id_string = '';
		var param = '';
		var update_order = false;
		var update_docs = false;
		var update_history = false;
		var update_setting = false;
		var v = '';

		if (mode == 'menu') {
			var main = (BX('main_div').style.display == 'none' ? false : true);
			if (id == undefined) {
				BX('main_div').style.display = BX('menu_order_span').style.display = (main ? 'none' : 'block');
				BX('setting_div').style.display = BX('menu_setting_span').style.display = (!main ? 'none' : 'block');
				main = !main;
			}
			else {
				var ar = BX.findChildren(BX('setting_div'), {'tag': 'div', 'class': 'setting'}, true);
				for (var i = 0; i < ar.length; i++) ar[i].style.display = (ar[i].id == id + '_div' ? 'block' : 'none');
				edost_UpdateCookie('setting_active', id.substr(8));
			}
			if (!main) {
				var ar = ['setting_module', 'setting_paysystem', 'setting_document'];
				for (var i = 0; i < ar.length; i++) if (BX('menu_' + ar[i]) != undefined) {
					var a = (BX(ar[i] + '_div').style.display == 'block' ? true : false);
					BX('menu_' + ar[i]).className = 'menu ' + (a ? 'on' : 'off')
					if (a && BX(ar[i] + '_none') != undefined) edost_SetData(ar[i]);
				}
			}
			return;
		}

		if (mode == 'module_individual') {
			if (id != undefined && id != '') {
				var s = id.split('|');
				if (s[1] == 'tariff') {
					var a = (s[2] == 'show' ? true : false);
					edost_UpdateCookie('setting_tariff_show', (a ? 'Y' : 'N'));
					BX('setting_module_tariff_show_' + s[0]).style.display = (!a ? 'inline' : 'none');
					BX('setting_module_tariff_hide_' + s[0]).style.display = (a ? 'inline' : 'none');
					BX('setting_module_tariff_' + s[0]).style.display = (a ? 'block' : 'none');
				}
			}

			var active = false;
			var E = BX('setting_module_individual'); var a = (E && E.checked ? true : false);
			var E = BX('setting_module_individual_server'); var a_server = (E && E.checked ? true : false);

			var ar = BX.findChildren(BX('setting_module_div'), {'tag': 'div', 'class': 'setting_module'}, true);
			for (var i = 0; i < ar.length; i++) {
				var s = ar[i].id.substr(15);

				var display = ((!a && s == 'all') || (a && (a_server || s != 'all')) ? true : false);
				ar[i].style.display = (display ? 'block' : 'none');

				if (display) {
                    var a1 = (!a || (a && s !== 'all') ? true : false);
                    var a2 = (!a || (a && (!a_server || s == 'all')) ? true : false);

					BX(ar[i].id + '_active').style.display = (a1 ? 'block' : 'none');

					if (BX('setting_module_active_' + s).checked && (!a && s == 'all' || a && s != 'all')) active = true;
					else {
						a1 = false;
						if (!a && s == 'all' || a && s != 'all') a2 = false;
					}

					BX(ar[i].id + '_server').style.display = (a1 ? 'block' : 'none');
					BX(ar[i].id + '_shop').style.display = (a2 ? 'block' : 'none');
				}

				// ������ ���������, ����������� � ������� eDost
				var a1 = (BX('setting_module_template_' + s).checked ? true : false);
				var a2 = (BX('setting_module_template_format_' + s).value != 'off' ? true : false);
				var a3 = (BX('setting_module_template_block_' + s).value != 'off' ? true : false);
				var a4 = (BX('setting_module_map_' + s).checked ? true : false);
				var ar2 = ['format', 'block', 'block_type', 'cod', 'autoselect_office'];
				for (var i2 = 0; i2 < ar2.length; i2++) {
					var display = (a1 && (ar2[i2] != 'block' && ar2[i2] != 'block_type' || a2) && (ar2[i2] != 'block_type' || a3) && (ar2[i2] != 'autoselect_office' || a4) ? 'block' : 'none');
					BX('setting_module_template_' + ar2[i2] + '_' + s + '_div').style.display = display;
				}
			}

			var E = BX('setting_module_individual_server_div');
			if (E) E.style.display = (a ? 'block' : 'none');

			BX('setting_module_button_title').style.display = (active ? 'block' : 'none');

			return;
		}

		if (mode == 'paysystem') {
			var v = id.split('|');
			id = v[0];
			n = (v[1] != undefined ? v[1] : '');

			var a = (BX('setting_paysystem_list_' + id).checked ? true : false);
			BX('setting_paysystem_tariff_' + id).style.display = (a ? 'block' : 'none');

			if (n == 'all' || n == 'none') {
				var ar = BX.findChildren(BX('setting_paysystem_tariff_' + id), {'tag': 'input'}, true);
				for (var i = 0; i < ar.length; i++) if (ar[i].type == 'checkbox') ar[i].checked = (n == 'all' ? true : false);
			}

			return;
		}

		if (mode == 'check_Y' || mode == 'check_N') {
			edost_UpdateActive('all', false, mode == 'check_Y' ? 'Y' : 'N');
			return;
		}


		if (mode == 'setting_module_title') {
			mode = 'setting_module';
			param += '&title=Y';
		}

		if (mode == 'setting_module_save' || mode == 'setting_document_save' || mode == 'setting_paysystem_save') {
			BX(mode).className = 'adm-btn adm-btn-load';
			BX(mode + '_loading').style.display = 'block';
		}
		if (mode == 'setting_module_save') {
			var E = BX('setting_module_individual'); var a = (E && E.checked ? true : false);
			var E = BX('setting_module_individual_server'); var a_server = (E && E.checked ? true : false);

			var ar = BX.findChildren(BX('setting_module_div'), function(E) { return ((E.tagName == 'INPUT' || E.tagName == 'SELECT') && E.type != 'button' && E.type != 'hidden' ? true : false); }, true);
			for (var i = 0; i < ar.length; i++) {
				if (ar[i].type == 'checkbox') v = (ar[i].checked ? 'Y' : 'N'); else v = ar[i].value;
				var key = ar[i].id.substr(15);
				var all = (key.substr(-3) == 'all' ? true : false);

				if (!a && !all || a && !a_server && all || a && a_server && !all && (key.substr(0, 11) == 'description' || key.substr(0, 5) == 'title')) continue;

				if (key.substr(0, 7) == 'active_' && v == 'Y') {
					var ar2 = BX.findChildren(BX('setting_module_' + key.substr(7) + '_server'), function(E) { return (E.tagName == 'INPUT' && E.type == 'text' ? true : false); }, true);
					for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].value != BX(ar2[i2].id + '_start').value) update_setting = true;
				}

				if (v != '' || key.substr(0, 11) != 'description') param += '&' + key + '=' + encodeURIComponent(v);
			}
		}
		if (mode == 'setting_paysystem_save') {
			var ar = BX.findChildren(BX('setting_paysystem_div'), {'tag': 'div', 'class': 'setting_paysystem'}, true);
			for (var i = 0; i < ar.length; i++) {
				var s = ar[i].id.substr(18);

				var s2 = '';
				if (BX('setting_paysystem_all_' + s).checked) s2 = 'all';
				else if (BX('setting_paysystem_none_' + s).checked) s2 = 'none';
				else s2 = edost_GetChecked('setting_paysystem_tariff_' + s, ',');

				param += '&paysystem_' + s + '=' + s2;
			}
		}
		if (mode == 'setting_document_save') {
			// ['id', (1 - �������� ������ �������, 2 - ��������� � ������� ���������)]
			var ar = [['show_order_id', 0], ['insurance_107', 1], ['duplex', 0], ['show_allow_delivery', 1], ['hide_deducted', 1], ['deducted', 0], ['hide_unpaid', 1], ['hide_without_doc', 1], ['complete_status', 1], ['cod', 0], ['info_color', 0], ['browser', 2], ['duplex_x', 0]];
			for (var i = 0; i < ar.length; i++) {
				var E = BX('setting_' + ar[i][0]);
				if (!E) continue;

				if (E.type == 'checkbox') v = (E.checked ? 'Y' : 'N'); else v = E.value;

				param += '&' + ar[i][0] + '=' + v;
				if (ar[i][1] > 0) {
					E2 = BX('setting_' + ar[i][0] + '_start');
					if (!E2) continue;

					if (E2.value != v) {
						E2.value = v;
						if (ar[i][1] == 1) update_order = true;
						if (ar[i][1] == 2) param += '&update_docs=Y';
					}
				}
			}

			param += '&docs_disable=' + edost_GetChecked('setting_docs_disable_div', ',');

			var v = edost_GetChecked('setting_show_status_div', ',');
			param += '&show_status=' + v;
			if (v != BX('setting_show_status_start').value) {
				BX('setting_show_status_start').value = v;
				update_order = true;
			}

			for (var i = 0; i < 6; i++) param += '&passport_' + i + '=' + BX('passport_' + i).value;
		}


		if (mode == 'update_allow_delivery' || mode == 'update_filter_days') {
			if (mode == 'update_filter_days') edost_UpdateCookie('filter_days', BX('filter_days').value);
			mode = 'order';
			param += '&update_allow_delivery=Y';
		}

		if (mode == 'find') {
			mode = 'order';
			var ar = BX('order_find').value;
			ar = ar.replace(/[^0-9,.-]/g, '').replace(/[.]/g, ',');
			ar = ar.split(',');

			var n = 0;
			id_string = '';
			for (var i = 0; i < ar.length; i++) {
				var v = ar[i].split('-');
				if (v[1] == undefined) {
					n++;
					if (n > 200) break;
					id_string += (id_string != '' ? '|' : '') + v[0];
				}
				else if (v[0] != '' || v[1] != '') {
	                if (v[0] == '') v[0] = v[1]*1 - 100*1;
	                if (v[1] == '') v[1] = v[0]*1 + 100*1;
	                if (v[0] < 1) v[0] = 1;
	                if (v[0] > v[1]) {
	                	var x = v[0];
	                	v[0] = v[1];
	                	v[1] = x;
	                }
					for (var i2 = v[0]; i2 <= v[1]; i2++) {
						n++;
						if (n > 200) break;
						id_string += (id_string != '' ? '|' : '') + i2;
					}
				}
			}
		}

		if (mode == 'history_order' || mode == 'history_print') {
			if (mode != 'history_print') mode = 'order';

			var ar = BX('history').value;
			ar = ar.split('-');
			if (ar[1] != undefined) id_string = ar[1];
			if (ar[2] != undefined) param += '&doc=' + ar[2];
		}

		if (mode == 'print' || mode == 'print_manual' || mode == 'history_print') {
			if (mode == 'print') param += '&update_status=Y';
			if (mode == 'print_manual') {
				edost_UpdateCookie('docs_active', edost_GetChecked('doc_div', '-'));
				param += '&doc=' + edost_GetChecked('doc_div');
			}
			if (mode != 'history_print') id_string = edost_GetChecked('order_table');

			if (id_string == '') {
				alert('<?=$sign['no_order_active']?>');
				return;
			}

			if (id != 'undefined') BX(id).className = 'adm-btn adm-btn-load';
			BX('history_div').innerHTML = '<div style="height: 45px; vertical-align: middle;"><div style="height: 10px;"></div><span class="low" style="font-size: 15px; font-weight: bold;"><?=$sign['loading_history']?></span></div>';

			mode = 'print';
		}


		if (mode == 'order' || mode == 'setting_module' || mode == 'setting_paysystem') BX(mode + '_div').innerHTML = '<span class="low" style="font-size: 20px; font-weight: bold;"><?=$sign['loading']?></span>';
		if (mode == 'setting_module_save') BX(mode + '_error').innerHTML = '';
		BX.ajax.post('edost.php', 'ajax=Y&mode=' + mode + '&id=' + id_string + param, function(res) {
			if (id != 'undefined') {
				var E = BX(id);
				if (E && E.type == 'button') BX(id).className = (id == 'button_print' ? 'adm-btn-save' : 'adm-btn');
			}

			if (mode == 'order' || mode == 'history' || mode == 'setting_module' || mode == 'setting_paysystem') {
				BX(mode + '_div').innerHTML = res;
				if (mode == 'setting_module') edost_SetData('module_individual');
			}

			if (mode == 'setting_module_save' || mode == 'setting_paysystem_save' || mode == 'setting_document_save') {
				BX(mode).className = 'adm-btn-save';
				BX(mode + '_loading').style.display = 'none';

				if (mode == 'setting_module_save')
					if (res != '') BX(mode + '_error').innerHTML = res;
					else if (update_setting) window.open('edost.php', '_self');
				if (mode == 'setting_document_save' && update_order) edost_SetData('update_allow_delivery');
			}

			if (mode == 'print') {
				if (res == '') return;
				res = (window.JSON && window.JSON.parse ? JSON.parse(res) : eval('(' + res + ')'));

				if (res.id != undefined && res.mode != undefined)
					if (res.id == '') alert('<?=$sign['no_doc']?>');
					else {
						var ar = res.mode.split('|');
						for (var i = 0; i < ar.length; i++)
							window.open('edost.php?mode=print|' + ar[i] + '&id=' + res.id + (res.doc != undefined ? '&doc=' + res.doc : ''), '_blank');
					}

				window.setTimeout("edost_SetData('history');", 3000);
			}
		});

	}
</script>
<? } ?>


<? if ($show) { ?>

<? if (!$ajax) { ?>
<div style="max-width: 950px;">

<?	if ($shop_warning) { ?>
	<div class="adm-info-message" style="display: block"><?=($warning['warning'].$warning['shop'])?></div>
<?	} ?>

	<div id="menu" class="menu" onclick="edost_SetData('menu');">
		<span id="menu_order_span" style="display: <?=(!$setting_module_show ? 'block' : 'none')?>;"><?=$button['menu_setting']?></span>
		<span id="menu_setting_span" style="display: <?=($setting_module_show ? 'block' : 'none')?>;"><?=$button['menu_order']?></span>
	</div>
<? }

if (!$ajax) { ?>
	<div id="main_div" style="display: <?=(!$setting_module_show ? 'block' : 'none')?>;">
		<div style="height: 30px; padding-top: 5px; padding-bottom: 10px;">
			<b><?=$sign['find_head']?></b> <input id="order_find" value="" type="text" style="width: 300px;">
			<input value="<?=$sign['find']?>" type="button" onclick="edost_SetData('find')">
			<img id="find_hint" style="position: absolute; margin: 6px 0 0 6px;" src="http://edostimg.ru/img/hint/hint.gif">
			<script type="text/javascript"> new top.BX.CHint({parent: top.BX('find_hint'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 350, hint: '<?=$sign['find_hint']?>'}); </script>
		</div>
<? }

if (!$ajax) { ?>
		<div id="history_div" style="font-size: 13px;">
<? }
if (count($history) > 0 && (!$ajax || ($ajax && $mode == 'history'))) { ?>
			<div style="height: 30px; padding-top: 5px; padding-bottom: 10px;">
				<b><?=$sign['history_head']?></b>
				<select id="history">
<?				$ar = array_reverse($history);
				foreach ($ar as $k => $v) { ?>
					<option value="<?=$v['mode']?>-<?=implode('|', $v['id'])?>-<?=(is_array($v['doc']) ? implode('|', $v['doc']) : '')?>"><?=$v['date']?> - <?=$v['name']?></option>
<?				} ?>
				</select>
				<input value="<?=$button['history']['name']?>" type="button" onclick="edost_SetData('history_order')">
				<input id="button_history_print" class="adm-btn" value="<?=$button['history']['print']?>" type="button" onclick="edost_SetData('history_print', this.id)">
			</div>
<?	}
if ($ajax && $mode == 'history') die();
if (!$ajax) { ?>
		</div>
<? } ?>


<? if (!$ajax) { ?>
	<div id="order_div">
<? } ?>

<? if (!$ajax || ($ajax && $mode == 'order')) { ?>

		<div style="margin: 15px 0px 2px 0px;">
<?		if ($allow_delivery) { ?>
			<b style="color: #08C; font-size: 15px;"><?=$sign['allow_delivery_head']?>
			<select id="filter_days" onchange="edost_SetData('update_filter_days')" style="padding: 1px; height: 22px;">
<?			foreach ($setting_data['filter_days'] as $k => $v) { ?>
				<option value="<?=$k?>" <?=($k == $setting_cookie['filter_days'] ? 'selected' : '')?>><?=$v?></option>
<?			} ?>
			</select>
			:</b>
	        <div style="float: right;">
				<input style="height: 18px;" value="<?=$button['update']?>" type="button" onclick="edost_SetData('update_allow_delivery')">
			</div>
<?		} else { ?>
			<input value="<?=$button['show_order'].$setting_data['filter_days'][$setting_cookie['filter_days']]?>" type="button" onclick="edost_SetData('update_allow_delivery')">
			<div style="margin: 20px"></div>
<?		} ?>
		</div>

		<table id="order_table" class="standard" width="100%" style="max-width: 950px;" border="1" bordercolor="#888" cellpadding="4" cellspacing="0">
<?		foreach ($orders as $v) {?>
			<tr id="order_<?=$v['ID']?>_tr" class="slim checkbox">
				<td width="20" onclick="edost_UpdateActive('order_<?=$v['ID']?>', true)">
					<input class="adm-checkbox adm-designed-checkbox" id="order_<?=$v['ID']?>" type="checkbox" checked="" onclick="edost_UpdateActive(this.id)">
					<label class="adm-designed-checkbox-label adm-checkbox" for="order_<?=$v['ID']?>"></label>
				</td>
				<td width="60" align="right" style="font-size: 13px; cursor: default;" onclick="edost_UpdateActive('order_<?=$v['ID']?>', true)">
					<?=$v['DATE_INSERT']?>
				</td>
				<td width="60" style="font-size: 15px; text-align: center;" align="center">
					<a href="/bitrix/admin/sale_order_detail.php?ID=<?=$v['ID']?>&lang=<?=LANGUAGE_ID?>"><b><?=('<span style="font-size: 10px;">'.$sign['order'] .'</span>'. $v['ID'])?></b></a>
				</td>

				<td width="320" align="left" style="font-size: 13px;">
					<?=$v['FIELD']['user_name']?><br>
					<span class="low"><?=$v['FIELD']['user_address_short']?></span>
					<div><span class="low" style="color: #55F;"><?=$v['DELIVERY_NAME']?></span></div>
					<div style="text-align: right2;"> <span class="low" style="color: #b59422;"><?=$v['PAY_SYSTEM_NAME']?></span></div>
					<?=($allow_delivery && count($v['DOCS']) == 0 ? $setting_data['hide_without_doc']['mark'] : '')?>
				</td>

				<td width="160" style="font-size: 13px; text-align: left;">
					<span id="order_status_<?=$v['ID']?>" style="cursor: default;"><?=$v['STATUS_NAME_SHORT']?></span><br>
<?					foreach ($flag as $k => $f) if (isset($v[$k]) && $v[$k] == $f['value']) echo $f['name'].'<br>'; ?>
					<script type="text/javascript">
						new top.BX.CHint({parent: top.BX('order_status_<?=$v['ID']?>'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 250, hint: '<?=('['.$v['STATUS_ID'].'] '.$order_status[$v['STATUS_ID']].'<br>'.$v['DATE_STATUS'])?>'});
					</script>
				</td>

				<td style="font-size: 10px; text-align: left; cursor: default;">
					<span id="order_id_<?=$v['ID']?>">
						<?=$v['ITEMS_STRING']?><br>
						<?=($v['PRICE_DELIVERY'] > 0 ? $sign['delivery'].': '.$v['PRICE_DELIVERY_FORMATED'].'<br>' : '')?>
						<span style="font-size: 13px;"><b><?=$v['TOTAL_FORMATED']?></b></span>
					</span>

<?					if (isset($v['HINT'])) { ?>
					<script type="text/javascript">
						new top.BX.CHint({parent: top.BX('order_id_<?=$v['ID']?>'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 350, hint: '<?=$v['HINT']?>'});
					</script>
<?					} ?>
				</td>

				<td width="80" align="center" style="font-size: 16px;">
					<a href="/bitrix/admin/sale_order_new.php?ID=<?=$v['ID']?>&lang=<?=LANGUAGE_ID?>"><?=$sign['change']?></a>
				</td>

			</tr>
<?		} ?>
		</table>

<?		if (count($orders) == 0) { ?>
		<div style="margin: 0px 0 30px 0; font-size: 20px; color: #A00;"><?=$sign['no_order']?></div>
<?		} else { ?>
		<div style="float: right; padding: 5px 0 8px 0;">
			<input style="height: 18px;" value="<?=$button['check']['Y']?>" type="button" onclick="edost_SetData('check_Y')">
			<input style="height: 18px;" value="<?=$button['check']['N']?>" type="button" onclick="edost_SetData('check_N')">
		</div>

		<div class="buttons" style="margin: 10px 0 30px 0;">
			<input id="button_print" class="adm-btn-save" value="<?=$button_print?>" type="button" onclick="edost_SetData('print', this.id)">
		</div>
<?		} ?>

<?	} ?>



<?		if (count($orders) != 0) { ?>
		<div style="margin-top: 35px; color: #888; font-size: 16px;">
			<b><?=$sign['manual_print_head']?></b>
			<img id="manual_print_hint" style="position: absolute; margin: 3px 0 0 6px;" src="http://edostimg.ru/img/hint/hint.gif">
			<script type="text/javascript"> new top.BX.CHint({parent: top.BX('manual_print_hint'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 250, hint: '<?=$sign['manual_print_hint']?>'}); </script>
		</div>
		<div id="doc_div" style="margin-top: 5px; padding: 0px; ">
<?			foreach ($docs as $k => $doc) if (!empty($doc['mode'])) { ?>
			<div class="checkbox" style="padding-top: 5px; font-size: 13px;">
				<input id="doc_<?=$k?>" value="<?=$k?>" style="margin: 0px;" type="checkbox"<?=(in_array($k, $setting_cookie['docs_active']) ? ' checked=""' : '')?>>
				<label for="doc_<?=$k?>"><b><?=$doc['name'].($doc['quantity'] > 1 ? ' ('.$doc['quantity'].$sign['quantity'].')' : '')?></b></label>
			</div>
<?			} ?>

			<div class="buttons" style="margin: 10px 0 30px 0;">
				<input id="button_print_manual" class="adm-btn" value="<?=$button['print']['name']?>" type="button" onclick="edost_SetData('print_manual', this.id)">
			</div>
		</div>
<?		} ?>


<? if (!$ajax) { ?>
		</div>
	</div>


	<div id="setting_div" style="display: <?=($setting_module_show ? 'block' : 'none')?>; font-size: 13px;">
<?		$ar = array('setting_module'); if ($delivery2paysystem) $ar[] = 'setting_paysystem'; $ar[] = 'setting_document';
		foreach ($ar as $v) { ?>
		<div id="menu_<?=$v?>" class="menu <?=($v == 'setting_'.$setting_cookie['setting_active'] ? 'on' : 'off')?>" style="float: left; margin-right: 20px;" onclick="edost_SetData('menu', '<?=$v?>');"><?=$button[$v]?></div>
<?		} ?>
		<div style="clear: both; padding-top: 20px;"></div>
<? } ?>
<? } ?>


<? if (!$ajax) { ?>
		<div id="setting_module_div" class="setting" style="display: <?=($setting_cookie['setting_active'] == 'module' || !$show ? 'block' : 'none')?>; font-size: 13px;">
<?			if (!$setting_module_show) { ?>
			<input id="setting_module_none" type="hidden" value="">
<?			} ?>
<? } ?>

<? if (!$show || ($ajax && $mode == 'setting_module') || $setting_module_show) { ?>
<?			if (count($bitrix_site) > 2) { ?>
			<div class="checkbox" style="padding-top: 5px;">
				<input id="setting_module_individual" style="margin: 0px;" type="checkbox"<?=(!isset($module_setting['all']) ? ' checked=""' : '')?> onclick="edost_SetData('module_individual')">
				<label for="setting_module_individual" class="blue"><b><?=$setting_data['module_individual']?></b></label>
			</div>
			<div class="checkbox" style="padding-top: 5px;" id="setting_module_individual_server_div">
				<input id="setting_module_individual_server" style="margin: 0px;" type="checkbox"<?=($module_individual_server ? ' checked=""' : '')?> onclick="edost_SetData('module_individual')">
				<label for="setting_module_individual_server" class="blue"><b><?=$setting_data['module_individual_server']?></b></label>
			</div>
			<div style="padding-bottom: 25px;"></div>
<?			} ?>

<?			foreach ($bitrix_site as $site_key => $site) {
				$s = (isset($module_setting[$site['ID']]) ? $module_setting[$site['ID']] : $module_setting['default']); ?>
			<div id="setting_module_<?=$site_key?>" class="setting_module">
<?				if ($site_key !== 'all') { ?>
				<div class="delimiter"></div>
				<div id="setting_module_head_<?=$site_key?>" class="head">
					<?=str_replace(array('<', '>'), array('&lt;', '&gt;'), $site['name'].' ('.$site['ID'].')')?>:
				</div>
<?				} ?>

				<div id="setting_module_<?=$site_key?>_active" class="checkbox" style="margin-top: 5px; font-weight: bold;">
					<input id="setting_module_active_<?=$site_key?>" style="margin: 0px;" type="checkbox"<?=($s['ACTIVE'] == 'Y' ? ' checked=""' : '')?> onclick="edost_SetData('module_individual')">
					<label for="setting_module_active_<?=$site_key?>" class="green"><?=$setting_data['module_config']['active']?></label>
				</div>

<?				$lines = array('server', 'shop');
				foreach ($lines as $line) { ?>
				<div id="setting_module_<?=$site_key?>_<?=$line?>">
<?					if ($line == 'server' && isset($s['edost']['error'])) { ?>
					<div style="margin: 12px 0 10px 0;">
						<span class="error"><?=$s['edost']['error']?></span>
					</div>
<?					} ?>

<?					foreach ($s['CONFIG']['CONFIG'] as $k => $v) {
						$a = (in_array($k, $module_server_key) ? true : false);
						if (!(($line == 'server' && $a) || ($line != 'server' && !$a))) continue;

						$c = (isset($setting_data['module_config'][$k]) ? $setting_data['module_config'][$k] : false);
						$id = 'setting_module_'.$k.'_'.$site_key;
?>
					<div id="<?=$id?>_div" class="checkbox" style="margin-top: <?=($k == 'template' ? 15 : 5)?>px;">
<?						if ($v['TYPE'] == 'CHECKBOX') { ?>
						<input id="<?=$id?>" <?=(!empty($c['update']) ? 'onclick="edost_SetData(\'module_individual\')"' : '')?> style="margin: 0px;" type="checkbox"<?=($v['VALUE'] == 'Y' ? ' checked=""' : '')?>>
						<label for="<?=$id?>" <?=($k == 'template' ? 'class="orange"' : '')?>><b><?=$v['TITLE']?></b></label>
<?						} else { ?>
						<b><?=$v['TITLE']?></b><?=($v['TYPE'] == 'TEXT' ? ':' : '')?>
<?						} ?>

<?						if ($v['TYPE'] == 'TEXT') { $length = (!empty($c['length']) ? $c['length'] : 40); ?>
						<input class="normal" id="<?=$id?>" value="<?=$v['VALUE']?>" type="text" style="padding: 0px 4px; width: <?=$length*7?>px;" maxlength="<?=$length?>">
<?							if ($line == 'server') { ?>
						<input id="<?=$id?>_start" type="hidden" value="<?=$v['VALUE']?>">
<?							} ?>
<?						} ?>

<?						if ($v['TYPE'] == 'DROPDOWN') { $ar = $v['VALUES']; ?>
						<select id="<?=$id?>" <?=(!empty($c['update']) ? 'onclick="edost_SetData(\'module_individual\')"' : '')?> style="vertical-align: baseline;">
<?							foreach ($ar as $k2 => $v2) { ?>
							<option value="<?=$k2?>" <?=($k2 == $v['VALUE'] ? 'selected=""' : '')?>><?=$v2?></option>
<?							} ?>
						</select>
<?						} ?>

						<?=(!empty($c['note']) ? '<span class="note">'.$c['note'].'</span>' : '')?>
						<?=(!empty($c['hint']) ? draw_hint($id, $c['hint'], 5, 2) : '')?>
					</div>
<?					} ?>

<?					if ($line == 'shop') { ?>
					<div class="checkbox" style="padding-top: 20px;">
						<b><?=$setting_data['module_name']?></b>
						<input class="normal" id="setting_module_name_<?=$site_key?>" value="<?=str_replace('"', '&quot;', $s['NAME'])?>" type="text" style="vertical-align: baseline; padding: 0px 4px; width: 200px;" maxlength="100">
						<?=draw_hint('setting_module_name_'.$site_key, $setting_data['module_name_hint'], 0, 2)?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<b><?=$setting_data['module_description']?></b>
						<input class="normal" id="setting_module_description_<?=$site_key?>" value="<?=str_replace('"', '&quot;', $s['DESCRIPTION'])?>" type="text" style="vertical-align: baseline; padding: 0px 4px; width: 280px;" maxlength="1000">
						<?=draw_hint('setting_module_description_'.$site_key, $setting_data['module_description_hint'], 0, 2)?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						<b><?=$setting_data['module_sort']?></b> <input class="normal" id="setting_module_sort_<?=$site_key?>" value="<?=$s['SORT']?>" type="text" style="vertical-align: baseline; padding: 0px 4px; width: 40px;" maxlength="4">
					</div>

<?						if ($module_tariff_count > 5) { ?>
					<div style="padding-top: 15px;">
						<span id="setting_module_tariff_show_<?=$site_key?>" class="link" style="color: #F00; display: <?=($setting_cookie['setting_tariff_show'] == 'Y' ? 'none' : 'inline')?>;" onclick="edost_SetData('module_individual', '<?=$site_key?>|tariff|show')"><?=$setting_data['module_tariff_show']?></span>
						<span id="setting_module_tariff_hide_<?=$site_key?>" class="link" style="color: #F88; display: <?=($setting_cookie['setting_tariff_show'] != 'Y' ? 'none' : 'inline')?>;" onclick="edost_SetData('module_individual', '<?=$site_key?>|tariff|hide')"><?=$setting_data['module_tariff_hide']?></span>
					</div>
<?						} ?>

					<div id="setting_module_tariff_<?=$site_key?>" style="display: <?=($module_tariff_count <= 5 || $setting_cookie['setting_tariff_show'] == 'Y' ? 'block' : 'none')?>;">
<?						foreach ($s['edost']['data'] as $k => $v) {
							$p = $s['PROFILES'][$k];
							$id = $k.'_'.$site_key;
							if ($k == 0) { ?>
							<div style="padding-top: 15px; font-size: 14px; font-weight: bold; color: #888;"><?=$setting_data['tariff_zero']?> <?=draw_hint('setting_module_tariff_zero_'.$site_key, $setting_data['tariff_zero_hint'], 4, 1)?></div>
<?							} ?>
							<div class="checkbox" style="margin-top: <?=(isset($v['company_id']) && $c !== 0 && $c != $v['company_id'] ? 10 : 4)?>px;">
								<input class="normal" id="setting_module_title_<?=$id?>" value="<?=str_replace('"', '&quot;', $p['TITLE'])?>" type="text" style="width: 400px;" maxlength="100">
								<input class="normal" id="setting_module_description_<?=$id?>" value="<?=str_replace('"', '&quot;', $p['DESCRIPTION'])?>" type="text" style="width: 500px;" maxlength="1000">
							</div>
<?							if ($k == 0 && count($s['edost']['data']) > 1) { $c = 0; ?>
							<div style="padding-top: 10px; font-size: 14px; color: #888;">
								<div style="display: inline-block; width: 410px;"><?=$setting_data['tariff_title']?> <?=draw_hint('setting_module_tariff_title_'.$site_key, $setting_data['tariff_title_hint'], 4, 1)?></div>
								<div style="display: inline-block;"><?=$setting_data['tariff_description']?></div>
							</div>
<?							} else $c = $v['company_id'];
						} ?>
					</div>
<?					} ?>
				</div>
<?				} ?>

				<div style="padding-bottom: 25px;"></div>
			</div>
<?			} ?>

<?			if ($module_tariff_count > 0) { ?>
			<div style="max-width: 900px;">
				<div style="float: right; padding: 10px 0 8px 0;">
					<input id="setting_module_button_title" style="height: 18px;" value="<?=$button['setting_module_title']?>" type="button" onclick="edost_SetData('setting_module_title')">
				</div>
			</div>
<?			} ?>

			<div style="padding-top: 5px;">
				<input id="setting_module_save" class="adm-btn-save" value="<?=$button['setting_save']?>" type="button" onclick="edost_SetData('setting_module_save')">
			</div>
			<div id="setting_module_save_loading" class="adm-btn-load-img" style="margin-top: -24px; margin-left: 80px; display: none;"></div>

			<div id="setting_module_save_error" style="color: #800;"></div>
<? } ?>

<? if (!$ajax) { ?>
		</div>
<? } ?>




<? if ($show) { ?>


<? if ($delivery2paysystem) { ?>
<? if (!$ajax) { ?>
		<div id="setting_paysystem_div" class="setting" style="display: <?=($setting_cookie['setting_active'] == 'paysystem' ? 'block' : 'none')?>; font-size: 13px;">
			<input id="setting_paysystem_none" type="hidden" value="">
<? } ?>

<? if ($ajax && $mode == 'setting_paysystem') { ?>
<?			$i = 0;
			foreach ($pay_system as $k => $v) if (!isset($v['cod'])) { $i++; ?>
			<div id="setting_paysystem_<?=$k?>" class="setting_paysystem">
<?				if ($i != 1) { ?>
				<div class="delimiter" style="margin: 15px 0 15px 0;"></div>
<?				} ?>
				<div class="head" style="margin-top: 0px;">
					<?=str_replace(array('<', '>'), array('&lt;', '&gt;'), $v['name'].' ('.$k.')')?>:
				</div>

				<div class="radio" style="padding-top: 5px; font-weight: bold;">
<?					foreach ($setting_data['paysystem_radio'] as $k2 => $v2) { ?>
					<label>
						<input style="margin: 0px;" name="setting_paysystem_radio_<?=$k?>" id="setting_paysystem_<?=$k2?>_<?=$k?>" type="radio" <?=($k2 == $v['radio'] ? 'checked=""' : '')?> onclick="edost_SetData('paysystem', '<?=$k?>')" value="">
						<span class="<?=$v2[1]?>"><?=$v2[0]?></span>
					</label>&nbsp;&nbsp;&nbsp;&nbsp;
<?					} ?>
				</div>

				<div id="setting_paysystem_tariff_<?=$k?>" style="padding: 10px 0 10px 0; display: <?=($v['radio'] == 'list' ? 'block' : 'none')?>;">
<?					foreach ($module_setting['default']['edost']['data'] as $k2 => $v2) {
						$id = 'settingpaysystem'.$k.'_'.$k2; ?>
						<div class="checkbox" style="margin-top: <?=(isset($v2['company_id']) && $c !== 0 && $c != $v2['company_id'] ? 10 : 4)?>px;">
							<input id="<?=$id?>" style="margin: 0px;" type="checkbox"<?=($v['radio'] == 'all' || in_array('edost:'.$k2, $v['edost']) ? ' checked=""' : '')?>>
							<label for="<?=$id?>"><b><?=$module_setting['default']['PROFILES'][$k2]['TITLE']?></b></label>
						</div>
<?						$c = $v2['company_id'];
					} ?>

					<div style="padding-top: 15px;">
						<input style="height: 18px;" value="<?=$button['check']['Y']?>" type="button" onclick="edost_SetData('paysystem', '<?=$k?>|all')">
						<input style="height: 18px;" value="<?=$button['check']['N']?>" type="button" onclick="edost_SetData('paysystem', '<?=$k?>|none')">
					</div>
				</div>
			</div>
<?			} ?>

			<div style="padding-top: 20px;">
				<input id="setting_paysystem_save" class="adm-btn-save" value="<?=$button['setting_save']?>" type="button" onclick="edost_SetData('setting_paysystem_save')">
			</div>
			<div id="setting_paysystem_save_loading" class="adm-btn-load-img" style="margin-top: -24px; margin-left: 80px; display: none;"></div>

<? } ?>

<? if (!$ajax) { ?>
		</div>
<? } ?>
<? } ?>




<? if (!$ajax) { ?>
		<div id="setting_document_div" class="setting" style="display: <?=(empty($setting_cookie['setting_active']) || $setting_cookie['setting_active'] == 'document' ? 'block' : 'none')?>;">
			<div style="margin-top: 5px; padding: 0px; border: 0px solid #CCC;">
				<b><?=$setting_data['browser_head']?></b>
				<input id="setting_browser_start" type="hidden" value="<?=$setting['browser']?>">
				<select id="setting_browser">
<?					foreach ($setting_data['browser'] as $k => $v) { ?>
					<option value="<?=$k?>" <?=($k == $setting['browser'] ? 'selected' : '')?>><?=$v?></option>
<?					} ?>
				</select>
			</div>

			<div class="checkbox" style="padding-top: 5px;">
				<b><?=$setting_data['duplex_x'][0]?>
				<input class="normal" id="setting_duplex_x" value="<?=$setting['duplex_x']?>" type="text" style="vertical-align: baseline; padding: 0px 4px; height: 19px; width: 30px;" maxlength="4">
				<?=$setting_data['duplex_x'][1]?></b>
				<?=draw_hint('duplex_x', $setting_data['duplex_x'][2])?>
			</div>


			<div class="checkbox" style="padding-top: 20px;">
				<input id="setting_show_order_id" style="margin: 0px;" type="checkbox"<?=($setting['show_order_id'] == 'Y' ? ' checked=""' : '')?>>
				<label for="setting_show_order_id"><b><?=$setting_data['show_order_id']?></b></label>

		        <span style="vertical-align: middle;">
		        (<?=$setting_data['info_color_head']?>
				<select id="setting_info_color" style="padding: 1px; height: 20px;">
<?					foreach ($setting_data['info_color'] as $v) { ?>
					<option value="<?=$v[1]?>" <?=($v[1] == $setting['info_color'] ? 'selected' : '')?>><?=$v[0]?></option>
<?					} ?>
				</select>)
		        </span>
			</div>

			<div class="checkbox" style="padding-top: 5px;">
				<input id="setting_insurance_107_start" type="hidden" value="<?=$setting['insurance_107']?>">
				<input id="setting_insurance_107" style="margin: 0px;" type="checkbox"<?=($setting['insurance_107'] == 'Y' ? ' checked=""' : '')?>>
				<label for="setting_insurance_107"><b><?=$setting_data['insurance_107']?></b></label>
			</div>

			<div class="checkbox" style="padding-top: 5px;">
				<input id="setting_duplex" style="margin: 0px;" type="checkbox"<?=($setting['duplex'] == 'Y' ? ' checked=""' : '')?>>
				<label for="setting_duplex"><b><?=$setting_data['duplex']?></b></label>
			</div>

			<div style="margin-top: 25px; font-weight: bold;"><?=$setting_data['passport_head']?></div>
			<div style="margin-top: 5px; padding: 0px;">
<?				foreach ($setting_data['passport'] as $k => $v) { ?><?=$v['name']?><input class="normal" id="passport_<?=$k?>" value="<?=$passport[$k]?>" type="text" style="vertical-align: baseline; padding: 0px 4px; height: 19px; width: <?=$v['width']?>px;" maxlength="<?=$v['max']?>"><? } ?>
			</div>


			<div style="padding-top: 25px;">
				<b><?=$setting_data['status']?></b>
				<input id="setting_complete_status_start" type="hidden" value="<?=$setting['complete_status']?>">
				<select id="setting_complete_status">
<?					foreach ($order_status as $k => $v) { ?>
					<option value="<?=$k?>" <?=($k == $setting['complete_status'] ? 'selected' : '')?>><?=($k != 'none' ? '['.$k.'] ' : '')?><?=$v?></option>
<?					} ?>
				</select>
			</div>
			<div style="margin-top: 5px;">
				<b><?=$setting_data['cod']?></b>
				<select id="setting_cod">
<?					$ar = array('none' => array('name' => $sign['none'])) + $pay_system;
					foreach ($ar as $k => $v) { $i++; ?>
					<option value="<?=$k?>" <?=($k == $setting['cod'] ? 'selected' : '')?>><?=($k !== 'none' ? '['.$k.'] ' : '').$v['name']?></option>
<?					} ?>
				</select>
			</div>


			<div class="checkbox" style="padding-top: 25px;">
				<input id="setting_show_allow_delivery_start" type="hidden" value="<?=$setting['show_allow_delivery']?>">
				<input id="setting_show_allow_delivery" style="margin: 0px;" type="checkbox"<?=($setting['show_allow_delivery'] == 'Y' ? ' checked=""' : '')?>>
				<label for="setting_show_allow_delivery"><b><?=$setting_data['show_allow_delivery']?></b></label>
			</div>

			<div class="checkbox" style="padding-top: 5px;">
				<input id="setting_hide_unpaid_start" type="hidden" value="<?=$setting['hide_unpaid']?>">
				<input id="setting_hide_unpaid" style="margin: 0px;" type="checkbox"<?=($setting['hide_unpaid'] == 'Y' ? ' checked=""' : '')?>>
				<label for="setting_hide_unpaid"><b><?=$setting_data['hide_unpaid']?></b></label>
			</div>

			<div class="checkbox" style="padding-top: 5px;">
				<input id="setting_hide_without_doc_start" type="hidden" value="<?=$setting['hide_without_doc']?>">
				<input id="setting_hide_without_doc" style="margin: 0px;" type="checkbox"<?=($setting['hide_without_doc'] == 'Y' ? ' checked=""' : '')?>>
				<label for="setting_hide_without_doc"><b><?=$setting_data['hide_without_doc']['name']?></b></label>
			</div>

<?			if ($deducted_enabled) { ?>
			<div class="checkbox" style="padding-top: 5px;">
				<input id="setting_hide_deducted_start" type="hidden" value="<?=$setting['hide_deducted']?>">
				<input id="setting_hide_deducted" style="margin: 0px;" type="checkbox"<?=($setting['hide_deducted'] == 'Y' ? ' checked=""' : '')?>>
				<label for="setting_hide_deducted"><b><?=$setting_data['hide_deducted']?></b></label>
			</div>

			<div class="checkbox" style="padding-top: 5px;">
				<input id="setting_deducted_start" type="hidden" value="<?=$setting['deducted']?>">
				<input id="setting_deducted" style="margin: 0px;" type="checkbox"<?=($setting['deducted'] == 'Y' ? ' checked=""' : '')?>>
				<label for="setting_deducted"><b><?=$setting_data['deducted']?></b></label>
			</div>
<?			} ?>


			<div style="margin-top: 20px; color: #080;"><b><?=$setting_data['show_status']?></b></div>
			<div id="setting_show_status_div" style="margin-top: 0px; padding: 0px; ">
				<input id="setting_show_status_start" type="hidden" value="<?=$setting['show_status']?>">
<?				foreach ($order_status as $k => $v) if ($k != 'none') { ?>
				<div class="checkbox" style="padding-top: 5px; font-size: 13px;">
					<input id="settingshowstatus_<?=$k?>" value="<?=$k?>" style="margin: 0px;" type="checkbox"<?=(in_array($k, $show_status) ? ' checked=""' : '')?>>
					<label for="settingshowstatus_<?=$k?>"><b><?='['.$k.'] '.$v?></b></label>
				</div>
<?				} ?>
			</div>


			<div style="margin-top: 20px; color: #B00;"><b><?=$setting_data['docs_disable']?></b></div>
			<div id="setting_docs_disable_div" style="margin-top: 0px; padding: 0px; ">
<?				foreach ($docs as $k => $doc) if (!empty($doc['mode'])) { ?>
				<div class="checkbox" style="padding-top: 5px; font-size: 13px;">
					<input id="settingdoc_<?=$k?>" value="<?=$k?>" style="margin: 0px;" type="checkbox"<?=(in_array($doc['id'], $docs_disable) ? ' checked=""' : '')?>>
					<label for="settingdoc_<?=$k?>"><b><?=$doc['name'].($doc['quantity'] > 1 ? ' ('.$doc['quantity'].$sign['quantity'].')' : '')?></b></label>
				</div>
<?				} ?>
			</div>


			<div style="padding-top: 20px;">
				<input id="setting_document_save" class="adm-btn-save" value="<?=$button['setting_save']?>" type="button" onclick="edost_SetData('setting_document_save')">
			</div>
			<div id="setting_document_save_loading" class="adm-btn-load-img" style="margin-top: -24px; margin-left: 80px; display: none;"></div>
		</div>
	</div>
</div>
<? } ?>

<script type="text/javascript">
	edost_UpdateActive('all');
</script>

<?
}

if ((!$show && !$ajax) || $setting_module_show) { ?>
<script type="text/javascript">
	edost_SetData('module_individual');
</script>
<? }


if (!$ajax) require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');


// ����� ���������
function draw_hint($name, $data, $x = 6, $y = 3) {
?>
	<img id="<?=$name?>_hint" style="position: absolute; margin: <?=$y?>px 0 0 <?=$x?>px;" src="http://edostimg.ru/img/hint/hint.gif">
	<script type="text/javascript"> new top.BX.CHint({parent: top.BX('<?=$name?>_hint'), show_timeout: 10, hide_timeout: 100, dx: 2, preventHide: true, min_width: 400, hint: '<?=$data?>'}); </script>
<?
}

// ����� �������� �� ����������
function draw_string($name, $n) {

	$ar = array(
		'order' => array('�����', '������', '�������'),
		'item' => array('�������', '��������', '���������'),
		'item2' => array('�������', '�������', '�������'),
	);

	$s = '';
	if ($n >= 11 && $n <= 19) $s = $ar[$name][2];
	else {
		$x = $n % 10;
		if ($x == 1) $s = $ar[$name][0];
		else if ($x >= 2 && $x <= 4) $s = $ar[$name][1];
		else $s = $ar[$name][2];
	}

	$s = $GLOBALS['APPLICATION']->ConvertCharset($s, 'windows-1251', SITE_CHARSET);

	return $n.' '.$s;

}

// ������ ������ � ������� �� ������ �� �����
function draw_field($field_key, $field, $doc, &$page, $block = false) {

	$space = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

	// ���������� ������� ������ (���� ����� �� ������ � ����)
	if (isset($doc['length']) && is_array($doc['length']))
		foreach ($doc['length'] as $k => $v) {
			$size = '';
			$length = strlen($field[$k]);
			if ($length > $v) {
				$size = ($block && $length > $v*1.5 ? ' line-height: 2mm;' : '').' font-size: 2.5mm;';
				if (isset($doc['space'][$k])) $field[$k] = $space . $field[$k];
			}
			if ($block && $length > $v*2.8) $field[$k] = substr($field[$k], 0, ceil($v*2.8)).'...';

			$page = str_replace('%'.$k.'_size%', $size, $page);
		}

	$page = str_replace($field_key, $field, $page);

}

?>