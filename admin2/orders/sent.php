<?php
include_once("session.php");
include_once("class/components/OrdersListPage.php");


$page = new OrdersListPage();


$page->getOrderListSQL()->where()->add("status", "'" . OrdersBean::STATUS_SENT . "'");

$view = $page->initView();
$actions = $page->viewItemActions();


$actions->append(new Action("Потвърди завършване", "?cmd=order_status", array(new DataParameter("orderID"),
                                                                                        new URLParameter("status", OrdersBean::STATUS_COMPLETED),))

);


$page->render();
?>
