<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("handlers/DeleteItemRequestHandler.php");
include_once("handlers/ToggleFieldRequestHandler.php");

include_once("components/TableView.php");
include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");
include_once("beans/UsersBean.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CLIENTS_MENU);

$bean = new UsersBean();
$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_toggle = new ToggleFieldRequestHandler($bean);
RequestController::addRequestHandler($h_toggle);

$search_fields = array("email", "fullname", "phone");
$scomp = new KeywordSearch($search_fields);

$sel = new SQLSelect();
$sel->fields = " u.email, u.fullname, u.userID, u.phone, last_active, counter, date_signup, u.suspend ";
$sel->from = " users u ";

$filter = $scomp->filterSelect();
if ($filter) {
    $sel = $sel->combineWith($filter);
}

$view = new TableView(new SQLQuery($sel, "userID"));
$view->setDefaultOrder(" userID DESC ");
$view->items_per_page = 20;

$view->addColumn(new TableColumn("userID", "ID"));

$view->addColumn(new TableColumn("fullname", "Full Name"));
$view->addColumn(new TableColumn("email", "Email"));
$view->addColumn(new TableColumn("phone", "Phone"));

$view->addColumn(new TableColumn("date_signup", "Registration Date"));
$view->addColumn(new TableColumn("last_active", "Last Active"));
$view->addColumn(new TableColumn("counter", "Login Count"));
$view->addColumn(new TableColumn("suspend", "State"));

$view->addColumn(new TableColumn("actions", "Actions"));

$vis_act = new ActionsTableCellRenderer();
$check_is_suspend = function (Action $act, array $data) {
    return ($data['suspend'] < 1);
};
$check_is_not_suspend = function (Action $act, array $data) {
    return ($data['suspend'] > 0);
};
$vis_act->getActions()->append($h_toggle->createAction("Disable", "field=suspend&status=1", $check_is_suspend));
$vis_act->getActions()->append($h_toggle->createAction("Enable", "field=suspend&status=0", $check_is_not_suspend));
$view->getColumn("suspend")->setCellRenderer($vis_act);

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());

$view->getColumn("actions")->setCellRenderer($act);

//store page URL to session and restore on confirm product add or insert
Session::Set("clients.list", $page->getPageURL());

$page->startRender($menu);

$scomp->render();

$view->render();

$page->finishRender();
?>
