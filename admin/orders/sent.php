<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/OrdersBean.php");
include_once("class/handlers/ConfirmSendRequestHandler.php");
include_once("lib/handlers/DeleteItemRequestHandler.php");
include_once("class/utils/OrdersQuery.php");

$page = new AdminPage();
$page->checkAccess(ROLE_ORDERS_MENU);

$bean = new OrdersBean();

$h_send = new ConfirmSendRequestHandler();
RequestController::addRequestHandler($h_send);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);


$sel = new OrdersQuery();

$sel->where = " o.status='" . OrdersBean::STATUS_SENT . "' ";


include_once("list.php");

$menu = array();

$page->startRender($menu);
$page->renderPageCaption();

$scomp->render();

$view->render();

$page->finishRender();
?>
