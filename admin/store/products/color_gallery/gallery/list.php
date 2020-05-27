<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductColorsBean.php");

include_once("components/GalleryView.php");

$rc = new BeanKeyCondition(new ProductColorsBean(), "../list.php" . queryString($_GET), array("color"));


$page = new AdminPage();


$page->setAccessibleTitle("Color Scheme Photos");

$page->setName(tr("Color Scheme Photos") . ": " . $rc->getData("color"));

$bean = new ProductColorPhotosBean();
$bean->select()->where()->addURLParameter($rc->getURLParameter());

$h_delete = new DeleteItemResponder($bean);

$h_repos = new ChangePositionResponder($bean);


$gv = new GalleryView($bean);
$gv->getItemActions()->addURLParameter($rc->getURLParameter());

Session::Set("color_scheme.photos", $page->getPageURL());

$page->startRender();

$gv->render();

$page->finishRender();

?>
