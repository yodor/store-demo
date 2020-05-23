<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/ProductColorsBean.php");
include_once("class/beans/ProductColorPhotosBean.php");

include_once("forms/PhotoForm.php");

$rc = new BeanKeyCondition(new ProductColorsBean(), "../list.php");

$menu = array();

$page = new AdminPage();

$photos = new ProductColorPhotosBean();
$photos->select()->where = $rc->getURLParameter()->text(TRUE);

$view = new BeanFormEditor($photos, new PhotoForm());

$view->getTransactor()->appendURLParameter($rc->getURLParameter());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();
?>
