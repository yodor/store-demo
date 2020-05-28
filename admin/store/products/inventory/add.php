<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/ProductInventoryInputForm.php");
include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductsBean.php");


$page = new AdminPage();


Session::Set("sizing.list", $page->getPageURL());
Session::Set("product.color_scheme", $page->getPageURL());

$ensure_product = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));
$prodID = (int)$ensure_product->getID();

$form = new ProductInventoryInputForm();
$form->setProductID($prodID);

$bean = new ProductInventoryBean();

$copyID = -1;
if (isset($_GET["copyID"])) {
    $copyID = (int)$_GET["copyID"];
}

$view = new BeanFormEditor($bean, $form);
//$view->reload_url = Session::Get("inventory.list");

// $view->getTransactor()->assignInsertValue("insert_date", DBConnections::get()->dateTime());
$view->getTransactor()->appendValue("prodID", $prodID);

$page->setName("Inventory: " . $ensure_product->getData("product_name"));

$view->processInput(); //redirect on successfully add or edit?

if ($copyID > 0) {
    $form->loadBeanData($copyID, $bean);
}

$page->startRender();

$view->render();

$page->finishRender();

?>
