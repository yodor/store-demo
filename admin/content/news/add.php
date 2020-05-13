<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/NewsItemInputForm.php");
include_once("class/beans/NewsItemsBean.php");


$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$view = new BeanFormEditor(new NewsItemsBean(), new NewsItemInputForm());

$view->processInput();

$page->startRender($menu);

$view->render();

$page->finishRender();


?>