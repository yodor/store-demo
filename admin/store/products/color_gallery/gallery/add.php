<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/ProductColorsBean.php");
include_once("class/beans/ProductColorPhotosBean.php");

include_once("forms/PhotoForm.php");

$rc = new RequestBeanKey(new ProductColorsBean(), "../list.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("color_scheme.photos"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back to Color Scheme Photos");
$page->addAction($action_back);

$photos = new ProductColorPhotosBean();
$photos->select()->where = $rc->getURLParameter()->text(TRUE);

$view = new BeanFormEditor($photos, new PhotoForm());

$view->getTransactor()->appendURLParameter($rc->getURLParameter());

$view->processInput();

$page->startRender($menu);

$view->render();

$page->finishRender();
?>
