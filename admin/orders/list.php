<?php
include_once("components/TableView.php");

include_once("beans/UsersBean.php");
include_once("class/beans/ClientAddressesBean.php");
include_once("class/beans/EkontAddressesBean.php");
include_once("class/beans/InvoiceDetailsBean.php");
include_once("class/beans/OrderItemsBean.php");

include_once("class/components/renderers/cells/OrderItemsCellRenderer.php");
include_once("class/components/renderers/cells/OrderDeliveryCellRenderer.php");
include_once("class/components/renderers/cells/OrderClientCellRenderer.php");
include_once("class/components/renderers/cells/OrderInvoiceCellRenderer.php");

include_once("components/renderers/cells/BooleanFieldCellRenderer.php");
include_once("components/renderers/cells/DateFieldCellRenderer.php");

include_once("components/KeywordSearchComponent.php");
include_once("iterators/SQLQuery.php");

$ekont_addresses = new EkontAddressesBean();
$client_addresses = new ClientAddressesBean();
$clients = new UsersBean();
$invoices = new InvoiceDetailsBean();
$order_items = new OrderItemsBean();

$db = DBConnections::Factory();

$search_fields = array("orderID", "items");
$scomp = new KeywordSearchComponent($search_fields);

$filter = $scomp->filterSelect();
if ($filter) {
    $sel = $sel->combineWith($filter);
}

$caption = "Orders List";

// if (isset($_GET["filter"])) {
//   $fsel = false;
//
//   $filter = $db->escapeString($_GET["filter"]);
//   if (strcmp($filter, "completed")==0) {
// 	$fsel = new SelectQuery();
// 	$fsel->where = " is_complete=1 ";
// 	$fsel->fields = "";
//
// 	$caption.=" - Completed";
//   }
//   else if (strcmp($filter, "confirmed")==0) {
// 	$fsel = new SelectQuery();
// 	$fsel->where = " is_complete=0 AND is_confirmed=1 ";
// 	$fsel->fields = "";
//
// 	$caption.=" - Confirmed";
//   }
//   if ($fsel) {
//     $sel = $sel->combineWith($fsel);
//
//   }
// }

// echo $sel->getSQL();

$view = new TableView(new SQLQuery($sel, "orderID"));

$view->setCaption($caption);

$view->setDefaultOrder(" order_date DESC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));

$view->addColumn(new TableColumn("order_date", "Order Date"));

$view->addColumn(new TableColumn("userID", "Client"));

$view->addColumn(new TableColumn("items", "Items"));

$view->addColumn(new TableColumn("note", "Note"));

$view->addColumn(new TableColumn("require_invoice", "Invoice"));

$view->addColumn(new TableColumn("delivery_type", "Delivery Type"));

$view->addColumn(new TableColumn("delivery_price", "Delivery Price"));

$view->addColumn(new TableColumn("total", "Total"));

$view->addColumn(new TableColumn("status", "Status"));

$view->addColumn(new TableColumn("actions", "Actions"));

// $view->getColumn("is_confirmed")->setCellRenderer(new BooleanFieldCellRenderer("Yes", "No"));
// $view->getColumn("require_invoice")->setCellRenderer(new BooleanFieldCellRenderer("Yes", "No"));
$view->getColumn("order_date")->setCellRenderer(new DateFieldCellRenderer());

$view->getColumn("userID")->setCellRenderer(new OrderClientCellRenderer());
$view->getColumn("items")->setCellRenderer(new OrderItemsCellRenderer());
$view->getColumn("delivery_type")->setCellRenderer(new OrderDeliveryCellRenderer());
$view->getColumn("require_invoice")->setCellRenderer(new OrderInvoiceCellRenderer());

$act = new ActionsTableCellRenderer();

// $act->addAction(
//   $h1->createAction("Маркирай изпратена","?cmd=confirm_send",  "return (\$row['status']>0 && \$row['is_complete']<1);")
//
// );
//
// $act->addAction(
//   new Action(
// 	"Потвърди изпълнение", "?cmd=confirm_send",
// 	array(
// 	  new ActionParameter("orderID", "orderID"),
// 	)
//
// // 	"return (\$row['is_confirmed']<1 && \$row['is_complete']<1);"
//   )
//
// );
// $act->addAction(  new RowSeparatorAction() );
// $act->addAction( $h_delete->createAction() );
// $act->addAction(  new RowSeparatorAction() );

$view->getColumn("actions")->setCellRenderer($act);

?>
