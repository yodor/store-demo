<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/ProductInventoryInputForm.php");
include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductsBean.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("products.inventory"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back To Inventory List");
$page->addAction($action_back);

Session::Set("sizing.list", $page->getPageURL());
Session::Set("product.color_scheme", $page->getPageURL());

$ensure_product = new RequestBeanKey(new ProductsBean(), "../list.php", array("product_name"));
$prodID = (int)$ensure_product->getID();

$form = new ProductInventoryInputForm();
$form->setProductID($prodID);

$bean = new ProductInventoryBean();

$copyID = -1;
if (isset($_GET["copyID"])) {
    $copyID = (int)$_GET["copyID"];

}

$view = new BeanFormEditor($bean, $form);
$view->reload_url = Session::Get("inventory.list");

// $view->getTransactor()->assignInsertValue("insert_date", DBConnections::get()->dateTime());
$view->getTransactor()->appendValue("prodID", $prodID);

$page->setCaption("Inventory: " . $ensure_product->getData("product_name"));

$view->processInput(); //redirect on successfully add or edit?

if ($copyID > 0) {
    $form->loadBeanData($copyID, $bean);
}

$page->startRender($menu);

$view->render();

$page->finishRender();

?>
