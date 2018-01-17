<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("lib/beans/ConfigBean.php");

include_once("lib/handlers/DeleteItemRequestHandler.php");


include_once("lib/components/TableView.php");
include_once("lib/components/KeywordSearchComponent.php");
include_once("lib/iterators/SQLResultIterator.php");

// include_once("class/components/OrderDetailsCellRenderer.php");
// include_once("class/components/CartDataCellRenderer.php");


$menu = array(


);



$page = new AdminPage();
$page->checkAccess(ROLE_CLIENTS_MENU);




$search_fields = array("email","fullname", "company", "city", "postcode", "address1", "address2");
$scomp = new KeywordSearchComponent($search_fields);





$caption = "Clients List";

$sel = new SelectQuery();
$sel->fields = " * ";
$sel->from = " users JOIN user_details ON user_details.userID = users.userID ";

$filter = $scomp->getQueryFilter();
if ($filter) {
$sel = $sel->combineWith($filter);
}

// $sql="SELECT * FROM users JOIN user_details ON user_details.userID = users.userID";
// $sqliterator = new SQLResultIterator($sql, "userID");


$view = new TableView(new SQLResultIterator($sel, "userID"));
$view->setDefaultOrder(" date_signup DESC ");
$view->items_per_page = 20;

$view->addColumn(new TableColumn("userID", "ID"));
$view->addColumn(new TableColumn("email", "Email"));
$view->addColumn(new TableColumn("fullname", "Full Name"));
$view->addColumn(new TableColumn("phone", "Phone"));
$view->addColumn(new TableColumn("city", "City"));
$view->addColumn(new TableColumn("postcode", "Post Code"));
$view->addColumn(new TableColumn("address1", "Address Line 1"));
$view->addColumn(new TableColumn("address2", "Address Line 2"));
$view->addColumn(new TableColumn("date_signup", "Registration Date"));

$act = new ActionsTableCellRenderer();

$view->setCaption($caption);

$page->beginPage($menu);

$scomp->render();

$view->render();


$page->finishPage();



?>