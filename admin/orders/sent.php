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

// $h_delete = new DeleteItemRequestHandler($bean);
// RequestController::addRequestHandler($h_delete);


$sel = new OrdersSQL();

$sel->where = " o.status='".OrdersBean::STATUS_SENT."' ";


include_once("list.php");

$act = $view->getColumn("actions")->getCellRenderer();
$act->addAction(
    new Action(
        "Потвърди завършване", "?cmd=order_status",
        array(
            new ActionParameter("orderID", "orderID"),
            new URLParameter("status", OrdersBean::STATUS_COMPLETED),
        )
    )

);

$menu = array();

$page->startRender($menu);


$scomp->render();

$view->render();

$page->finishRender();
?>
