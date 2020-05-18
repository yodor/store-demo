<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/ProductInputForm.php");
include_once("class/beans/ProductsBean.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);


$view = new BeanFormEditor(new ProductsBean(), new ProductInputForm());

//shortcuts for new ...
Session::Set("categories.list", $page->getPageURL());
Session::Set("brands.list", $page->getPageURL());
Session::Set("classes.list", $page->getPageURL());

$view->getTransactor()->assignInsertValue("insert_date", DBConnections::Get()->dateTime());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();

?>
