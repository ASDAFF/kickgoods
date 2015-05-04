<?php
namespace Bitrix\Catalog;

use Bitrix\Main;
use Bitrix\Main\Type;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class DiscountTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> XML_ID string(255) optional
 * <li> SITE_ID string(2) mandatory
 * <li> TYPE int mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> ACTIVE_FROM datetime optional
 * <li> ACTIVE_TO datetime optional
 * <li> RENEWAL bool optional default 'N'
 * <li> NAME string(255) optional
 * <li> MAX_USES int mandatory
 * <li> COUNT_USES int mandatory
 * <li> COUPON string(20) optional
 * <li> SORT int optional default 100
 * <li> MAX_DISCOUNT double optional
 * <li> VALUE_TYPE string(1) mandatory default 'P'
 * <li> VALUE double mandatory default 0.0000
 * <li> CURRENCY string(3) mandatory
 * <li> MIN_ORDER_SUM double optional default 0.0000
 * <li> TIMESTAMP_X datetime mandatory default 'CURRENT_TIMESTAMP'
 * <li> COUNT_PERIOD string(1) mandatory default 'U'
 * <li> COUNT_SIZE int mandatory
 * <li> COUNT_TYPE bool optional default 'Y'
 * <li> COUNT_FROM datetime optional
 * <li> COUNT_TO datetime optional
 * <li> ACTION_SIZE int mandatory
 * <li> ACTION_TYPE bool optional default 'Y'
 * <li> MODIFIED_BY int optional
 * <li> DATE_CREATE datetime optional
 * <li> CREATED_BY int optional
 * <li> PRIORITY int optional default 1
 * <li> LAST_DISCOUNT bool optional default 'Y'
 * <li> VERSION int optional default 1
 * <li> NOTES string(255) optional
 * <li> CONDITIONS string optional
 * <li> UNPACK string optional
 * </ul>
 *
 * @package Bitrix\Catalog
 **/

class DiscountTable extends Main\Entity\DataManager
{
	const TYPE_DISCOUNT = 0;
	const TYPE_DISCOUNT_SAVE = 1;

	const VALUE_TYPE_PERCENT = 'P';
	const VALUE_TYPE_FIX = 'F';
	const VALUE_TYPE_SALE = 'S';

	const COUNT_PERIOD_TYPE_ALL = 'U';
	const COUNT_PERIOD_TYPE_INTERVAL = 'D';
	const COUNT_PERIOD_TYPE_PERIOD = 'P';

	const COUNT_TYPE_SIZE_DAY = 'D';
	const COUNT_TYPE_SIZE_MONTH ='M';
	const COUNT_TYPE_SIZE_YEAR = 'Y';

	const ACTION_PERIOD_TYPE_ALL = 'U';
	const ACTION_PERIOD_TYPE_INTERVAL = 'D';
	const ACTION_PERIOD_TYPE_PERIOD = 'P';

	const ACTION_TYPE_SIZE_DAY = 'D';
	const ACTION_TYPE_SIZE_MONTH ='M';
	const ACTION_TYPE_SIZE_YEAR = 'Y';

