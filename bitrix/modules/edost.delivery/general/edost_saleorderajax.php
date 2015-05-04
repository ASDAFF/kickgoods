<?
class CEdostModifySaleOrderAjax {

	// проверка наличия в заказе доставки и наложенного платежа edost
	public static function CheckOrderDevileryEdostAndEdostPayCod($arOrder) {
		if (isset($arOrder['PAY_SYSTEM_ID']) && isset($arOrder['PERSON_TYPE_ID']) && !empty($arOrder['DELIVERY_ID']) && substr($arOrder['DELIVERY_ID'], 0, 6) == 'edost:') {
			$dbPaySystem = CSalePaySystem::GetList(array('SORT' => 'ASC', 'PSA_NAME' => 'ASC'), array('ACTIVE' => 'Y', 'PERSON_TYPE_ID' => $arOrder['PERSON_TYPE_ID'], 'PSA_HAVE_PAYMENT' => 'Y'));
			while ($arPaySystem = $dbPaySystem->Fetch()) if ($arPaySystem['ID'] == $arOrder['PAY_SYSTEM_ID']) {
				if (substr($arPaySystem['PSA_ACTION_FILE'], -11) == 'edostpaycod') return true;
				break;
			}
		}
		return false;
	}

	// загрузка настроек модуля edost
	public static function GetEdostConfig($site_id) {

		$r = array();
		$s = COption::GetOptionString('edost.delivery', 'module_setting', '');
		if ($s != '') {
			$s = unserialize($s);
//			echo '<br><b>GetEdostConfig ('.$site_id.'):</b> <pre style="font-size: 12px">'.print_r($s, true).'</pre>';

			if (isset($s['all'])) $s = $s['all'];
			else if (isset($s[$site_id])) $s = $s[$site_id];
			else $s = false;

			if ($s !== false) $s = explode(';', $s);
		}
		$setting_key = array(
			'id' => '', 'ps' => '', 'host' => '', 'hide_error' => 'N', 'show_zero_tariff' => 'N',
			'map' => 'N', 'cod_status' => '', 'send_zip' => 'Y', 'hide_payment' => 'Y', 'sort_ascending' => 'N',
			'template' => 'N', 'template_format' => 'odt', 'template_block' => 'off', 'template_block_type' => 'none', 'template_cod' => 'td', 'template_autoselect_office' => 'N', 'autoselect' => 'Y',
		);
		$i = 0;
		if (is_array($s)) foreach ($setting_key as $k => $v) {
			$r[$k] = (isset($s[$i]) ? $s[$i] : $v);
			$i++;
		}
		return $r;

	}


	// вызывается после обработки платежной системы при расчете заказа в DoCalculateOrder
	function OnSCCalculateOrderPaySystem(&$arOrder) {
//		$_SESSION['EDOST']['arOrder'] = $arOrder;

		$id = explode(':', $arOrder['DELIVERY_ID']);
		if ($id[0] !== 'edost' || empty($id[1]) || !class_exists('CDeliveryEDOST')) return;

		$tariff = CDeliveryEDOST::GetEdostTariff(intval($id[1]));
		$priceoffice = false;
		if (!empty($tariff['priceoffice']) && !empty($arOrder['ORDER_PROP'])) {
			$props = array();
			$ar = CSaleOrderProps::GetList(array(), array(), false, false, array('ID', 'CODE'));
			while ($v = $ar->GetNext()) if ($v['CODE'] == 'ADDRESS') $props[] = $v['ID'];
			if (!empty($props)) foreach ($arOrder['ORDER_PROP'] as $k => $v) if (in_array($k, $props)) {
				$priceoffice = edost_class::GetOfficePrice($tariff['priceoffice'], '', $v);
				break;
			}
		}

		$price = ($priceoffice !== false ? $priceoffice['price'] : -1);
		if (self::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) {
			$price = ($priceoffice !== false ? $priceoffice['pricecash'] : $tariff['pricecash']);
			if ($price < 0) $price = 0; // для выбранного тарифа наложенный платеж недоступен
		}
		if ($price >= 0) {
			$base_currency = CDeliveryEDOST::GetRUB();
			$arOrder['DELIVERY_PRICE'] = edost_class::GetPrice('value', $price, $base_currency, $arOrder['CURRENCY']);
			$arOrder['PRICE_DELIVERY'] = $arOrder['DELIVERY_PRICE'];
		}

	}


