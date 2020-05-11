<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/ProductCategoriesBean.php");
include_once("components/NestedSetTreeView.php");
include_once("components/renderers/items/TextTreeItem.php");


$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_add = new Action("", "add.php", array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Category");
$page->addAction($action_add);

$bean = new ProductCategoriesBean();

$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);


$ir = new TextTreeItem();
$ir->addAction(new Action("Up", "?cmd=reposition&type=left", array(new ActionParameter("item_id", $bean->key()))));
$ir->addAction(new Action("Down", "?cmd=reposition&type=right", array(new ActionParameter("item_id", $bean->key()))));

$ir->addAction(new Action("Edit", "add.php", array(new ActionParameter("editID", $bean->key()))));
$ir->addAction($h_delete->createAction());

$ir->setLabelKey("category_name");

$tv = new NestedSetTreeView();
$tv->setItemRenderer($ir);

$tv->setItemIterator(new SQLQuery($bean->selectTree(array("category_name")), $bean->key(), $bean->getTableName()));

$tv->setName("ProductCategores");

Session::Set("categories.list", $page->getPageURL());

$page->startRender($menu);
$page->renderPageCaption();

$tv->render();

$page->finishRender();


?>
