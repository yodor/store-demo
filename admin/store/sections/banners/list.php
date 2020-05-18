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

$rc = new RequestBeanKey(new SectionsBean(), "../list.php", array("section_title"));

$page->setName(tr("Banners Gallery") . ": " . $rc->getData("section_title"));

$bean = new SectionBannersBean();
$bean->select()->where = $rc->getURLParameter()->text(TRUE);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$gv = new GalleryView($bean);
$gv->getItemActions()->addURLParameter($rc->getURLParameter());

$page->startRender();

$gv->render();

$page->finishRender();

?>
