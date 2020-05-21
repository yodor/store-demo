<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductClassesBean.php");

include_once("components/TableView.php");
include_once("components/renderers/cells/TableImageCellRenderer.php");
include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");
// include_once("class/beans/ProductInventoryPhotosBean.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);


$bean = new ProductClassesBean();

$sql = $bean->select();

$sql->from = " product_classes pc ";
$sql->fields = " pc.*, (SELECT GROUP_CONCAT(attribute_name SEPARATOR '<BR>') FROM class_attributes ca WHERE ca.pclsID=pc.pclsID) as class_attributes ";

$h_delete = new DeleteItemResponder($bean);


$view = new TableView(new SQLQuery($sql, $bean->key()));
$view->setCaption("Product Classes List");
$view->setDefaultOrder($bean->key() . " DESC ");
$view->addColumn(new TableColumn($bean->key(), "ID"));
$view->addColumn(new TableColumn("class_name", "Class Name"));
$view->addColumn(new TableColumn("class_attributes", "Attributes"));

$view->addColumn(new TableColumn("actions", "Actions"));

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());

$view->getColumn("actions")->setCellRenderer($act);


$page->startRender();

$view->render();

$page->finishRender();
?>
