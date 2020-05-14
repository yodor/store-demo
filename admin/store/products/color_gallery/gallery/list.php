<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductColorsBean.php");

include_once("components/GalleryView.php");

$rc = new RequestBeanKey(new ProductColorsBean(), "../list.php" . queryString($_GET), array("color"));

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("product.color_scheme"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back");
$page->addAction($action_back);

$action_add = new Action("", "add.php" . $rc->getQuery(), array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Photo");
$page->addAction($action_add);


$page->setAccessibleTitle("Color Scheme Photos");

$page->setCaption(tr("Color Scheme Photos") . ": " . $rc->getData("color"));

$bean = new ProductColorPhotosBean();
$bean->select()->where = $rc->getURLParameter()->text(TRUE);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$gv = new GalleryView($bean);
$gv->getActionsCollection()->addURLParameter($rc->getURLParameter());

Session::Set("color_scheme.photos", $page->getPageURL());

$page->startRender($menu);



$gv->render();

$page->finishRender();

?>
