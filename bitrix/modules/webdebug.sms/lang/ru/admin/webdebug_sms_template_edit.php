<?
$MESS['WEBDEBUG_SMS_APPLICATION_TITLE_EDIT'] = 'Редактирование SMS-шаблона';
$MESS['WEBDEBUG_SMS_APPLICATION_TITLE_ADD'] = 'Добавление SMS-шаблона';

$MESS['WEBDEBUG_SMS_TOOLBAR_LIST_NAME'] = 'Вернуться в список';
	$MESS['WEBDEBUG_SMS_TOOLBAR_LIST_DESC'] = 'Вернуться к списку SMS-шаблонов';
$MESS['WEBDEBUG_SMS_TOOLBAR_ADD_NAME'] = 'Добавить';
	$MESS['WEBDEBUG_SMS_TOOLBAR_ADD_DESC'] = 'Перейти к добавлению нового SMS-шаблона';
$MESS['WEBDEBUG_SMS_TOOLBAR_COPY_NAME'] = 'Копировать';
	$MESS['WEBDEBUG_SMS_TOOLBAR_COPY_DESC'] = 'Копировать текущий SMS-шаблон';
$MESS['WEBDEBUG_SMS_TOOLBAR_DELETE_NAME'] = 'Удалить';
	$MESS['WEBDEBUG_SMS_TOOLBAR_DELETE_DESC'] = 'Копировать текущий SMS-шаблон';
	$MESS['WEBDEBUG_SMS_TOOLBAR_DELETE_NAME_CONFIRM'] = 'Удалить текущий SMS-шаблон?';

$MESS['WEBDEBUG_SMS_TAB_GENERAL_NAME'] = 'Параметры';
	$MESS['WEBDEBUG_SMS_TAB_GENERAL_DESC'] = 'Параметры SMS-шаблона';

$MESS['WEBDEBUG_SMS_FIELD_ACTIVE'] = 'Активность';
$MESS['WEBDEBUG_SMS_FIELD_SITE_ID'] = 'Привязка к сайту';
$MESS['WEBDEBUG_SMS_FIELD_NAME'] = 'Название шаблона';
	$MESS['WEBDEBUG_SMS_FIELD_NAME_DEFAULT'] = 'SMS-шаблон';
$MESS['WEBDEBUG_SMS_FIELD_SORT'] = 'Сортировка';
$MESS['WEBDEBUG_SMS_FIELD_DESCRIPTION'] = 'Описание шаблона';
	$MESS['WEBDEBUG_SMS_FIELD_DESCRIPTION_ADDITIONAL'] = '<br/><br/><b>Данные о пользователе</b><br/>#X_USER_ID# - ID пользователя<br/>#X_USER_LOGIN# - Логин пользователя<br/>#X_USER_NAME# - Имя пользователя<br/>#X_USER_LAST_NAME# - Фамилия пользователя<br/>#X_USER_SECOND_NAME# - Отчество пользователя<br/>#X_USER_EMAIL# - E-mail пользователя<br/>#X_USER_PHONE# - Телефон пользователя (не указанный как мобильный)<br/>#X_USER_MOBILE# - Моб. телефон пользователя (из профиля пользователя)<br/>#X_PHONE# - телефон пользователя (или мобильный из профиля, или телефон из заказа, или обычный из профиля)<br/><br/><b>Стандарнтые поля</b><br/>#DEFAULT_PHONE# - Номер получателя по умолчанию (устанавливается в настройках SMS-модуля)<br/>#DEFAULT_EMAIL_FROM# - E-mail администратора сайта (устанавливается в настройках)<br/>#SITE_NAME# - Название сайта (устанавливается в настройках)<br/>#SERVER_NAME# - URL сервера (устанавливается в настройках)';
	$MESS['WEBDEBUG_SMS_FIELD_DESCRIPTION_ORDER_PARAMETERS'] = '<br/><br/><b>Данные заказа (только для событий типа SALE_*)</b><br/>#X_ORDER_SUMM# - сумма заказа<br/>#X_ORDER_DATE# - дата заказа<br/>#X_ORDER_COMMENTS# - комментарии к заказу';
	$MESS['WEBDEBUG_SMS_FIELD_DESCRIPTION_ORDER_PROPERTIES'] = '<br/><br/><b>Свойства заказа (только для событий типа SALE_*)</b>';
		$MESS['WEBDEBUG_SMS_FIELD_DESCRIPTION_ORDER_PROPERTIES_NO'] = '--- свойств заказа нет ---';
$MESS['WEBDEBUG_SMS_FIELD_TEMPLATE'] = 'Шаблон';
	$MESS['WEBDEBUG_SMS_FIELD_TEMPLATE_DEFAULT'] = 'Текст SMS сообщения

--
#SERVER_NAME#';
$MESS['WEBDEBUG_SMS_FIELD_RECEIVER'] = 'Кому';
$MESS['WEBDEBUG_SMS_FIELD_PHONE_FROM_EMAIL'] = 'Пробовать определять номер телефона из e-mail';
$MESS['WEBDEBUG_SMS_FIELD_EMAIL_FIELD'] = 'Поле, содержащее e-mail';
	$MESS['WEBDEBUG_SMS_FIELD_EMAIL_FIELD_EMPTY'] = '--- не выбрано (или отсутствует в почтовом шаблоне) ---';
$MESS['WEBDEBUG_SMS_FIELD_EVENT'] = 'Почтовое событие';
	$MESS['WEBDEBUG_SMS_FIELD_EVENT_SELECT'] = '--- выберите тип почтового события ---';
	$MESS['WEBDEBUG_SMS_FIELD_EVENT_EMPTY'] = '--- типы почтовых событий не заданы в системе ---';
$MESS['WEBDEBUG_SMS_FIELD_STOP'] = 'Отменить отправку сообщения на e-mail';

$MESS['WEBDEBUG_SMS_EVENT_FIELDS'] = 'Доступные поля (вставляются на месте курсора)';

$MESS['WEBDEBUG_SMS_ERROR_TEMPLATE_NOT_FOUND'] = 'Указанный SMS-шаблон не найден!';
?>