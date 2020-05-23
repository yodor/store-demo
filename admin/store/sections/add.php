<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/SectionInputForm.php");
include_once("class/beans/SectionsBean.php");


$page = new AdminPage();

$view = new BeanFormEditor(new SectionsBean(), new SectionInputForm());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();

?>
