<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/BrandsBean.php");
include_once("components/TableView.php");


$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);



$bean = new BrandsBean();

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$view = new TableView($bean->query());
$view->setCaption("Brands List");
$view->setDefaultOrder(" brand_name ASC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));
$view->addColumn(new TableColumn("brand_name", "Name"));
$view->addColumn(new TableColumn("summary", "Summary"));
$view->addColumn(new TableColumn("url", "URL"));

$view->addColumn(new TableColumn("actions", "Actions"));

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());

$view->getColumn("actions")->setCellRenderer($act);

Session::Set("brands.list", $page->getPageURL());

$page->startRender();

$view->render();

$page->finishRender();

?>
