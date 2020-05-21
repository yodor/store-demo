<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductsBean.php");
include_once("class/beans/StoreColorsBean.php");

include_once("components/TableView.php");
include_once("components/renderers/cells/TableImageCellRenderer.php");
include_once("components/renderers/cells/ColorCodeCellRenderer.php");
include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);


$bean = new StoreColorsBean();

$h_delete = new DeleteItemResponder($bean);


$view = new TableView($bean->query());
$view->setCaption("Available Colors");
$view->setDefaultOrder(" color ASC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));

$view->addColumn(new TableColumn("color", "Color"));
$view->addColumn(new TableColumn("color_code", "Color Code"));

$view->addColumn(new TableColumn("actions", "Actions"));

$view->getColumn("color_code")->setCellRenderer(new ColorCodeCellRenderer());

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());

$act->getActions()->append(new RowSeparator());

$view->getColumn("actions")->setCellRenderer($act);


$page->startRender();

// $ksc->render();
$view->render();

$page->finishRender();

?>
