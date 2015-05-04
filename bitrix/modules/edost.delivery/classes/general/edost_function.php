<?
/*********************************************************************************
���������������� ������� ������ eDost (��� ���������� ������ ���� �� ��������������)

��� ����������� � ����� 'edost_const.php' ������ ���� ����������� ���������:
define('DELIVERY_EDOST_FUNCTION', 'Y');
*********************************************************************************/

class edost_function {

	// ���������� ����� �������� ��������
	public static function BeforeCalculate(&$order, &$config) {
/*
		$order - ������������ ������ �������� � ����������� �������
		$config - ��������� ������

		return false; // ���������� ���������� �������
		return array('hide' => true); // ��������� ������ (�� ������������ ������ �� ������, �� ��������� ������)
		return array('data' => array( ������ �������� )); // �������� ������ � �������� ��������� �������� 'data' (������ ������ ��������������� ��������� eDost)
*/

//		echo '<br><b>BeforeCalculate - arOrder:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';


//		echo '<br>SERVER[REQUEST_URI]:'.$_SERVER['REQUEST_URI'];
//		$_SESSION['EDOST']['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
//		unset($_SESSION['EDOST']['office_default']); // �������� ��������� �� ����� �����
//		$order['LOCATION_TO'] = 10000000000;

/*
		// ������� ����������� ����� ��� ��������� �������������� (������ ��������� �������)
		$ar = array(5979, 5980); // ID ��������������
		if (in_array($order['LOCATION_TO'], $ar)) {
			$order['location'] = CDeliveryEDOST::GetEdostLocation($order['LOCATION_TO']);
			if ($order['location'] === false) return false;

			return array(
				'sizetocm' => '1', // ����������� ��������� ��������� �������� � ����������
				'data' => array(
					9 => array( // ����� "���� ��������"
						'id' => 5,
						'price' => 400,
						'priceinfo' => 0,
						'pricecash' => 500,
						'transfer' => 0,
						'day' => '3-4 ���',
						'insurance' => 0,
						'company' => '���� ��������',
						'name' => '�������-��������',
						'format' => 'door',
						'company_id' => 1,
						'city' => '',
						'profile' => 9,
						'sort' => 4,
					)
				)
			);
		}
*/

/*
		// �������� �� � ������ �� ������� eDost (��������, ����� � �������� ��������� �������� � ������ �������, � ��������� �������� ����� �������� � ����������� �� ��������������� ����������)
		$config['id'] = '12345';
		$config['ps'] = 'aaaaa';
*/

		// ��������� ������ �� �������� ���������� ������
//		if (strpos($_SERVER['REQUEST_URI'], '/personal/order/make') === 0) return array('hide' => true);

		// ��������� ������ � �������� ������
//		if (strpos($_SERVER['REQUEST_URI'], '/catalog') === 0 || strpos($_SERVER['REQUEST_URI'], '/bitrix/components/edost/catalogdelivery') === 0) return array('hide' => true);

/*
		// ��������� ������ ��� ��������� ��������������
		$ar = array(5979, 5980); // ID ��������������
		if (in_array($order['LOCATION_TO'], $ar)) return array('hide' => true);
*/

		return false;

	}

