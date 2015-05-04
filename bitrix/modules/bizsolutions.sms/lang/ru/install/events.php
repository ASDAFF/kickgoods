<?
$MESS["EVENT_ORDER_ID"]="Код заказа";
$MESS["EVENT_ORDER_DATE"]="Дата заказа";
$MESS["EVENT_ORDER_STATUS"]="Статус заказа";
$MESS["EVENT_ORDER_PHONE"]="Телефон пользователя";
$MESS["EVENT_STATUS_DESCR"]="Описание статуса заказа";
$MESS["EVENT_STATUS_TEXT"]="Текст";
$MESS["EVENT_CHANGING_STATUS_TO"]="Изменение статуса заказа на";
$MESS["EVENT_STATUS_PHONE_SUBJ"]="Изменение статуса заказа N#ORDER_ID#";
$MESS["EVENT_STATUS_PHONE_BODY1"]="Новый статус заказа N#ORDER_ID#: ";
$MESS["EVENT_SALE_PHONE"] = "Телефон отдела продаж";

//techsupport
$MESS["EVENT_TICKET_NEW_FOR_TECHSUPPORT_NAME"] = "Новое обращение в техподдержку создано";
$MESS["EVENT_TICKET_NEW_FOR_TECHSUPPORT_DESC"]= "#ID# - номер обращения
#PHONE_TO# - телефон поддержки
#CRITICAL# - критичность
#DATE_TICKET# - дата обновления";
$MESS ['EVENT_TICKET_NEW_FOR_TECHSUPPORT_SUBJECT'] = "Новое обращение в техподдержку";
$MESS ['EVENT_TICKET_NEW_FOR_TECHSUPPORT_MESSAGE'] = "Информация об обращении:
Номер #ID#
Дата обновления #DATE_TICKET#
Критичность: #CRITICAL#";


$MESS["EVENT_TICKET_CHANGE_FOR_TECHSUPPORT_NAME"] = "Сообщение в техподержку изменено";
$MESS["EVENT_TICKET_CHANGE_FOR_TECHSUPPORT_DESC"]= "#ID# - номер обращения
#PHONE_TO# - телефон поддержки
#CRITICAL# - критичность
#DATE_TICKET# - дата добавления";
$MESS ['EVENT_TICKET_CHANGE_FOR_TECHSUPPORT_SUBJECT'] = "Сообщение в техподержку изменено";
$MESS ['EVENT_TICKET_CHANGE_FOR_TECHSUPPORT_MESSAGE'] = "Информация об обращении:
Номер #ID#
Дата добавления #DATE_TICKET#
Критичность: #CRITICAL#
Изменения: #WHAT_CHANGE#";

// admin techsupport
$MESS["EVENT_ADMIN_TICKET_NEW_FOR_TECHSUPPORT_NAME"] = "Новое обращение в техподдержку создано";
$MESS["EVENT_ADMIN_TICKET_NEW_FOR_TECHSUPPORT_DESC"]= "#ID# - номер обращения
#PHONE_TO# - телефон поддержки
#CRITICAL# - критичность
#DATE_TICKET# - дата обновления";
$MESS ['EVENT_ADMIN_TICKET_NEW_FOR_TECHSUPPORT_SUBJECT'] = "Новое обращение в техподдержку";
$MESS ['EVENT_ADMIN_TICKET_NEW_FOR_TECHSUPPORT_MESSAGE'] = "Информация об обращении:
Номер #ID#
Дата обновления #DATE_TICKET#
Критичность: #CRITICAL#";


$MESS["EVENT_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT_NAME"] = "Сообщение в техподержку изменено";
$MESS["EVENT_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT_DESC"]= "#ID# - номер обращения
#PHONE_TO# - телефон поддержки
#CRITICAL# - критичность
#DATE_TICKET# - дата добавления";
$MESS ['EVENT_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT_SUBJECT'] = "Сообщение в техподержку изменено";
$MESS ['EVENT_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT_MESSAGE'] = "Информация об обращении:
Номер #ID#
Дата добавления #DATE_TICKET#
Критичность: #CRITICAL#
Изменения: #WHAT_CHANGE#";

