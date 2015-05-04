<?
$MESS["EVENT_ORDER_ID"]="Order code";
$MESS["EVENT_ORDER_DATE"]="Order date";
$MESS["EVENT_ORDER_STATUS"]="Order status";
$MESS["EVENT_ORDER_PHONE"]="User phone";
$MESS["EVENT_STATUS_DESCR"]="Order status description";
$MESS["EVENT_STATUS_TEXT"]="Text";
$MESS["EVENT_CHANGING_STATUS_TO"]="Change order status to";
$MESS["EVENT_STATUS_PHONE_SUBJ"]="Order status change N#ORDER_ID#";
$MESS["EVENT_STATUS_PHONE_BODY1"]="New status of order N#ORDER_ID#: ";
$MESS["EVENT_SALE_PHONE"] = "Sales department phone number";

//techsupport
$MESS["EVENT_TICKET_NEW_FOR_TECHSUPPORT_NAME"] = "New request for technical support has been created";
$MESS["EVENT_TICKET_NEW_FOR_TECHSUPPORT_DESC"]= "#ID# - technical support request ticket number
#PHONE_TO# - technical support phone number
#CRITICAL# - criticality
#DATE_TICKET# - date of update";
$MESS ['EVENT_TICKET_NEW_FOR_TECHSUPPORT_SUBJECT'] = "New request for technical support";
$MESS ['EVENT_TICKET_NEW_FOR_TECHSUPPORT_MESSAGE'] = "Information about technical support request:
Number #ID#
Date of update #DATE_TICKET#
Criticality: #CRITICAL#";


$MESS["EVENT_TICKET_CHANGE_FOR_TECHSUPPORT_NAME"] = "Request for technical support has been updated";
$MESS["EVENT_TICKET_CHANGE_FOR_TECHSUPPORT_DESC"]= "#ID# - request number
#PHONE_TO# - technical support phone number
#CRITICAL# - criticality
#DATE_TICKET# - date of request";
$MESS ['EVENT_TICKET_CHANGE_FOR_TECHSUPPORT_SUBJECT'] = "Request for technical support has been updated";
$MESS ['EVENT_TICKET_CHANGE_FOR_TECHSUPPORT_MESSAGE'] = "Information about technical support request:
Number #ID#
Date of request #DATE_TICKET#
Criticality: #CRITICAL#
Changes: #WHAT_CHANGE#";

// admin techsupport
$MESS["EVENT_ADMIN_TICKET_NEW_FOR_TECHSUPPORT_NAME"] = "New request for technical support has been created";
$MESS["EVENT_ADMIN_TICKET_NEW_FOR_TECHSUPPORT_DESC"]= "#ID# - request number
#PHONE_TO# - technical support phone number
#CRITICAL# - criticality
#DATE_TICKET# - date of update";
$MESS ['EVENT_ADMIN_TICKET_NEW_FOR_TECHSUPPORT_SUBJECT'] = "New request for technical support";
$MESS ['EVENT_ADMIN_TICKET_NEW_FOR_TECHSUPPORT_MESSAGE'] = "Information about technical support request:
Number #ID#
Date of update #DATE_TICKET#
Criticality: #CRITICAL#";


$MESS["EVENT_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT_NAME"] = "Request for technical support has been updated";
$MESS["EVENT_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT_DESC"]= "#ID# - request number
#PHONE_TO# - technical support phone number
#CRITICAL# - criticality
#DATE_TICKET# - date of request";
$MESS ['EVENT_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT_SUBJECT'] = "Request for technical support has been updated";
$MESS ['EVENT_ADMIN_TICKET_CHANGE_FOR_TECHSUPPORT_MESSAGE'] = "Information about technical support request:
Number #ID#
Date of request #DATE_TICKET#
Criticality: #CRITICAL#
Changes: #WHAT_CHANGE#";