	const ACTUAL_VERSION = 2;
	const OLD_VERSION = 1;
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_catalog_discount';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID' => new Main\Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_ID_FIELD')
			)),
			'XML_ID' => new Main\Entity\StringField('XML_ID', array(
				'validation' => array(__CLASS__, 'validateXmlId'),
				'title' => Loc::getMessage('DISCOUNT_ENTITY_XML_ID_FIELD')
			)),
			'SITE_ID' => new Main\Entity\StringField('SITE_ID', array(
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('DISCOUNT_ENTITY_SITE_ID_FIELD')
			)),
			'TYPE' => new Main\Entity\IntegerField('TYPE', array(
				'required' => true,
				'default_value' => self::TYPE_DISCOUNT,
				'validation' => array(__CLASS__, 'validateType'),
				'title' => Loc::getMessage('DISCOUNT_ENTITY_TYPE_FIELD')
			)),
			'ACTIVE' => new Main\Entity\BooleanField('ACTIVE', array(
				'values' => array('N', 'Y'),
				'default_value' => 'Y',
				'title' => Loc::getMessage('DISCOUNT_ENTITY_ACTIVE_FIELD')
			)),
			'ACTIVE_FROM' => new Main\Entity\DatetimeField('ACTIVE_FROM', array(
				'default_value' => null,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_ACTIVE_FROM_FIELD')
			)),
			'ACTIVE_TO' => new Main\Entity\DatetimeField('ACTIVE_TO', array(
				'default_value' => null,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_ACTIVE_TO_FIELD')
			)),
			'RENEWAL' => new Main\Entity\BooleanField('RENEWAL', array(
				'values' => array('N', 'Y'),
				'default_value' => 'N',
				'title' => Loc::getMessage('DISCOUNT_ENTITY_RENEWAL_FIELD')
			)),
			'NAME' => new Main\Entity\StringField('NAME', array(
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('DISCOUNT_ENTITY_NAME_FIELD')
			)),
			'MAX_USES' => new Main\Entity\IntegerField('MAX_USES', array(
				'default_value' => 0,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_MAX_USES_FIELD')
			)),
			'COUNT_USES' => new Main\Entity\IntegerField('COUNT_USES', array(
				'default_value' => 0,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_COUNT_USES_FIELD')
			)),
			'COUPON' => new Main\Entity\StringField('COUPON', array(
				'validation' => array(__CLASS__, 'validateCoupon'),
				'title' => Loc::getMessage('DISCOUNT_ENTITY_COUPON_FIELD')
			)),
			'SORT' => new Main\Entity\IntegerField('SORT', array(
				'title' => Loc::getMessage('DISCOUNT_ENTITY_SORT_FIELD')
			)),
			'MAX_DISCOUNT' => new Main\Entity\FloatField('MAX_DISCOUNT', array(
				'title' => Loc::getMessage('DISCOUNT_ENTITY_MAX_DISCOUNT_FIELD')
			)),
			'VALUE_TYPE' => new Main\Entity\EnumField('VALUE_TYPE', array(
				'required' => true,
				'values' => array(self::VALUE_TYPE_PERCENT, self::VALUE_TYPE_FIX, self::VALUE_TYPE_SALE),
				'default_value' => self::VALUE_TYPE_PERCENT,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_VALUE_TYPE_FIELD')
			)),
			'VALUE' => new Main\Entity\FloatField('VALUE', array(
				'required' => true,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_VALUE_FIELD')
			)),
			'CURRENCY' => new Main\Entity\StringField('CURRENCY', array(
				'required' => true,
				'validation' => array(__CLASS__, 'validateCurrency'),
				'title' => Loc::getMessage('DISCOUNT_ENTITY_CURRENCY_FIELD')
			)),
			'MIN_ORDER_SUM' => new Main\Entity\FloatField('MIN_ORDER_SUM', array(
				'default_value' => 0,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_MIN_ORDER_SUM_FIELD')
			)),
			'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
				'required' => true,
				'default_value' => new Type\DateTime(),
				'title' => Loc::getMessage('DISCOUNT_ENTITY_TIMESTAMP_X_FIELD')
			)),
			'COUNT_PERIOD' => new Main\Entity\EnumField('COUNT_PERIOD', array(
				'values' => array(self::COUNT_PERIOD_TYPE_ALL, self::COUNT_PERIOD_TYPE_INTERVAL, self::COUNT_PERIOD_TYPE_PERIOD),
				'default_value' => self::COUNT_PERIOD_TYPE_ALL,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_COUNT_PERIOD_FIELD')
			)),
			'COUNT_SIZE' => new Main\Entity\IntegerField('COUNT_SIZE', array(
				'default_value' => 0,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_COUNT_SIZE_FIELD')
			)),
			'COUNT_TYPE' => new Main\Entity\EnumField('COUNT_TYPE', array(
				'values' => array(self::COUNT_TYPE_SIZE_DAY, self::COUNT_TYPE_SIZE_MONTH, self::COUNT_TYPE_SIZE_YEAR),
				'default_value' => self::COUNT_TYPE_SIZE_YEAR,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_COUNT_TYPE_FIELD')
			)),
			'COUNT_FROM' => new Main\Entity\DatetimeField('COUNT_FROM', array(
				'default_value' => null,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_COUNT_FROM_FIELD')
			)),
			'COUNT_TO' => new Main\Entity\DatetimeField('COUNT_TO', array(
				'default_value' => null,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_COUNT_TO_FIELD')
			)),
			'ACTION_SIZE' => new Main\Entity\IntegerField('ACTION_SIZE', array(
				'default_value' => 0,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_ACTION_SIZE_FIELD')
			)),
			'ACTION_TYPE' => new Main\Entity\EnumField('ACTION_TYPE', array(
				'values' => array(self::ACTION_TYPE_SIZE_DAY, self::ACTION_TYPE_SIZE_MONTH, self::ACTION_TYPE_SIZE_YEAR),
				'default_value' => self::ACTION_TYPE_SIZE_YEAR,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_ACTION_TYPE_FIELD')
			)),
			'MODIFIED_BY' => new Main\Entity\IntegerField('MODIFIED_BY', array(
				'title' => Loc::getMessage('DISCOUNT_ENTITY_MODIFIED_BY_FIELD')
			)),
			'DATE_CREATE' => new Main\Entity\DatetimeField('DATE_CREATE', array(
				'default_value' => null,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_DATE_CREATE_FIELD')
			)),
			'CREATED_BY' => new Main\Entity\IntegerField('CREATED_BY', array(
				'title' => Loc::getMessage('DISCOUNT_ENTITY_CREATED_BY_FIELD')
			)),
			'PRIORITY' => new Main\Entity\IntegerField('PRIORITY', array(
				'default_value' => 1,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_PRIORITY_FIELD')
			)),
			'LAST_DISCOUNT' => new Main\Entity\BooleanField('LAST_DISCOUNT', array(
				'values' => array('N', 'Y'),
				'default_value' => 'Y',
				'title' => Loc::getMessage('DISCOUNT_ENTITY_LAST_DISCOUNT_FIELD')
			)),
			'VERSION' => new Main\Entity\EnumField('VERSION', array(
				'values' => array(self::OLD_VERSION, self::ACTUAL_VERSION),
				'default_value' => self::ACTUAL_VERSION,
				'title' => Loc::getMessage('DISCOUNT_ENTITY_VERSION_FIELD')
			)),
			'NOTES' => new Main\Entity\StringField('NOTES', array(
				'validation' => array(__CLASS__, 'validateNotes'),
				'title' => Loc::getMessage('DISCOUNT_ENTITY_NOTES_FIELD')
			)),
			'CONDITIONS' => new Main\Entity\TextField('CONDITIONS', array(
				'title' => Loc::getMessage('DISCOUNT_ENTITY_CONDITIONS_FIELD')
			)),
			'CONDITIONS_LIST' => new Main\Entity\TextField('CONDITIONS_LIST', array(
				'serialized' => true,
				'column_name' => 'CONDITIONS',
				'title' => Loc::getMessage('DISCOUNT_ENTITY_CONDITIONS_LIST_FIELD')
			)),
			'UNPACK' => new Main\Entity\TextField('UNPACK', array(
				'title' => Loc::getMessage('DISCOUNT_ENTITY_UNPACK_FIELD')
			)),
			'CREATED_BY_USER' => new Main\Entity\ReferenceField(
				'CREATED_BY_USER',
				'Bitrix\Main\User',
				array('=this.CREATED_BY' => 'ref.ID')
			),
			'MODIFIED_BY_USER' => new Main\Entity\ReferenceField(
				'MODIFIED_BY_USER',
				'Bitrix\Main\User',
				array('=this.MODIFIED_BY' => 'ref.ID')
			)
		);
	}
	/**
	 * Returns validators for XML_ID field.
	 *
	 * @return array
	 */
	public static function validateXmlId()
	{
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}
	/**
	 * Returns validators for SITE_ID field.
	 *
	 * @return array
	 */
	public static function validateSiteId()
	{
		return array(
			new Main\Entity\Validator\Length(null, 2),
		);
	}
	/**
	 * Returns validators for TYPE field.
	 *
	 * @return array
	 */
	public static function validateType()
	{
		return array(
			array(__CLASS__, 'checkType')
		);
	}
	/**
	 * Returns validators for NAME field.
	 *
	 * @return array
	 */
	public static function validateName()
	{
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}
	/**
	 * Returns validators for COUPON field.
	 *
	 * @return array
	 */
	public static function validateCoupon()
	{
		return array(
			new Main\Entity\Validator\Length(null, 20),
		);
	}
	/**
	 * Returns validators for CURRENCY field.
	 *
	 * @return array
	 */
	public static function validateCurrency()
	{
		return array(
			new Main\Entity\Validator\Length(null, 3),
		);
	}
	/**
	 * Returns validators for NOTES field.
	 *
	 * @return array
	 */
	public static function validateNotes()
	{
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}

	/**
	 * Check TYPE field.
	 *
	 * @param int $value					Current field value.
	 * @param int|array $primary			Primary key.
	 * @param array $row					Current data.
	 * @param Main\Entity\Field $field		Field object.
	 * @return bool|string
	 */
	public static function checkType($value, $primary, array $row, Main\Entity\Field $field)
	{
		if (
			$value == self::TYPE_DISCOUNT
			|| $value == self::TYPE_DISCOUNT_SAVE
		)
		{
			return true;
		}
		return Loc::getMessage('DISCOUNT_ENTITY_VALIDATOR_TYPE');
	}
}