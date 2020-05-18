<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductColorsBean.php");

include_once("components/GalleryView.php");

$rc = new RequestBeanKey(new ProductColorsBean(), "../list.php" . queryString($_GET), array("color"));


$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);


$page->setAccessibleTitle("Color Scheme Photos");

$page->setName(tr("Color Scheme Photos") . ": " . $rc->getData("color"));

$bean = new ProductColorPhotosBean();
$bean->select()->where = $rc->getURLParameter()->text(TRUE);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$gv = new GalleryView($bean);
$gv->getItemActions()->addURLParameter($rc->getURLParameter());

Session::Set("color_scheme.photos", $page->getPageURL());

$page->startRender();

$gv->render();

$page->finishRender();

?>
