<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/SectionsBean.php");
include_once("class/beans/SectionBannersBean.php");

include_once("forms/PhotoForm.php");

$rc = new BeanKeyCondition(new SectionsBean(), "../list.php");

$menu = array();

$page = new AdminPage();

$photos = new SectionBannersBean();
$photos->select()->where()->addURLParameter($rc->getURLParameter());

$form = new PhotoForm();
$field = new DataInput("link", "Link", 0);
new TextField($field);
$form->addInput($field);
$view = new BeanFormEditor($photos, $form);

$view->getTransactor()->appendURLParameter($rc->getURLParameter());

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();

?>