// subscribe
$MESS["EVENT_SUBSCRIBE_CONFIRM_NAME"] = "Confirmation of subscription";
$MESS["EVENT_SUBSCRIBE_CONFIRM_DESC"]= "#ID# - subscription ID
#CONFIRM_CODE# - confirmation code
#SUBSCR_SECTION# - section with subscription edit page
#USER_NAME# - subscriber's name
#DATE_SUBSCR# - address add/edit date";
$MESS ['EVENT_SUBSCRIBE_CONFIRM_SUBJECT'] = "Confirmation of subscription";
$MESS ['EVENT_SUBSCRIBE_CONFIRM_MESSAGE'] = "Subscription information:
Add/edit date #DATE_SUBSCR#
Confirmation code: #CONFIRM_CODE#";

// Admin support
$MESS["EVENT_ADMIN_SUBSCRIBE_CONFIRM_NAME"] = "Confirmation of subscription";
$MESS["EVENT_ADMIN_SUBSCRIBE_CONFIRM_DESC"]= "#ID# - subscription ID
#CONFIRM_CODE# - confirmation code
#SUBSCR_SECTION# - section with subscription edit page
#USER_NAME# - subscriber's name
#DATE_SUBSCR# - address add/edit date";
$MESS ['EVENT_ADMIN_SUBSCRIBE_CONFIRM_SUBJECT'] = "Confirmation of subscription";
$MESS ['EVENT_ADMIN_SUBSCRIBE_CONFIRM_MESSAGE'] = "Subscription information:
Add/edit date #DATE_SUBSCR#
Confirmation code: #CONFIRM_CODE#";


// Sale
$MESS["EVENT_SALE_NEW_ORDER_NAME"] = "New order";
$MESS["EVENT_SALE_NEW_ORDER_DESC"] = "#ORDER_ID# - order code
#ORDER_DATE# - order date
#ORDER_USER# - client
#PRICE# - order price
#PHONE_TO# - client's phone
#ORDER_LIST# - order list
#SALE_PHONE# - sales department phone number";
$MESS["EVENT_SALE_NEW_ORDER_SUBJECT"] = "New order N#ORDER_ID#";
$MESS["EVENT_SALE_NEW_ORDER_MESSAGE"] = "Your order N#ORDER_ID# accepted. Price: #PRICE# Order list: #ORDER_LIST#";

$MESS["EVENT_SALE_ORDER_CANCEL_NAME"] = "Order cancel";
$MESS["EVENT_SALE_ORDER_CANCEL_DESC"] = "#ORDER_ID# - Order code
#ORDER_DATE# - order date
#PHONE_TO# - client's phone
#ORDER_CANCEL_DESCRIPTION# - reason of cancellation
#SALE_PHONE# - sales department phone number";
$MESS["EVENT_SALE_ORDER_CANCEL_SUBJECT"] = "Order cancel N#ORDER_ID#";
$MESS["EVENT_SALE_ORDER_CANCEL_MESSAGE"] = "Order N#ORDER_ID# cancelled
#ORDER_CANCEL_DESCRIPTION#";

$MESS["EVENT_SALE_ORDER_PAID_NAME"] = "Order paid";
$MESS["EVENT_SALE_ORDER_PAID_DESC"] = "#ORDER_ID# - order code
#ORDER_DATE# - order date
#PHONE_TO# - client's phone
#SALE_PHONE# - sales department phone number";
$MESS["EVENT_SALE_ORDER_PAID_SUBJECT"] = "Order N#ORDER_ID# paid";
$MESS["EVENT_SALE_ORDER_PAID_MESSAGE"] = "Order N#ORDER_ID# paid";

$MESS["EVENT_SALE_ORDER_DELIVERY_NAME"] = "Order delivery is allowed";
$MESS["EVENT_SALE_ORDER_DELIVERY_DESC"] = "#ORDER_ID# - order code
#ORDER_DATE# - order date
#PHONE_TO# - client's phone
#SALE_PHONE# - sales department phone number";
$MESS["EVENT_SALE_ORDER_DELIVERY_SUBJECT"] = "Order delivery N#ORDER_ID# is allowed";
$MESS["EVENT_SALE_ORDER_DELIVERY_MESSAGE"] = "Order delivery N#ORDER_ID#  is allowed";

