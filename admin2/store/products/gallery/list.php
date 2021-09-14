<?php
include_once("session.php");
include_once("templates/admin/GalleryViewPage.php");

include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductsBean.php");

//include_once("components/GalleryView.php");


$rc = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));

$cmp = new GalleryViewPage();

$cmp->getPage()->setName(tr("Галерия снимки") . ": " . $rc->getData("product_name"));


$cmp->setRequestCondition($rc);


$bean = new ProductPhotosBean();
$cmp->setBean($bean);

$cmp->render();





//$menu = array();
//
//$page = new AdminPage();
//
//$rc = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));
//
//$page->setName(tr("Product") . ": " . $rc->getData("product_name"));
//
//$bean = new ProductPhotosBean();
//$bean->select()->where()->addURLParameter($rc->getURLParameter());
//
//$h_delete = new DeleteItemResponder($bean);
//
//$h_repos = new ChangePositionResponder($bean);
//
//$gv = new GalleryView($bean);
//
//$gv->getItemActions()->addURLParameter($rc->getURLParameter());
//
//Session::Set("products.gallery", $page->getPageURL());
//
//$page->setPageMenu($menu);
//$page->startRender();
//
//$gv->render();
//
//$page->finishRender();

?>
