<?php
include_once("session.php");
include_once("store/pages/AdminPage.php");
include_once("store/forms/RegisterClientInputForm.php");
include_once("beans/UsersBean.php");


$page = new AdminPage();

$view = new BeanFormEditor(new UsersBean(), new RegisterClientInputForm());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();
?>