// subscribe
$MESS["EVENT_SUBSCRIBE_CONFIRM_NAME"] = "Подтверждение подписки";
$MESS["EVENT_SUBSCRIBE_CONFIRM_DESC"]= "#ID# - идентификатор подписки
#CONFIRM_CODE# - код подтверждения
#SUBSCR_SECTION# - раздел с страницей редактирования подписки
#USER_NAME# - имя подписчика
#DATE_SUBSCR# - дата добавления/изменения адреса";
$MESS ['EVENT_SUBSCRIBE_CONFIRM_SUBJECT'] = "Подтверждение подписки";
$MESS ['EVENT_SUBSCRIBE_CONFIRM_MESSAGE'] = "Информация о подписке:
Дата добавления/изменения #DATE_SUBSCR#
Код подтверждения: #CONFIRM_CODE#";

// Admin support
$MESS["EVENT_ADMIN_SUBSCRIBE_CONFIRM_NAME"] = "Подтверждение подписки";
$MESS["EVENT_ADMIN_SUBSCRIBE_CONFIRM_DESC"]= "#ID# - идентификатор подписки
#CONFIRM_CODE# - код подтверждения
#SUBSCR_SECTION# - раздел с страницей редактирования подписки
#USER_NAME# - имя подписчика
#DATE_SUBSCR# - дата добавления/изменения адреса";
$MESS ['EVENT_ADMIN_SUBSCRIBE_CONFIRM_SUBJECT'] = "Подтверждение подписки";
$MESS ['EVENT_ADMIN_SUBSCRIBE_CONFIRM_MESSAGE'] = "Информация о подписке:
Дата добавления/изменения #DATE_SUBSCR#
Код подтверждения: #CONFIRM_CODE#";


