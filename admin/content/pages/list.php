<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("beans/DynamicPagesBean.php");
include_once("components/renderers/cells/BooleanFieldCellRenderer.php");
include_once("components/renderers/cells/TableImageCellRenderer.php");

$menu = array();

$page = new AdminPage();

$page->checkAccess(ROLE_CONTENT_MENU);

if (!isset($_GET["chooser"])) {

    $page->setCaption("Dynamic Pages");

    $action_add = new Action("", "add.php", array());
    $action_add->setAttribute("action", "add");
    $action_add->setAttribute("title", "Add Page");
    $page->addAction($action_add);

}
else {
    $page->setCaption("Choose Page to Link");

}

$page->setAccessibleTitle("Dynamic Pages");

$bean = new DynamicPagesBean();

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$h_repos = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_repos);

$view = new TableView($bean->query());
$view->setCaption("Dynamic Pages List");
$view->setDefaultOrder(" position ASC ");
// $view->search_filter = " ORDER BY day_num ASC ";
$view->addColumn(new TableColumn($bean->key(), "ID"));
$view->addColumn(new TableColumn("photo", "Photo"));
$view->addColumn(new TableColumn("item_title", "Title"));
// $view->addColumn(new TableColumn("subtitle","Subtitle"));

$view->addColumn(new TableColumn("visible", "Visibility"));

$view->addColumn(new TableColumn("item_date", "Date"));

$view->addColumn(new TableColumn("position", "Position"));

$view->addColumn(new TableColumn("actions", "Actions"));

$view->getColumn("visible")->setCellRenderer(new BooleanFieldCellRenderer("Yes", "No"));

$view->getColumn("photo")->setCellRenderer(new TableImageCellRenderer());

$act = new ActionsTableCellRenderer();

if (isset($_GET["chooser"]) && isset($_SESSION["chooser_return"])) {

    $action_chooser = new Action("Choose", $_SESSION["chooser_return"], array(new DataParameter("page_id", $bean->key()),
                                                                              new URLParameter("page_class", "DynamicPagesBean")));

    $act->addAction($action_chooser);

}
else {

    $act->addAction(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
    $act->addAction(new PipeSeparator());
    $act->addAction($h_delete->createAction());

    $act->addAction(new RowSeparator());

    $act->addAction(new Action("Photo Gallery", "gallery/list.php", array(new DataParameter($bean->key()))));

    $act->addAction(new RowSeparator());

    $bkey = $bean->key();
    $repos_param = array(new DataParameter("item_id", $bkey));

    $act->addAction(new Action("Previous", "?cmd=reposition&type=previous", $repos_param));
    $act->addAction(new PipeSeparator());
    $act->addAction(new Action("Next", "?cmd=reposition&type=next", $repos_param));

    $act->addAction(new RowSeparator());

    $act->addAction(new Action("First", "?cmd=reposition&type=first", $repos_param));
    $act->addAction(new PipeSeparator());
    $act->addAction(new Action("Last", "?cmd=reposition&type=last", $repos_param));

}

$view->getColumn("actions")->setCellRenderer($act);

$page->startRender($menu);

// echo "<div class='page_caption'>";
// echo tr("Dynamic Pages");
// echo "</div>";

$view->render();
if (isset($_GET["chooser"])) unset($_GET["chooser"]);

$page->finishRender();
?>
