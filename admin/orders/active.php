<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/OrdersBean.php");
include_once("class/responders/OrderStatusRequestHandler.php");
include_once("responders/DeleteItemResponder.php");
include_once("class/utils/OrdersSQL.php");

$page = new AdminPage();

$bean = new OrdersBean();

$h_send = new OrderStatusRequestResponder();

$h_delete = new DeleteItemResponder($bean);


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
