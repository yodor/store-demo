<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/SectionInputForm.php");
include_once("class/beans/SectionsBean.php");


$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("sections.list"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back to Sections");
$page->addAction($action_back);

$view = new BeanFormEditor(new SectionsBean(), new SectionInputForm());

$view->processInput();

$page->startRender($menu);

$page->renderPageCaption();

$view->render();

$page->finishRender();


?>
