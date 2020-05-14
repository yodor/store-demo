<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductPhotosBean.php");

include_once("forms/PhotoForm.php");

$rc = new RequestBeanKey(new ProductsBean(), "../list.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("products.gallery"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back To Gallery");
$page->addAction($action_back);

$photos = new ProductPhotosBean();
$photos->select()->where = $rc->getURLParameter()->text(TRUE);

$view = new BeanFormEditor($photos, new PhotoForm());

$view->getTransactor()->appendURLParameter($rc->getURLParameter());

$view->processInput();

$page->startRender($menu);

$view->render();

$page->finishRender();

?>
