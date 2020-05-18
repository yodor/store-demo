<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/RegisterClientInputForm.php");
include_once("beans/UsersBean.php");


$page = new AdminPage();
$page->checkAccess(ROLE_CLIENTS_MENU);

$view = new BeanFormEditor(new UsersBean(), new RegisterClientInputForm());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();
?>
