<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/ProductCategoryInputForm.php");
include_once("class/beans/ProductCategoriesBean.php");


$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$view = new BeanFormEditor(new ProductCategoriesBean(), new ProductCategoryInputForm());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();

?>