	// отмена отправки письма с напоминанием об оплате заказа, если выбран наложенный платеж edost
	function OnSCOrderRemindSendEmail($OrderID, &$eventName, &$arFields) {
		if ($eventName == 'SALE_ORDER_REMIND_PAYMENT') {
			$arOrder = CSaleOrder::GetByID($OrderID);
			if (self::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) return false;
		}
		return true;
	}


	// установка статуса нового заказа, если выбран наложенный платеж edost
	function OnSCBeforeOrderAdd(&$arOrder) {
		if (self::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) {
			$config = self::GetEdostConfig(isset($arOrder['SITE_ID']) ? $arOrder['SITE_ID'] : 'all');
			if ($config['cod_status'] != '') $arOrder['STATUS_ID'] = $config['cod_status'];
		}
	}


	// вызывается после подтверждения заказа
	function OnSCOrderOneStepComplete($ID, $arOrder) {
	}


	// вызывается перед расчетом доставки
	function OnSCOrderOneStepOrderPropsHandler(&$arResult, &$arUserResult) {
//		echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult, true).'</pre>';
//		echo '<br><b>arUserResult:</b> <pre style="font-size: 12px">'.print_r($arUserResult, true).'</pre>';

		$sign = GetMessage('EDOST_DELIVERY_SIGN');

		// загрузка дополнительных параметров из id доставки: edost:57:'office_id':'cod_tariff'
		$id = (isset($arUserResult['DELIVERY_ID']) ? $arUserResult['DELIVERY_ID'] : '');
		$v = explode(':', $id);
		$ar = array();
		if ($v[0] === 'edost' && isset($v[2])) {
			$id = 'edost:'.$v[1];
			$arUserResult['DELIVERY_ID'] = $id;
			$ar['profile'] = $v[1];
			if (!empty($v[2])) $ar['office_id'] = $v[2];
			if (!empty($v[3])) $ar['cod_tariff'] = ($v[3] === 'Y' ? true : false);
		}
		$ar['id'] = $id;
		if (!empty($_REQUEST['edost_bookmark'])) $ar['bookmark'] = substr($_REQUEST['edost_bookmark'], 0, 10);
		$arResult['edost']['active'] = $ar;

		$arResult['edost']['error'] = (isset($arResult['ERROR']) ? $arResult['ERROR'] : array());
		$arResult['edost']['config'] = self::GetEdostConfig(SITE_ID);


		// поле ADDRESS (для сохранения данных по выбранному пункту выдачи)
		$address_id = -1;
		$address = '';
		foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as $k => $v) if ($v['CODE'] == 'ADDRESS' && in_array($v['TYPE'], array('TEXT', 'TEXTAREA'))) {
			$address_id = $k;
			$address = $v['VALUE'];
		}
		$arResult['edost']['address_id'] = $address_id;

