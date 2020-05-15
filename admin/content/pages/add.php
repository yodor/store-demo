<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("forms/DynamicPageForm.php");
include_once("beans/DynamicPagesBean.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$bean = new DynamicPagesBean();
$bean->debug_sql = FALSE;

$view = new BeanFormEditor($bean, new DynamicPageForm());

$view->processInput();

$page->startRender($menu);

$view->render();

$page->finishRender();

?>