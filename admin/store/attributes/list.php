<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/AttributesBean.php");
include_once("components/TableView.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_add = new Action("", "add.php", array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add attribute");
$page->addAction($action_add);

$bean = new AttributesBean();

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$view = new TableView($bean->query());
$view->setCaption("Store attributes list");
$view->setDefaultOrder(" name ASC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));
$view->addColumn(new TableColumn("name", "Name"));
$view->addColumn(new TableColumn("unit", "Unit"));
$view->addColumn(new TableColumn("type", "Type"));

$view->addColumn(new TableColumn("actions", "Actions"));

$act = new ActionsTableCellRenderer();
$act->addAction(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->addAction(new PipeSeparator());
$act->addAction($h_delete->createAction());

$view->getColumn("actions")->setCellRenderer($act);

Session::Set("attributes.list", $page->getPageURL());

$page->startRender($menu);

$view->render();

$page->finishRender();

?>