		// сброс старого (из профиля покупателя) адреса пункта выдачи при первой загрузке + перенос в дефолтные значения для нового выбора
		if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_SESSION['EDOST']['readonly'])) {
			$_SESSION['EDOST']['readonly'] = false;
			if ($address != '') {
				$ar = array($sign['shop'].':', $sign['office'].':', $sign['office'], $sign['terminal'], $sign['postamat']['name']);
				foreach ($ar as $v) if (strpos($address, $v.' ') === 0) {
					$s = explode(', '.$sign['code'].': ', $address);
					if (!empty($s[1])) {
						$s = explode('/', $s[1]);
						if (!empty($s[3])) {
							$office_id = $s[1];
							$s = explode('-', $s[3]);
							$_SESSION['EDOST']['office_default']['profile'] = array('id' => $office_id, 'profile' => intval($s[0]), 'cod_tariff' => !empty($s[1]) ? true : false);
						}
						$arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] = '';
					}
					break;
				}
			}
		}

	}


	// вызывается после расчета доставки
	function OnSCOrderOneStepDeliveryHandler(&$arResult, &$arUserResult) {
//		echo '<br><b>arResult[DELIVERY]:</b> <pre style="font-size: 12px">'.print_r($arResult['DELIVERY'], true).'</pre>';

		if (empty($arResult['DELIVERY'])) return;

		$config = (isset($arResult['edost']['config']) ? $arResult['edost']['config'] : self::GetEdostConfig(SITE_ID));
		$bitrix_delivery_id = $arUserResult['DELIVERY_ID'];

		$order = array(
			'PRICE' => $arResult['ORDER_PRICE'],
			'WEIGHT' => $arResult['ORDER_WEIGHT'],
			'LOCATION_FROM' => COption::GetOptionInt('sale', 'location'),
			'LOCATION_TO' => $arUserResult['DELIVERY_LOCATION'],
			'LOCATION_ZIP' => $arUserResult['DELIVERY_LOCATION_ZIP'],
		);

		// новые параметры битрикс 14
		$ar = array('MAX_DIMENSIONS' => 'MAX_DIMENSIONS', 'DIMENSIONS' => 'ORDER_DIMENSIONS', 'ITEMS_DIMENSIONS' => 'ITEMS_DIMENSIONS', 'ITEMS' => 'BASKET_ITEMS', 'EXTRA_PARAMS' => 'DELIVERY_EXTRA');
		foreach ($ar as $k => $v) if (isset($arResult[$v])) $order[$k] = $arResult[$v];

		if (!class_exists('edost_class')) require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/edost.delivery/classes/general/delivery_edost.php');
		$data = edost_class::FormatTariff($arResult['DELIVERY'], $arResult['BASE_LANG_CURRENCY'], $order, isset($arResult['edost']['active']) ? $arResult['edost']['active'] : false);
		if ($data === false) return;
		$arResult['edost']['format'] = $data;
		$arUserResult['DELIVERY_ID'] = $data['active']['id'];

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$base_currency = CDeliveryEDOST::GetRUB();

		// перевод форматированных тарифов обратно в формат битрикса (для стандартного шаблона)
		$ar = array();
		if (!empty($data['data'])) foreach ($data['data'] as $f_key => $f) foreach ($f['tariff'] as $k => $v) if (isset($v['id'])) {
			$id = $v['id'];
			if (isset($v['profile'])) {
				$profile = $v['profile'];

				if (!isset($ar[$id])) {
					if (isset($arResult['DELIVERY'][$id])) $ar[$id] = $arResult['DELIVERY'][$id];
					else $ar[$id] = array('SID' => $id, 'SORT' => 0, 'TITLE' => '', 'DESCRIPTION' => '');
					$ar[$id]['PROFILES'] = array();
				}

				$s = (isset($arResult['DELIVERY'][$id]['PROFILES'][$profile]) ? $arResult['DELIVERY'][$id]['PROFILES'][$profile] : array());
				if (!isset($s['SID'])) {
					$s['SID'] = $v['profile'];
					$s['TITLE'] = $v['company'].($v['name'] !== '' ? ' ('.$v['name'].')' : '');
					$s['DESCRIPTION'] = $v['description'];
					$s['FIELD_NAME'] = 'DELIVERY_ID';
				}

				if ($id.':'.$profile === $data['active']['id']) $s['CHECKED'] = 'Y';
				else if (isset($s['CHECKED'])) unset($s['CHECKED']);

				if ($id == 'edost') {
					if ($config['template'] == 'Y') $s['DESCRIPTION'] = $sign['template_warning'].($s['DESCRIPTION'] != '' ? '<br>' : '').$s['DESCRIPTION'];
					else if ($v['tariff_id'] == 29 && isset($data['pickpointmap'])) $s['pickpointmap'] = $data['pickpointmap'];
					else if (in_array($v['format'], array('shop', 'office', 'terminal')) && !empty($data['office'][$v['company_id']])) $s['office_data'] = $data['office'][$v['company_id']];

					if (!empty($v['error'])) $s['TITLE'] .= '<br><font color="#FF0000">'.$v['error'].'</font>';
				}

				$ar[$id]['PROFILES'][$profile] = $s;
			}
			else {
				if (!isset($arResult['DELIVERY'][$id])) continue;
				$s = $arResult['DELIVERY'][$id];
				if ($id === $data['active']['id']) $s['CHECKED'] = 'Y';
				else if (isset($s['CHECKED'])) unset($s['CHECKED']);
				$ar[$id] = $s;
			}
		}
		$arResult['DELIVERY'] = $ar;


		// данные для стандартного шаблона (стоимость доставки, дни и офисы)
		if (!empty($arResult['DELIVERY']['edost']['PROFILES']) && $config['template'] != 'Y') {
			$office_set = (!empty($_REQUEST['edost_office']) ? substr($_REQUEST['edost_office'], 0, 10) : 0); // выбранный офис из POST
			foreach ($arResult['DELIVERY']['edost']['PROFILES'] as $profile => $v) {
				$tariff = CDeliveryEDOST::GetEdostTariff($profile);

				// офисы
				$office_number = 0;
				if (isset($v['office_data'])) {
					$office_number = count($v['office_data']);

					$i = (isset($_SESSION['EDOST']['address'][$tariff['company_id']]) ? $_SESSION['EDOST']['address'][$tariff['company_id']] : '');
					if (isset($v['office_data'][$office_set])) $office_id = $office_set;
					else if (isset($v['office_data'][$i])) $office_id = $i;
					else foreach ($v['office_data'] as $o) {
						$office_id = $o['id'];
						break;
					}
					$_SESSION['EDOST']['address'][$tariff['company_id']] = $office_id;

					$o = $v['office_data'][$office_id];
					$tariff = CDeliveryEDOST::GetEdostTariff($profile, $o['type']);

					if ($v['CHECKED'] == 'Y') {
						$arResult['edost']['format']['active']['address'] = edost_class::GetOfficeAddress($o, $tariff);
						$arResult['edost']['format']['active']['office_id'] = $office_id;
						if (isset($o['codmax']) && $tariff['pricecash'] > $o['codmax']) $arResult['edost']['format']['active']['cod'] = false;
						if (!empty($tariff['office_type'])) $arResult['edost']['format']['active']['office_type'] = $tariff['office_type'];
					}

					$s = '<td>'.($tariff['format'] == 'shop' ? $sign['shop'] : $sign['delivery'][$tariff['format']]).':</td><td style="padding-left: 5px;">';
					if ($office_number != 1) $s .= '<select id="edost_office_'.$profile.'" onchange="edost_SetOffice('.$profile.');">';
					foreach ($v['office_data'] as $o)
						if ($office_number == 1) $s .= '<b>'.$o['address'].'</b>'.'<input type="hidden" id="edost_office_'.$profile.'" value="'.$o['id'].'">';
						else $s .= '<option '.($o['id'] == $office_id ? 'selected="selected"' : '').' value="'.$o['id'].'">'.$o['address'].'</option>';
					if ($office_number != 1) $s .= '</select>';
					$s .= '</td>';
					$s .= '<td style="padding-left: 10px;"><a href="#" style="cursor: pointer; text-decoration: none; font-size: 11px;" onclick="edost_OpenMap('.$profile.'); return false;" >'.$sign['map2'].'</a></td>';
					$s = '<table class="edost_office_table" style="display: inline; margin: 0px;" border="0" cellspacing="0" cellpadding="0"><tr style="padding: 0px; margin: 0px;">'.$s.'</tr></table>';

					$v['onclick'] = 'edost_SetOffice('.$profile.');';
					$v['office'] = $s;
				}

				$tariff['price_formatted'] = edost_class::GetPrice('formatted', $tariff['price'], $base_currency, $arResult['BASE_LANG_CURRENCY']);

				if ($profile == 0 || !empty($tariff['priceinfo'])) $p = '';
				else if ($tariff['price'] == 0) $p = $sign['free_bitrix'];
				else $p = $tariff['price_formatted'];
				$v['price'] = $p;

				if (!empty($tariff['day'])) $v['day'] = $tariff['day'];

				if (!empty($tariff['priceinfo'])) {
					$v['price_backup'] = $tariff['price_formatted'];
					$v['priceinfo'] = edost_class::GetPrice('formatted', $tariff['priceinfo'], $base_currency, $arResult['BASE_LANG_CURRENCY']);

					$s0 = $v['DESCRIPTION'];
					$s1 = str_replace('%price_info%', $v['priceinfo'], $sign['priceinfo_warning_bitrix']);
					$s2 = ($tariff['price'] > 0 ? str_replace('%price%', $tariff['price_formatted'], $sign['priceinfo_description']) : '');
					$v['DESCRIPTION'] = $s1 . ($s1 != '' && $s2 != '' ? '<br>' : '') . $s2 . (($s1 != '' || $s2 != '') && $s0 != '' ? '<br>' : '') . $s0;
				}

				if (!empty($tariff['format'])) {
					if ($tariff['format'] == 'house') $v['DESCRIPTION'] = $sign['house_warning_bitrix'].($v['DESCRIPTION'] != '' ? '<br>' : '').$v['DESCRIPTION'];
					if ($tariff['format'] == 'terminal' && $office_number > 1) $v['DESCRIPTION'] = $sign['terminal_warning_bitrix'].($v['DESCRIPTION'] != '' ? '<br>' : '').$v['DESCRIPTION'];
				}

				$arResult['DELIVERY']['edost']['PROFILES'][$profile] = $v;
			}
		}
//		echo '<br><b>arResult[DELIVERY] NEW:</b> <pre style="font-size: 12px">'.print_r($arResult['DELIVERY'], true).'</pre>';


		// пересчет стоимости доставки (для тарифов eDost или после изменения активной доставки)
		$id = explode(':', $arUserResult['DELIVERY_ID']);
		if (empty($id[0]) || $id[0] === 'edost' || $arUserResult['DELIVERY_ID'] !== $bitrix_delivery_id) {
			if (isset($arResult['edost']['error'])) $arResult['ERROR'] = $arResult['edost']['error'];

			$price = false;
			if (!empty($id[0])) if ($id[0] === 'edost') {
				$office_type = (!empty($arResult['edost']['format']['active']['office_type']) ? $arResult['edost']['format']['active']['office_type'] : 0);
				$tariff = CDeliveryEDOST::GetEdostTariff($id[1], $office_type);
				$price = edost_class::GetPrice('price', $tariff['price'], $base_currency, $arResult['BASE_LANG_CURRENCY']);
			}
			else if (isset($id[1])) {
				$ar = CSaleDeliveryHandler::CalculateFull($id[0], $id[1], $order, $arResult['BASE_LANG_CURRENCY']);
				if ($ar['RESULT'] == 'ERROR') $arResult['ERROR'][] = (!empty($ar['TEXT']) ? $ar['TEXT'] : 'delivery error');
				else {
					$price = edost_class::GetPrice('price', $ar['VALUE'], '', $arResult['BASE_LANG_CURRENCY']);
					if (isset($ar['PACKS_COUNT'])) $arResult['PACKS_COUNT'] = $ar['PACKS_COUNT'];
				}
			}
			else foreach ($arResult['DELIVERY'] as $v) if (isset($v['ID']) && $v['ID'] == $id[0]) {
				$price = edost_class::GetPrice('price', $v['PRICE'], $v['CURRENCY'], $arResult['BASE_LANG_CURRENCY']);
				break;
			}

			$arResult['DELIVERY_PRICE'] = (!empty($price['price']) ? $price['price'] : 0);
			$arResult['DELIVERY_PRICE_FORMATED'] = (!empty($price['price']) ? $price['price_formatted'] : '');
		}


		// привязка пункта выдачи eDost к складу битрикса
		if (defined('DELIVERY_EDOST_BUYER_STORE')) {
			$store = array();
			$ar = explode(',', DELIVERY_EDOST_BUYER_STORE);
			foreach ($ar as $v) {
				$v = explode('=', $v);
				if (!empty($v[0]) && !empty($v[1])) $store[$v[0]] = $v[1];
			}
			$office_id = (!empty($arResult['edost']['format']['active']['office_id']) ? $arResult['edost']['format']['active']['office_id'] : 0);
			if (isset($store[$office_id])) {
				$arUserResult['DELIVERY_STORE'] = $arUserResult['DELIVERY_ID']; // ид выбранной доставки, которой принадлежит привязанный офис
				$arResult['BUYER_STORE'] = $store[$office_id];
			}
		}

	}


	// вызывается после обработки платежных систем
	function OnSCOrderOneStepPaySystemHandler(&$arResult, &$arUserResult) {
//		echo '<br><b>arResult[DELIVERY]:</b> <pre style="font-size: 12px">'.print_r($arResult['DELIVERY'], true).'</pre>';
//		echo '<br><b>arResult[PAY_SYSTEM]:</b> <pre style="font-size: 12px">'.print_r($arResult['PAY_SYSTEM'], true).'</pre>';

		$config = (isset($arResult['edost']['config']) ? $arResult['edost']['config'] : self::GetEdostConfig(SITE_ID));
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$arResult['edost']['javascript'] = '';

		$address_id = (isset($arResult['edost']['address_id']) ? $arResult['edost']['address_id'] : -1);
		$address = '';
		$address_type = '';
		if ($address_id != -1)
			if (!isset($arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id])) $address_id = -1;
			else {
				$v = $arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id];
				$address = $v['VALUE'];
				$address_type = $v['TYPE'];
			}
		if ($address_id != -1) $arResult['edost']['javascript'] .= '<input type="hidden" value="ORDER_PROP_'.$address_id.'" id="address_input">';


		// предупреждения модуля edost (warning)
		if (class_exists('CDeliveryEDOST')) {
			$warning = CDeliveryEDOST::GetEdostWarning();
			if ($warning != '') {
				// вывод ошибки при подтверждении заказа, если перед оформлением была выбрана почта (наземная) и есть предупреждение по индексу
				if ($arUserResult['CONFIRM_ORDER'] == 'Y' && in_array($arResult['edost']['active']['id'], array('edost:3', 'edost:4')))
					foreach (CDeliveryEDOST::$result['warning'] as $v) if (in_array($v, array(1, 2))) {
						$s = GetMessage('EDOST_DELIVERY_WARNING');
						$arResult['ERROR'][] = $s[$v];
					}

				// для стандартного шаблона
				if ($config['template'] != 'Y') $arResult['edost']['warning'] = '<span id="edost_warning" style="color: #F00; font-weight: bold;">'.$warning.'</span>';
			}
		}


		// PickPoint для стандартного шаблона
		$office_set = (!empty($_REQUEST['edost_office']) ? substr($_REQUEST['edost_office'], 0, 10) : 0); // выбранный офис из POST
		if (!empty($arResult['DELIVERY']['edost']['PROFILES']['57']['pickpointmap']) && $address_id != -1) {
			$v = $arResult['DELIVERY']['edost']['PROFILES']['57'];
			$company_id = 26;

			if (isset($_SESSION['EDOST']['location']) && $_SESSION['EDOST']['location'] != $arUserResult['DELIVERY_LOCATION']) $_SESSION['EDOST']['address'][$company_id] = '';
			else if ($office_set === 'pickpoint') $_SESSION['EDOST']['address'][$company_id] = $address;

			$s = (isset($_SESSION['EDOST']['address'][$company_id]) ? $_SESSION['EDOST']['address'][$company_id] : '');
			if ($v['CHECKED'] == 'Y') $arResult['edost']['format']['active']['address'] = $s;
			if ($s != '') {
				$s1 = explode(': ', $s);
				$s2 = explode(', '.$sign['code'].': ', $s);
				$s = ($s1[0] == $sign['postamat']['name'].' PickPoint' ? $sign['delivery']['postamat'] : $sign['delivery']['office']).': <b>'.str_replace($s1[0].': ', '', $s2[0]).'</b><br>';
			}
			else {
				$s = $sign['postamat']['get'];
				$v['onclick'] = "PickPoint.open(EdostPickPoint,{city:'".$v['pickpointmap']."', ids:null}); edost_SubmitActive('set'); submitForm();";
			}
			$v['office'] = '<a style="color: #A00; text-decoration: none;" href="#" id="EdostPickPointRef" onclick="PickPoint.open(EdostPickPoint,{city:\''.$v['pickpointmap'].'\', ids:null}); return false;">'.$s.'</a>';

			$arResult['DELIVERY']['edost']['PROFILES']['57'] = $v;
		}


		// сохранение нового адреса в поле ADDRESS
		if ($address_id != -1) {
			$address_readonly = (isset($arResult['edost']['format']['active']['address']) ? true : false);
			$address_new = ($address_readonly ? $arResult['edost']['format']['active']['address'] : false);

			if (empty($_SESSION['EDOST']['readonly']) && $office_set !== 'pickpoint') $_SESSION['EDOST']['address'][0] = $address;
			else if (!$address_readonly) $address_new = (isset($_SESSION['EDOST']['address'][0]) ? $_SESSION['EDOST']['address'][0] : '');

			if ($address_new !== false) {
				$address = $address_new;
				$arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] = $address;
				$arUserResult['ORDER_PROP'][$address_id] = $address;
			}

			$_SESSION['EDOST']['readonly'] = $address_readonly;
			$_SESSION['EDOST']['location'] = $arUserResult['DELIVERY_LOCATION'];
		}


		// удаление способов оплаты, если нет способов доставки или доставка не выбрана
		if ($config['hide_payment'] == 'Y' && (empty($arResult['edost']['format']['count']) || empty($arUserResult['DELIVERY_ID']))) $arResult['PAY_SYSTEM'] = array();

		// ошибка "не выбран способ доставки"
		if ($config['autoselect'] != 'Y' && !empty($arResult['edost']['format']['count']) && empty($arUserResult['DELIVERY_ID'])) $arResult['ERROR'][] = $sign['delivery_unchecked'];

		// ошибка "не выбрана точка самовывоза"
		if (!empty($address_readonly) && $arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] == '') $arResult['ERROR'] = array($sign['office_unchecked']);


		$cod_tariff = (!empty($arResult['edost']['format']['cod_tariff']) ? true : false);
		$cod_tariff_active = (!empty($arResult['edost']['format']['active']['cod_tariff']) ? true : false);
		if ($cod_tariff && $cod_tariff_active) $arUserResult['PAY_CURRENT_ACCOUNT'] = false;

		// удаление наложенного платежа для тарифов без наложки
		$tariff = false;
		$id = explode(':', $arUserResult['DELIVERY_ID']);
		if ($id[0] === 'edost' && class_exists('CDeliveryEDOST')) {
			$office_type = (!empty($arResult['edost']['format']['active']['office_type']) ? $arResult['edost']['format']['active']['office_type'] : 0);
			$tariff = CDeliveryEDOST::GetEdostTariff($id[1], $office_type);
			if (!isset($tariff['pricecash']) || $tariff['pricecash'] < 0) $tariff = false;
			if (empty($arResult['edost']['format']['active']['cod'])) $tariff = false;
			if ($cod_tariff && !$cod_tariff_active) $tariff = false;
		}
		$acitve = $set = $edost = false;
		foreach ($arResult['PAY_SYSTEM'] as $k => $v) {
			if (substr($v['PSA_ACTION_FILE'], -11) == 'edostpaycod')
				if ($tariff !== false) $edost = $k;
				else {
					unset($arResult['PAY_SYSTEM'][$k]);
					continue;
				}
			if ($set === false) $set = $k;
			if ($v['CHECKED'] == 'Y') $acitve = $k;
		}
		if ($cod_tariff && $cod_tariff_active && $acitve !== false && $edost !== false && $acitve != $edost) {
			unset($arResult['PAY_SYSTEM'][$acitve]);
			$acitve = false;
			$set = $edost;
		}

		// выделение первого способа оплаты, если нет активных
		if ($acitve === false && $set !== false && (empty($arUserResult['PAY_CURRENT_ACCOUNT']) || $arUserResult['PAY_CURRENT_ACCOUNT'] !== 'Y' || $cod_tariff && $cod_tariff_active)) {
			$arResult['PAY_SYSTEM'][$set]['CHECKED'] = 'Y';
			$acitve = $set;
		}
		$arUserResult['PAY_SYSTEM_ID'] = ($acitve !== false ? $arResult['PAY_SYSTEM'][$acitve]['ID'] : '');

		// учет наценок наложенного платежа
		if ($edost !== false) {
			$v = $arResult['PAY_SYSTEM'][$edost];
			$base_currency = CDeliveryEDOST::GetRUB();

			// нестандартное название и описание
			$ar = GetMessage('EDOST_DELIVERY_COD');
			if (is_array($ar)) foreach ($ar as $s) if (in_array($tariff['id'], $s['tariff'])) {
				if (isset($s['name'])) $v['PSA_NAME'] = $s['name'];
				if (isset($s['description'])) $v['DESCRIPTION'] = $s['description'];
				if (isset($s['description2']) && $tariff['id'] == 29 && strpos($address, $sign['postamat']['name'].' PickPoint') === 0) $v['DESCRIPTION'] = $s['description2'];
			}

			$p = array();
			$p += edost_class::GetPrice('codplus', $tariff['pricecash'] - $tariff['price'], $base_currency, $arResult['BASE_LANG_CURRENCY']);
			$p += edost_class::GetPrice('transfer', $tariff['transfer'], $base_currency, $arResult['BASE_LANG_CURRENCY']);
			$p += edost_class::GetPrice('codtotal', $tariff['pricecash'] - $tariff['price'] + $tariff['transfer'], $base_currency, $arResult['BASE_LANG_CURRENCY']);

			if (!empty($p['codplus'])) $v['codplus'] = str_replace('%codplus%', $p['codplus_formatted'], $sign['codplus']);
			if (!empty($p['transfer'])) $v['transfer'] = str_replace('%transfer%', $p['transfer_formatted'], $sign['transfer']);
			if (!empty($p['codplus']) && !empty($p['transfer'])) $v['codtotal'] = str_replace('%codtotal%', $p['codtotal_formatted'], $sign['codtotal']);

			// в стандартном шаблоне информация по наценке добавляется в описание
			if ($config['template'] != 'Y') {
				$ar = array('codplus', 'transfer', 'codtotal');
				foreach ($ar as $v2) if (!empty($v[$v2])) $v['DESCRIPTION'] .= ($v['DESCRIPTION'] != '' ? '<br>' : '').($v2 == 'transfer' ? '<font color="#FF0000">'.$v[$v2].'</font>' : $v[$v2]);
			}

			if (isset($v['CHECKED']) && $v['CHECKED'] == 'Y') {
				$p += edost_class::GetPrice('pricecash', $tariff['pricecash'], $base_currency, $arResult['BASE_LANG_CURRENCY']);
				$arResult['DELIVERY_PRICE'] = $p['pricecash'];
				$arResult['DELIVERY_PRICE_FORMATED'] = (!empty($p['pricecash']) ? $p['pricecash_formatted'] : '');
			}

			$arResult['PAY_SYSTEM'][$edost] = $v;
		}
