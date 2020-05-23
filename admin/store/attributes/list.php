<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/AttributesBean.php");
include_once("components/TableView.php");

$menu = array();

$page = new AdminPage();


$bean = new AttributesBean();

$h_delete = new DeleteItemResponder($bean);


$view = new TableView($bean->query());
$view->setCaption("Store attributes list");
$view->setDefaultOrder(" name ASC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));
$view->addColumn(new TableColumn("name", "Name"));
$view->addColumn(new TableColumn("unit", "Unit"));
$view->addColumn(new TableColumn("type", "Type"));

$view->addColumn(new TableColumn("actions", "Actions"));

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());

$view->getColumn("actions")->setCellRenderer($act);

Session::Set("attributes.list", $page->getPageURL());

$page->startRender();

$view->render();

$page->finishRender();

?>
