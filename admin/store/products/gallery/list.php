<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductsBean.php");

include_once("components/GalleryView.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);
$page->setAccessibleTitle("Photo Gallery");



$rc = new RequestBeanKey(new ProductsBean(), "../list.php", array("product_name"));



$page->setName(tr("Product Gallery") . ": " . $rc->getData("product_name"));

$bean = new ProductPhotosBean();
$bean->select()->where = $rc->getURLParameter()->text(TRUE);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$gv = new GalleryView($bean);

$gv->getItemActions()->addURLParameter($rc->getURLParameter());

Session::Set("products.gallery", $page->getPageURL());

$page->startRender($menu);

$gv->render();

$page->finishRender();

?>
