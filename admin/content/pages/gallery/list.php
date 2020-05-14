<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("beans/DynamicPagePhotosBean.php");
include_once("beans/DynamicPagesBean.php");

include_once("components/GalleryView.php");

$rc = new RequestBeanKey(new DynamicPagesBean(), "../list.php");

$menu = array(
);

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);
$page->setCaption("Dynamic Page Photo Gallery");
$page->setAccessibleTitle("Photo Gallery");

$action_add = new Action("", "add.php" . $rc->getQuery(), array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Photo");
$page->addAction($action_add);

$bean = new DynamicPagePhotosBean();
$bean->select()->where = $rc->getURLParameter()->text(TRUE);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$gv = new GalleryView($bean);
$gv->getActionsCollection()->addURLParameter($rc->getURLParameter());

$page->startRender($menu);



$gv->render();

$page->finishRender();

?>