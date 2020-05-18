<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/StoreColorInputForm.php");
include_once("class/beans/StoreColorsBean.php");


$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);



$view = new BeanFormEditor(new StoreColorsBean(), new StoreColorInputForm());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();

?>
