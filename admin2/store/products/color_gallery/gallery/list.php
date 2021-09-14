<?php
include_once("session.php");
include_once("templates/admin/GalleryViewPage.php");

include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductColorsBean.php");
include_once("class/beans/ProductsBean.php");

$rc = new BeanKeyCondition(new ProductColorsBean(), "../list.php", array("color"));

$ensure_product = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));


$cmp = new GalleryViewPage();

$cmp->getPage()->setName(tr("Color Scheme") . ": " . $ensure_product->getData("product_name")." - ".tr("Color").": ".$rc->getData("color"));


$cmp->setRequestCondition($rc);


$bean = new ProductColorPhotosBean();
$cmp->setBean($bean);

$cmp->render();

?>