	// ���������� ����� ��������� ���������� ������ � ����� �������� �� ������ eDost
	public static function BeforeCalculateRequest(&$order, &$config) {
/*
		$order - ���������������� ������ �������� � ����������� �������
		$config - ��������� ������

		return false; // ���������� ���������� �������
		return array('hide' => true); // ��������� ������ (�� ������������ ������ �� ������, �� ��������� ������)
		return array('data' => array( ������ �������� )); // �������� ������ � �������� ��������� �������� 'data' (������ ������ ��������������� ��������� eDost)

		������ ������������ �� ����������:
			$order['LOCATION_TO'] - �� �������������� ��������
			$order['LOCATION_ZIP'] - �������� ������ (���� ������, ����� �� ���������� �� ������ �������)
			$order['WEIGHT'] - ��� ������ � �������
			$order['PRICE'] - ���� ������ � ������
			$order['size'] - ������ � ���������� ������ (������� ��������� ������ ��������� � ������������ � ������ �������� eDost)
				��������������: �� ������ �������� ������ ���� ������������� �� ����������� - ������: $order['size'] = array(30, 10, 20);  sort($order['size']);
*/

//		echo '<br><b>BeforeCalculateRequest - arOrder:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';


//		$order['size'] = array(10, 20, 30);
//		$order['LOCATION_TO'] = 1;
//		$order['WEIGHT'] = 500;
//		$order['WEIGHT'] += 32000;
//		$order['PRICE'] = 1000;

/*
		// ���������� �������������� ������� �� ��������� eDost (������ ������������ ������� �� ���� �������� $order['LOCATION_TO'])
		$order['location'] = array(
		    'country' => 0, // ��� ������ ��������� eDost (0 - ������)
		    'region' => 59, // ��� ������� ��������� eDost
		    'city' => '�����', // �������� ������ � ��������� win
		);
//		$order['LOCATION_TO'] = 100; // ���� 'LOCATION_TO' �� �������, ����� ��� ���������� ������ �����������, ����������� ������ ���� �������� ���������� ��� �������������� (����� ����)
*/

/*
		// �������� ��� �� �������� ��� ��������� ��������������
		$ar = array(5979, 5980); // ID ��������������
		if (in_array($order['LOCATION_TO'], $ar)) $order['WEIGHT'] += 1000;
*/

		return false;

	}

	// ���������� ����� ������� ��������
	public static function AfterCalculate($order, $config, &$result) {
/*
		$order - ���������������� ������ �������� � ����������� �������
		$config - ��������� ������
		$result - ��������� �������
*/

//		echo '<br><b>AfterCalculate - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterCalculate - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';

/*
		// ���������� ��������� �������� �� ����� (��� ����� � EMS - id: 1, 2, 3)
		if (!empty($result['data'])) foreach ($result['data'] as $k => $v)
			if (in_array($v['id'], array(1, 2, 3)) && $v['price'] > 0) {
				$result['data'][$k]['priceinfo'] = $v['price'];
				$result['data'][$k]['price'] = 0;
			}
*/

		// ������� �� ������� ����� "DPD - parcel - �� ������ ������" (��� 91)
//		if (isset($result['data']['91'])) unset($result['data']['91']);

/*
		// ��������� ��������� �������� ������ "PickPoint" (��� 57)
		if (isset($result['data']['57'])) {
			// ��������� ������������� ��������� �������� ��� ��������� ��������������
			$ar = array(5979, 5980); // ID ��������������
			if (in_array($order['LOCATION_TO'], $ar)) {
				$result['data']['57']['price'] = 250; // ��������� ��������
				$result['data']['57']['pricecash'] = 250; // ��������� �������� ��� ���������� ������� (-1 - ��������� ���������� ������)
			}

			// ���������� ������������ ��������� ��� ������� ������ � ����� 5
			$result['data']['57']['priceoffice'] = array(
				5 => array(
					'type' => 5,
					'price' => $result['data']['57']['price'] + 100, // ����������� ���� �������� + 100 ���.
					'priceinfo' => 0,
					'pricecash' => 800, // �������
				),
			);
		}
*/
	}


	// ���������� ����� �������� ������ �� ������� ������
	public static function AfterGetOffice($order, &$result) {
/*
		$order - ��������� ������
		$result - ������ ������
*/
//		echo '<br><b>AfterGetOffice - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterGetOffice - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';


		// ������� ������ ������ ������ '��������� 1' (��� 's1')
//		if (isset($result['data']['s1'])) unset($result['data']['s1']);

/*
		// ������� ����� ������ ��� ������ '��������� 1' (��� 's1')
		$result['data']['s1'] = array(
			'12345A12345' => array(
				'id' => '12345A12345',
				'code' => '',
				'name' => '�� �����',
				'address' => '������, ��. ��������� ������, �. 6, ����. 1',
				'address2' => '��. 5',
				'tel' => '+7-123-123-45-67',
				'schedule' => '� 10 �� 20, ��� ��������2222',
				'gps' => '37.592311,55.596037',
				'type' => 3,
				'metro' => '',
			),
		);
*/
	}

}
?>