<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/StoreSizesBean.php");

include_once("components/TableView.php");
include_once("components/renderers/cells/ImageCellRenderer.php");
include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");

$menu = array();

$page = new AdminPage();

$bean = new StoreSizesBean();

$h_delete = new DeleteItemResponder($bean);

$view = new TableView($bean->query());
$view->items_per_page = 100;

$view->setCaption("Sizing Codes List");
$view->setDefaultOrder(" size_value ASC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));
$view->addColumn(new TableColumn("size_value", "Size"));

$view->addColumn(new TableColumn("actions", "Actions"));

$act = new ActionsCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());

$view->getColumn("actions")->setCellRenderer($act);

$page->startRender();

$view->render();

$page->finishRender();
?>
