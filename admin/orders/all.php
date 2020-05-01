<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/OrdersBean.php");
include_once("class/utils/OrdersQuery.php");

$page = new AdminPage();
$page->checkAccess(ROLE_ORDERS_MENU);

$bean = new OrdersBean();



$sel = new OrdersSQL();


include_once("list.php");

$menu = array();

$page->startRender($menu);

$page->renderPageCaption();

$scomp->render();


$view->render();


$page->finishRender();
?>
