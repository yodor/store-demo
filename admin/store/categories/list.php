<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/ProductCategoriesBean.php");
include_once("components/NestedSetTreeView.php");
include_once("components/renderers/items/TextTreeItem.php");


$page = new AdminPage();


$bean = new ProductCategoriesBean();

$h_repos = new ChangePositionResponder($bean);


$h_delete = new DeleteItemResponder($bean);


$ir = new TextTreeItem();
$ir->getActions()->append(new Action("Up", "?cmd=reposition&type=left", array(new DataParameter("item_id", $bean->key()))));
$ir->getActions()->append(new Action("Down", "?cmd=reposition&type=right", array(new DataParameter("item_id", $bean->key()))));

$ir->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$ir->getActions()->append($h_delete->createAction());

$ir->setLabelKey("category_name");

$tv = new NestedSetTreeView();
$tv->setItemRenderer($ir);

$tv->setIterator(new SQLQuery($bean->selectTree(array("category_name")), $bean->key(), $bean->getTableName()));

$tv->setName("ProductCategores");

Session::Set("categories.list", $page->getPageURL());

$page->startRender();

$tv->render();

$page->finishRender();

?>
