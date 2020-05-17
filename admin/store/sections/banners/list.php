<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/SectionBannersBean.php");
include_once("class/beans/SectionsBean.php");

include_once("components/GalleryView.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);
$page->setAccessibleTitle("Banners Gallery");

$action_back = new Action("", Session::Get("sections.list"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back to Sections");
$page->addAction($action_back);

$rc = new RequestBeanKey(new SectionsBean(), "../list.php", array("section_title"));

$action_add = new Action("", "add.php" . $rc->getQuery(), array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Photo");
$page->addAction($action_add);

$page->setName(tr("Banners Gallery") . ": " . $rc->getData("section_title"));

$bean = new SectionBannersBean();
$bean->select()->where = $rc->getURLParameter()->text(TRUE);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$gv = new GalleryView($bean);
$gv->viewActions()->addURLParameter($rc->getURLParameter());

Session::Set("section.banners.list", $page->getPageURL());

$page->startRender($menu);

$gv->render();

$page->finishRender();

?>
