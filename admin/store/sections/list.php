<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/SectionsBean.php");
include_once("components/TableView.php");

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$bean = new SectionsBean();

$h_position = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_position);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$view = new TableView($bean->query());
$view->setCaption("Sections List");
$view->setDefaultOrder(" position ASC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));
$view->addColumn(new TableColumn("section_title", "Section"));
$view->addColumn(new TableColumn("position", "Position"));

$view->addColumn(new TableColumn("actions", "Actions"));

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());
$act->getActions()->append(new RowSeparator());

$repos_param = array(new DataParameter("item_id", $bean->key()),);

$act->getActions()->append(new Action("First", "?cmd=reposition&type=first", $repos_param));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append(new Action("Last", "?cmd=reposition&type=last", $repos_param));
$act->getActions()->append(new RowSeparator());
$act->getActions()->append(new Action("Previous", "?cmd=reposition&type=previous", $repos_param));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append(new Action("Next", "?cmd=reposition&type=next", $repos_param));

$act->getActions()->append(new RowSeparator());

$act->getActions()->append(new Action("Banners Gallery", "banners/list.php", array(new DataParameter("secID", $bean->key()))));

$view->getColumn("actions")->setCellRenderer($act);

$page->startRender();

$view->render();

$page->finishRender();

?>
