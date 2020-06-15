<?php
include_once("session.php");
include_once("templates/admin/GalleryViewPage.php");

include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductColorsBean.php");

$rc = new BeanKeyCondition(new ProductColorsBean(), "../list.php", array("color"));

$cmp = new GalleryViewPage();

$cmp->getPage()->setName(tr("Color Scheme Photos") . ": " . $rc->getData("color"));

$cmp->setRequestCondition($rc);


$bean = new ProductColorPhotosBean();
$cmp->setBean($bean);

$cmp->render();

?>
