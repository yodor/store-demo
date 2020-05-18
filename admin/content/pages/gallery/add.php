<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("beans/DynamicPagesBean.php");
include_once("beans/DynamicPagePhotosBean.php");

include_once("forms/PhotoForm.php");

$rc = new RequestBeanKey(new DynamicPagesBean(), "list.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$event_photos = new DynamicPagePhotosBean();
$event_photos->select()->where = $rc->getURLParameter()->text(TRUE);

$view = new BeanFormEditor($event_photos, new PhotoForm());

//current version of dynamic page photos table is set to DBROWS
$view->getForm()->getInput("photo")->getProcessor()->transact_mode = InputProcessor::TRANSACT_DBROW;

$view->getTransactor()->appendURLParameter($rc->getURLParameter());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();

?>