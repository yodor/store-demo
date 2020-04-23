<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/SectionBannersBean.php");
include_once("class/beans/SectionsBean.php");


include_once("lib/components/GalleryView.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);
$page->setAccessibleTitle("Banners Gallery");

$action_back = new Action("", Session::Get("sections.list"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back to Sections");
$page->addAction($action_back);

$rc = new ReferenceKeyPageChecker(new SectionsBean(), "../list.php");


$action_add = new Action("", "add.php?" . $rc->ref_key . "=" . $rc->ref_id, array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Photo");
$page->addAction($action_add);


$page->setCaption(tr("Banners Gallery") . ": " . $rc->ref_row["section_title"]);

$bean = new SectionBannersBean();
$bean->setFilter($rc->ref_key . "='" . $rc->ref_id . "'");


$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);


$gv = new GalleryView();
$gv->blob_field = "photo";

$gv->initView($bean, "add.php", $rc->ref_key, $rc->ref_id);

Session::Set("section.banners.list", $page->getPageURL());


$page->startRender($menu);
$page->renderPageCaption();

$gv->render();


$page->finishRender();


?>
