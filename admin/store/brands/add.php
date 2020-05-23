<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/BrandInputForm.php");
include_once("class/beans/BrandsBean.php");



$page = new AdminPage();


$view = new BeanFormEditor(new BrandsBean(), new BrandInputForm());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();

?>
