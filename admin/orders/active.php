<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/OrdersBean.php");
include_once("class/handlers/OrderStatusRequestHandler.php");
include_once("handlers/DeleteItemRequestHandler.php");
include_once("class/utils/OrdersSQL.php");

$page = new AdminPage();
$page->checkAccess(ROLE_ORDERS_MENU);

$bean = new OrdersBean();

$h_send = new OrderStatusRequestHandler();
RequestController::addRequestHandler($h_send);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$sel = new OrdersSQL();

$sel->where = " o.status='" . OrdersBean::STATUS_PROCESSING . "' ";

include_once("list.php");

$act = $view->getColumn("actions")->getCellRenderer();
$act->getActions()->append(new Action("Потвърди изпращане", "?cmd=order_status", array(new DataParameter("orderID"),
                                                                            new URLParameter("status", OrdersBean::STATUS_SENT),))

);
$act->getActions()->append(new RowSeparator());
$act->getActions()->append(new Action("Откажи изпращане", "?cmd=order_status", array(new DataParameter("orderID"),
                                                                          new URLParameter("status", OrdersBean::STATUS_CANCELED),))

);


$page->startRender();

$scomp->render();

$view->render();

$page->finishRender();
?>
