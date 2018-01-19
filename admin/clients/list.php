<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("lib/handlers/DeleteItemRequestHandler.php");
include_once("lib/handlers/ToggleFieldRequestHandler.php");

include_once("lib/components/TableView.php");
include_once("lib/components/KeywordSearchComponent.php");
include_once("lib/iterators/SQLResultIterator.php");
include_once("lib/beans/UsersBean.php");

$menu = array(


);

$page = new AdminPage();
$page->checkAccess(ROLE_CLIENTS_MENU);



$bean = new UsersBean();
$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);
$h_toggle = new ToggleFieldRequestHandler($bean);
RequestController::addRequestHandler($h_toggle);


$search_fields = array("email","fullname", "phone");
$scomp = new KeywordSearchComponent($search_fields);



$sel = new SelectQuery();
$sel->fields = " u.email, u.fullname, u.userID, u.phone, last_active, counter, date_signup, u.suspend ";
$sel->from = " users u ";

$filter = $scomp->getQueryFilter();
if ($filter) {
    $sel = $sel->combineWith($filter);
}



$view = new TableView(new SQLResultIterator($sel, "userID"));
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
$vis_act->addAction( $h_toggle->createAction("Disable", "&field=suspend&status=1", "return (\$row['suspend'] < 1);"));
$vis_act->addAction( $h_toggle->createAction("Enable", "&field=suspend&status=0", "return (\$row['suspend'] > 0);"));
$view->getColumn("suspend")->setCellRenderer($vis_act);

$act = new ActionsTableCellRenderer();
$act->addAction(
  new Action("Edit", "add.php", array(new ActionParameter("editID",$bean->getPrKey()))  )
); 
$act->addAction(new PipeSeparatorAction());
$act->addAction( $h_delete->createAction() );

$view->getColumn("actions")->setCellRenderer($act);

//store page URL to session and restore on confirm product add or insert
Session::set("clients.list", $page->getPageURL());

$page->beginPage($menu);

$page->renderPageCaption();

$scomp->render();

$view->render();

$page->finishPage();
?>
