<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("store/beans/OrdersBean.php");
// include_once("class/responders/ConfirmSendRequestHandler.php");
include_once("responders/DeleteItemResponder.php");
include_once("store/utils/OrdersSQL.php");

$page = new AdminPage();

$bean = new OrdersBean();

$sel = new OrdersSQL();

$sel->where()->add("o.status", "'" . OrdersBean::STATUS_COMPLETED . "'");

include_once("list.php");

$menu = array();

$page->startRender();

$scomp->render();

$view->render();

$page->finishRender();
?>
