<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/ProductColorInputForm.php");
include_once("class/beans/ProductColorsBean.php");
include_once("class/beans/ProductsBean.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("product.color_scheme"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back to Colors List");
$page->addAction($action_back);

$ensure_product = new RequestBeanKey(new ProductsBean(), "../list.php", array("product_name"));

$view = new BeanFormEditor(new ProductColorsBean(), new ProductColorInputForm($ensure_product->getID()));

Session::Set("color_codes.list", $page->getPageURL());

$view->getTransactor()->appendValue("prodID", $ensure_product->getID());

$page->setCaption("Color Scheme: " . $ensure_product->getData("product_name"));

$view->processInput();

$page->startRender($menu);

$view->render();

$page->finishRender();

?>