// Sale
$MESS["EVENT_SALE_NEW_ORDER_NAME"] = "Новый заказ";
$MESS["EVENT_SALE_NEW_ORDER_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#ORDER_USER# - заказчик
#PRICE# - сумма заказа
#PHONE_TO# - телефон заказчика
#ORDER_LIST# - состав заказа
#SALE_PHONE# - телефон отдела продаж";
$MESS["EVENT_SALE_NEW_ORDER_SUBJECT"] = "Новый заказ N#ORDER_ID#";
$MESS["EVENT_SALE_NEW_ORDER_MESSAGE"] = "Ваш заказ N#ORDER_ID# принят. Стоимость: #PRICE# Состав заказа: #ORDER_LIST#";

$MESS["EVENT_SALE_ORDER_CANCEL_NAME"] = "Отмена заказа";
$MESS["EVENT_SALE_ORDER_CANCEL_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#PHONE_TO# - телефон заказчика
#ORDER_CANCEL_DESCRIPTION# - причина отмены
#SALE_PHONE# - телефон отдела продаж";
$MESS["EVENT_SALE_ORDER_CANCEL_SUBJECT"] = "Отмена заказа N#ORDER_ID#";
$MESS["EVENT_SALE_ORDER_CANCEL_MESSAGE"] = "Заказ N#ORDER_ID# отменен
#ORDER_CANCEL_DESCRIPTION#";

$MESS["EVENT_SALE_ORDER_PAID_NAME"] = "Заказ оплачен";
$MESS["EVENT_SALE_ORDER_PAID_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#PHONE_TO# - телефон заказчика
#SALE_PHONE# - телефон отдела продаж";
$MESS["EVENT_SALE_ORDER_PAID_SUBJECT"] = "Заказ N#ORDER_ID# оплачен";
$MESS["EVENT_SALE_ORDER_PAID_MESSAGE"] = "Заказ N#ORDER_ID# оплачен";

$MESS["EVENT_SALE_ORDER_DELIVERY_NAME"] = "Доставка заказа разрешена";
$MESS["EVENT_SALE_ORDER_DELIVERY_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#PHONE_TO# - телефон заказчика
#SALE_PHONE# - телефон отдела продаж";
$MESS["EVENT_SALE_ORDER_DELIVERY_SUBJECT"] = "Доставка заказа N#ORDER_ID# разрешена";
$MESS["EVENT_SALE_ORDER_DELIVERY_MESSAGE"] = "Доставка заказа N#ORDER_ID#  разрешена";

// Admin sale
$MESS["EVENT_ADMIN_SALE_NEW_ORDER_NAME"] = "Новый заказ";
$MESS["EVENT_ADMIN_SALE_NEW_ORDER_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#ORDER_USER# - заказчик
#PRICE# - сумма заказа
#USER_PHONE# - телефон заказчика
#ORDER_LIST# - состав заказа
#SALE_PHONE# - телефон отдела продаж";
$MESS["EVENT_ADMIN_SALE_NEW_ORDER_SUBJECT"] = "Новый заказ N#ORDER_ID#";
$MESS["EVENT_ADMIN_SALE_NEW_ORDER_MESSAGE"] = "Создан новый заказ. Пользователь: #ORDER_USER#, телефон: #USER_PHONE#, заказ N#ORDER_ID# Стоимость: #PRICE#. Состав заказа: #ORDER_LIST#";

$MESS["EVENT_ADMIN_SALE_ORDER_CANCEL_NAME"] = "Отмена заказа";
$MESS["EVENT_ADMIN_SALE_ORDER_CANCEL_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#PHONE_TO# - телефон заказчика
#ORDER_CANCEL_DESCRIPTION# - причина отмены
#SALE_PHONE# - телефон отдела продаж";
$MESS["EVENT_ADMIN_SALE_ORDER_CANCEL_SUBJECT"] = "Отмена заказа N#ORDER_ID#";
$MESS["EVENT_ADMIN_SALE_ORDER_CANCEL_MESSAGE"] = "Заказ N#ORDER_ID# отменен
#ORDER_CANCEL_DESCRIPTION#";

$MESS["EVENT_ADMIN_SALE_ORDER_PAID_NAME"] = "Заказ оплачен";
$MESS["EVENT_ADMIN_SALE_ORDER_PAID_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#PHONE_TO# - телефон заказчика
#SALE_PHONE# - телефон отдела продаж";
$MESS["EVENT_ADMIN_SALE_ORDER_PAID_SUBJECT"] = "Заказ N#ORDER_ID# оплачен";
$MESS["EVENT_ADMIN_SALE_ORDER_PAID_MESSAGE"] = "Заказ N#ORDER_ID# оплачен";

$MESS["EVENT_ADMIN_SALE_ORDER_DELIVERY_NAME"] = "Доставка заказа разрешена";
$MESS["EVENT_ADMIN_SALE_ORDER_DELIVERY_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#PHONE_TO# - телефон заказчика
#SALE_PHONE# - телефон отдела продаж";
$MESS["EVENT_ADMIN_SALE_ORDER_DELIVERY_SUBJECT"] = "Доставка заказа N#ORDER_ID# разрешена";
$MESS["EVENT_ADMIN_SALE_ORDER_DELIVERY_MESSAGE"] = "Доставка заказа N#ORDER_ID#  разрешена";

// TASKS

$MESS["EVENT_TASKS_TASK_ADD_NAME"] = "Отправлять ответственному при добавленнии новой задачи";
$MESS["EVENT_TASKS_TASK_ADD_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_ADD_SUBJECT"] = "Отправлять ответственному при добавленнии новой задачи";
$MESS["EVENT_TASKS_TASK_ADD_MESSAGE"] = "Для Вас добавлена новая задача #TITLE# от #CREATED_BY# крайний срок: #DEADLINE#";

$MESS["EVENT_TASKS_TASK_UPDATE_TITLE_NAME"] = "Отправлять ответсвенному при редактировании задачи (изменение названия, описания и т.д.)";
$MESS["EVENT_TASKS_TASK_UPDATE_TITLE_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_UPDATE_TITLE_SUBJECT"] = "Отправлять ответсвенному при редактировании задачи (изменение названия, описания и т.д.)";
$MESS["EVENT_TASKS_TASK_UPDATE_TITLE_MESSAGE"] = "Ваша задача #TITLE# отредактирована";

$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_3_NAME"] = "Отправлять постановщику при изменении статуса на 'Выполняется'";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_3_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_3_SUBJECT"] = "Отправлять постановщику при изменении статуса на 'Выполняется'";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_3_MESSAGE"] = "#RESPONSIBLE# изменил статус задачи #TITLE# на 'Выполняется'";

$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_4_NAME"] = "Отправлять постановщику при изменении статуса на 'Завершена'";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_4_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_4_SUBJECT"] = "Отправлять постановщику при изменении статуса на 'Завершена'";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_4_MESSAGE"] = "#RESPONSIBLE# изменил статус задачи #TITLE# на 'Завершена'";

$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_5_NAME"] = "Отправлять ответственному при изменении статуса на 'Принята постановщиком'";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_5_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_5_SUBJECT"] = "Отправлять ответственному при изменении статуса на 'Принята постановщиком'";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_5_MESSAGE"] = "Результат задачи #TITLE# принят постановщиком #CREATED_BY#";

$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_2_NAME"] = "Отправлять ответственному при изменении статуса на 'Требует доработки'";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_2_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_2_SUBJECT"] = "Отправлять ответственному при изменении статуса на 'Требует доработки'";
$MESS["EVENT_TASKS_TASK_UPDATE_STATUS_2_MESSAGE"] = "Задача #TITLE# не принята постановщиком #CREATED_BY# и требует доработки";

$MESS["EVENT_TASKS_TASK_UPDATE_DEADLINE_NAME"] = "Отправлять ответственному при изменения крайнего срока";
$MESS["EVENT_TASKS_TASK_UPDATE_DEADLINE_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_UPDATE_DEADLINE_SUBJECT"] = "Отправлять ответственному при изменения крайнего срока";
$MESS["EVENT_TASKS_TASK_UPDATE_DEADLINE_MESSAGE"] = "У задачи #TITLE# изменился крайний срок на #DEADLINE#";

$MESS["EVENT_TASKS_TASK_UPDATE_PRIORITY_NAME"] = "Отправлять ответственному при изменении приоритета";
$MESS["EVENT_TASKS_TASK_UPDATE_PRIORITY_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_UPDATE_PRIORITY_SUBJECT"] = "Отправлять ответственному при изменении приоритета";
$MESS["EVENT_TASKS_TASK_UPDATE_PRIORITY_MESSAGE"] = "У задачи #TITLE# изменился приоритет на: #PRIORITY#";

$MESS["EVENT_TASKS_TASK_UPDATE_RESPONSIBLE_ID_NAME"] = "Отправлять новому ответственному при изменении ответственного";
$MESS["EVENT_TASKS_TASK_UPDATE_RESPONSIBLE_ID_DESC"] = "#TITLE# - Название задачи
#CREATED_BY# - Постановщик
#RESPONSIBLE# - Ответственный
#DEADLINE# - Крайний срок
#PRIORITY# - Приоритет";
$MESS["EVENT_TASKS_TASK_UPDATE_RESPONSIBLE_ID_SUBJECT"] = "Отправлять новому ответственному при изменении ответственного";
$MESS["EVENT_TASKS_TASK_UPDATE_RESPONSIBLE_ID_MESSAGE"] = "На Вас назначена задача #TITLE# от #CREATED_BY# крайний срок: #DEADLINE#";

?>