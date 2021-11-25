<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("store/forms/ProductColorInputForm.php");
include_once("store/beans/ProductColorsBean.php");
include_once("store/beans/ProductsBean.php");

$ensure_product = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));

$cmp = new BeanEditorPage();
$cmp->getPage()->setName(tr("Edit Color Scheme") . ": " . $ensure_product->getData("product_name"));

$cmp->setRequestCondition($ensure_product);
$cmp->setBean(new ProductColorsBean());
$cmp->setForm(new ProductColorInputForm($ensure_product->getID()));

$cmp->render();

?>