//		echo '<br><b>arResult[PAY_SYSTEM] NEW:</b> <pre style="font-size: 12px">'.print_r($arResult['PAY_SYSTEM'], true).'</pre>';


		if ($config['template'] == 'Y') {
			if (!isset($arResult['edost']['format'])) $arResult['edost']['format'] = false;
		}
		else if (isset($arResult['edost']['format'])) unset($arResult['edost']['format']);


		// javascript - офисы (стандартный шаблон)
		if ($config['template'] != 'Y' && $address_id != -1) $arResult['edost']['javascript'] .= '
		<input type="hidden" value="" id="edost_office" name="edost_office">
		<script type="text/javascript">
			function edost_OpenMap(n) {
				var E = document.getElementById("edost_office_" + n);
				if (E) window.open("http://www.edost.ru/office.php?c=" + E.value, "_blank");
			}

			function edost_SetOffice(n) {
				var E = document.getElementById("edost_office_" + n);
				if (E) {
					var E2 = document.getElementById("edost_office");
					if (E2) E2.value = E.value;
					if (document.getElementById("ID_DELIVERY_edost_" + n).checked) submitForm();
				}
			}
		</script>';


		// javascript - PickPoint (стандартный шаблон)
		if ($config['template'] != 'Y' && $address_id != -1 && $config['map'] == 'Y') $arResult['edost']['javascript'] .= '
		<input type="hidden" value="" id="edost_submit_active">
		<script type="text/javascript">
			function edost_SubmitActive(n) {
				var E = document.getElementById("edost_submit_active");
				if (E) {
					if (n == "set") E.value = "Y";
					else return (E.value == "Y" ? true : false);
				}
			}

			function EdostPickPoint(rz) {
				if (edost_SubmitActive("get")) return false;

				var s = (rz[\'name\'].substr(0, 3) == "'.$sign['postamat']['pvz'].'" ? "'.$sign['office'].'" : "'.$sign['postamat']['name'].'") + " PickPoint: ";

				var i = rz[\'address\'].indexOf("'.$sign['postamat']['rf'].'");
				if (i > 0) rz[\'address\'] = rz[\'address\'].substr(i + 22);
				var s2 = rz[\'name\'];
				var i = s2.indexOf(":");
				if (i > 0) s2 = s2.substr(i + 1).replace(/^\s+/g, "");
				s2 = s2.trim();
				if (s2 != "") rz[\'address\'] += " (" + s2 + ")";

				rz[\'id\'] = ", '.$sign['code'].': " + rz[\'id\'];

				document.getElementById(document.getElementById("address_input").value).'.($address_type == 'TEXTAREA' ? 'innerHTML' : 'value').' = s + rz[\'address\'] + rz[\'id\'];

				var E = document.getElementById("EdostPickPointRef");
				if (E) E.innerHTML = "'.$sign['loading'].'";

				var E = document.getElementById("edost_office");
				if (E) E.value = "pickpoint";

				var E = document.getElementById("ID_DELIVERY_edost_57");
				if (E && !E.checked) E.checked = true;

		        submitForm();
			}
		</script>';


		// javascript - блокировка поля ADDRESS, если выбран тариф с офисом
		if ($address_id != -1) $arResult['edost']['javascript'] .= '
		<script language=javascript>
			var E = document.getElementById(document.getElementById("address_input").value);
			if (E) {'.
				(!empty($address_readonly) ? '
				E.readOnly = true; E.style.color = "#707070"; E.style.backgroundColor = "#E0E0E0";' : '
				E.readOnly = false; E.style.color = "#000000"; E.style.backgroundColor = "#FFFFFF";').'
			}
		</script>';

	}

}
?>