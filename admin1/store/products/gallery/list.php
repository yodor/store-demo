<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductsBean.php");

include_once("components/GalleryView.php");

$menu = array();

$page = new AdminPage();

$rc = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));

$page->setName(tr("Product Gallery") . ": " . $rc->getData("product_name"));

$bean = new ProductPhotosBean();
$bean->select()->where()->addURLParameter($rc->getURLParameter());

$h_delete = new DeleteItemResponder($bean);

$h_repos = new ChangePositionResponder($bean);

$gv = new GalleryView($bean);

$gv->getItemActions()->addURLParameter($rc->getURLParameter());

Session::Set("products.gallery", $page->getPageURL());

$page->setPageMenu($menu);
$page->startRender();

$gv->render();

$page->finishRender();

?>
