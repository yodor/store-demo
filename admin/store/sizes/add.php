<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/StoreSizeInputForm.php");
include_once("class/beans/StoreSizesBean.php");
include_once("class/beans/ProductsBean.php");

$menu = array();

$page = new AdminPage();


$view = new BeanFormEditor(new StoreSizesBean(), new StoreSizeInputForm());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();

?>
