<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/OrdersBean.php");
include_once("class/handlers/ConfirmSendRequestHandler.php");
include_once("lib/handlers/DeleteItemRequestHandler.php");

$page = new AdminPage();

$bean = new OrdersBean();

$h_send = new ConfirmSendRequestHandler($bean);
RequestController::addRequestHandler($h_send);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);




$sel = new SelectQuery();
$sel->from = " orders o  ";

//select additionaly the items and client - allow search
$sel->fields = " *, (SELECT GROUP_CONCAT('-oi-', oi.product) FROM  order_items oi WHERE oi.orderID=o.orderID) as items, (SELECT CONCAT_WS('--', u.fullname, u.email, u.phone) FROM users u WHERE u.userID=o.userID) as client ";
$sel->where = " o.status='Initial' ";


include_once("list.php");

$menu = array();

$page->beginPage($menu);

$scomp->render();


$view->render();


$page->finishPage();
?>
