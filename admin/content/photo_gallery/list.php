<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/GalleryPhotosBean.php");

include_once("components/GalleryView.php");


$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_add = new Action("", "add.php", array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Photo");
$page->addAction($action_add);

$bean = new GalleryPhotosBean();

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$gv = new GalleryView($bean);

$gv->setCaption("Sample Photo Gallery");



$page->startRender();

$gv->render();

$page->finishRender();

?>