// Admin sale
$MESS["EVENT_ADMIN_SALE_NEW_ORDER_NAME"] = "New order";
$MESS["EVENT_ADMIN_SALE_NEW_ORDER_DESC"] = "#ORDER_ID# - order code
#ORDER_DATE# - order date
#ORDER_USER# - client
#PRICE# - order price
#USER_PHONE# - client's phone
#ORDER_LIST# - order list
#SALE_PHONE# - sales department phone number";
$MESS["EVENT_ADMIN_SALE_NEW_ORDER_SUBJECT"] = "New order N#ORDER_ID#";
$MESS["EVENT_ADMIN_SALE_NEW_ORDER_MESSAGE"] = "New order created. User: #ORDER_USER#, phone: #USER_PHONE#, order N#ORDER_ID# Price: #PRICE#. Order list: #ORDER_LIST#";

$MESS["EVENT_ADMIN_SALE_ORDER_CANCEL_NAME"] = "Order cancellation";
$MESS["EVENT_ADMIN_SALE_ORDER_CANCEL_DESC"] = "#ORDER_ID# - order code
#ORDER_DATE# - order date
#PHONE_TO# - client's phone
#ORDER_CANCEL_DESCRIPTION# - reason of cancellation
#SALE_PHONE# - sales department phone number";
$MESS["EVENT_ADMIN_SALE_ORDER_CANCEL_SUBJECT"] = "Order cancellation N#ORDER_ID#";
$MESS["EVENT_ADMIN_SALE_ORDER_CANCEL_MESSAGE"] = "Order N#ORDER_ID# has been cancelled
#ORDER_CANCEL_DESCRIPTION#";

$MESS["EVENT_ADMIN_SALE_ORDER_PAID_NAME"] = "Order paid";
$MESS["EVENT_ADMIN_SALE_ORDER_PAID_DESC"] = "#ORDER_ID# - order code
#ORDER_DATE# - order date
#PHONE_TO# - client's phone
#SALE_PHONE# - sales department phone number";
$MESS["EVENT_ADMIN_SALE_ORDER_PAID_SUBJECT"] = "Order N#ORDER_ID# paid";
$MESS["EVENT_ADMIN_SALE_ORDER_PAID_MESSAGE"] = "Order N#ORDER_ID# paid";

$MESS["EVENT_ADMIN_SALE_ORDER_DELIVERY_NAME"] = "Order delivery is allowed";
$MESS["EVENT_ADMIN_SALE_ORDER_DELIVERY_DESC"] = "#ORDER_ID# - order code
#ORDER_DATE# - order date
#PHONE_TO# - client's phone
#SALE_PHONE# - sales department phone number";
$MESS["EVENT_ADMIN_SALE_ORDER_DELIVERY_SUBJECT"] = "Order delivery N#ORDER_ID# is allowed";
$MESS["EVENT_ADMIN_SALE_ORDER_DELIVERY_MESSAGE"] = "Order delivery N#ORDER_ID# is allowed";

// TASKS
$MESS["EVENT_TASKS_TASK_ADD_NAME"] = "Add task";
$MESS["EVENT_TASKS_TASK_ADD_DESC"] = "#TASK# - Task name";
$MESS["EVENT_TASKS_TASK_ADD_SUBJECT"] = "New task added";
$MESS["EVENT_TASKS_TASK_ADD_MESSAGE"] = "New task added #TASK#";

$MESS["EVENT_TASKS_TASK_UPDATE_NAME"] = "Task edit";
$MESS["EVENT_TASKS_TASK_UPDATE_DESC"] = "#TASK# - Task name";
$MESS["EVENT_TASKS_TASK_UPDATE_SUBJECT"] = "Task updated";
$MESS["EVENT_TASKS_TASK_UPDATE_MESSAGE"] = "Task #TASK# updated";

$MESS["EVENT_TASKS_TASK_DELETE_NAME"] = "Remove task";
$MESS["EVENT_TASKS_TASK_DELETE_DESC"] = "#TASK# - Task name";
$MESS["EVENT_TASKS_TASK_DELETE_SUBJECT"] = "Task removed";
$MESS["EVENT_TASKS_TASK_DELETE_MESSAGE"] = "Task removed #TASK#";

?>