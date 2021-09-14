<?php
include_once("session.php");
include_once("class/components/OrdersListPage.php");


$page = new OrdersListPage();


$page->getOrderListSQL()->where()->add("status", "'" . OrdersBean::STATUS_CANCELED . "'");


$page->render();
?>
