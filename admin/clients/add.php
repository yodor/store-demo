<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/RegisterClientInputForm.php");
include_once("beans/UsersBean.php");


$menu = array();


$page = new AdminPage();
$page->checkAccess(ROLE_CLIENTS_MENU);

$action_back = new Action("", Session::Get("clients.list"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back to Clients");
$page->addAction($action_back);

$view = new BeanFormEditor(new UsersBean(), new RegisterClientInputForm());

// $view->getForm()->getRenderer()->setAttribute("onSubmit", "return checkForm(this)");


$view->processInput();


$page->startRender($menu);

$page->renderPageCaption();

$view->render();

$page->finishRender();
?>
