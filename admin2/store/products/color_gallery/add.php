<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("store/forms/ProductColorInputForm.php");
include_once("store/beans/ProductColorsBean.php");
include_once("store/beans/ProductsBean.php");

$ensure_product = new BeanKeyCondition(new ProductsBean(), "list.php", array("product_name"));

$cmp = new BeanEditorPage();


$cmp->setRequestCondition($ensure_product);
$cmp->setBean(new ProductColorsBean());
$cmp->setForm(new ProductColorInputForm($ensure_product->getID()));

if (isset($_GET["editID"])) {
    $cmp->getPage()->setName(tr("Color Scheme") . ": " . $ensure_product->getData("product_name")." - ".tr("Edit"));
}
else {
    $cmp->getPage()->setName(tr("Color Scheme") . ": " . $ensure_product->getData("product_name")." - ".tr("Add"));
}

$cmp->render();

?>
