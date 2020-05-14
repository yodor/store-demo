<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/SectionsBean.php");
include_once("class/beans/SectionBannersBean.php");

include_once("forms/PhotoForm.php");

$rc = new RequestBeanKey(new SectionsBean(), "../list.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("section.banners.list"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back To Banners");
$page->addAction($action_back);

$photos = new SectionBannersBean();
$photos->select()->where = $rc->getURLParameter()->text(TRUE);

$form = new PhotoForm();
$field = new DataInput("link", "Link", 0);
new TextField($field);
$form->addInput($field);
$view = new BeanFormEditor($photos, $form);

$view->getTransactor()->appendURLParameter($rc->getURLParameter());

$view->processInput();

$page->startRender($menu);

$view->render();

$page->finishRender();

?>
