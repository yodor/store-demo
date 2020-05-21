<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/OrdersBean.php");
include_once("class/responders/OrderStatusRequestHandler.php");
include_once("responders/DeleteItemResponder.php");
include_once("class/utils/OrdersSQL.php");

$page = new AdminPage();
$page->checkAccess(ROLE_ORDERS_MENU);

$bean = new OrdersBean();

$h_send = new OrderStatusRequestResponder();

$sel = new OrdersSQL();

$sel->where = " o.status='" . OrdersBean::STATUS_SENT . "' ";

include_once("list.php");

$act = $view->getColumn("actions")->getCellRenderer();
$act->getActions()->append(new Action("Потвърди завършване", "?cmd=order_status", array(new DataParameter("orderID"),
                                                                             new URLParameter("status", OrdersBean::STATUS_COMPLETED),))

);


$page->startRender();

$scomp->render();

$view->render();

$page->finishRender();
?>
