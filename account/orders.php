<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");
include_once("class/components/renderers/cells/OrderDeliveryCellRenderer.php");
include_once("class/components/renderers/cells/OrderInvoiceCellRenderer.php");
include_once("class/components/renderers/cells/OrderItemsCellRenderer.php");
include_once("components/TableView.php");
include_once("components/renderers/cells/DateCellRenderer.php");
include_once("iterators/SQLQuery.php");
include_once("beans/UsersBean.php");
include_once("class/beans/ClientAddressesBean.php");
include_once("class/beans/EkontAddressesBean.php");
include_once("class/beans/InvoiceDetailsBean.php");
include_once("class/beans/OrderItemsBean.php");

$page = new AccountPage();

$ekont_addresses = new EkontAddressesBean();
$client_addresses = new ClientAddressesBean();
$invoices = new InvoiceDetailsBean();
$order_items = new OrderItemsBean();

$clients = new UsersBean();

$sel = new SQLSelect();

$sel->fields()->set("*");
$sel->fields()->setExpression(" (SELECT concat(sum(oi.qty),' бр.') FROM order_items oi WHERE oi.orderID = o.orderID) ", "item_count");
$sel->from = " orders o ";
$sel->where()->add("o.userID", $page->getUserID());
$sel->order_by = " 'Processing', 'Sent', 'Completed' ";

$view = new TableView(new SQLQuery($sel, "orderID"));

// $view->setCaption($caption);

$view->setDefaultOrder(" order_date DESC ");

$view->addColumn(new TableColumn("orderID", "Номер"));

$view->addColumn(new TableColumn("order_date", "Дата"));

$view->addColumn(new TableColumn("item_count", "Продукти"));

$view->addColumn(new TableColumn("total", "Сума"));

// $view->addColumn(new TableColumn("delivery_price", "Delivery Price"));

// $view->addColumn(new TableColumn("delivery_type", "Delivery Type"));

// $view->addColumn(new TableColumn("note", "Забележка"));

// $view->addColumn(new TableColumn("require_invoice", "Фактуриране"));

$view->addColumn(new TableColumn("status", "Статус"));

$view->addColumn(new TableColumn("actions", "Действия"));

// $view->getColumn("is_confirmed")->setCellRenderer(new BooleanFieldCellRenderer("Yes", "No"));
// $view->getColumn("require_invoice")->setCellRenderer(new BooleanFieldCellRenderer("Yes", "No"));
$view->getColumn("order_date")->setCellRenderer(new DateCellRenderer());

// $view->getColumn("userID")->setCellRenderer(new OrderClientCellRenderer());
// $view->getColumn("items")->setCellRenderer(new OrderItemsCellRenderer());
// $view->getColumn("delivery_type")->setCellRenderer(new OrderDeliveryCellRenderer());
// $view->getColumn("require_invoice")->setCellRenderer(new BooleanFieldCellRenderer("Да", "Не"));

$act = new ActionsCellRenderer();

$act->getActions()->append(new Action("Покажи детайли", "order_details.php", array(new DataParameter("orderID"))));

$view->getColumn("actions")->setCellRenderer($act);

$page->startRender();
$page->setTitle(tr("История на поръчките"));

echo "<div class='caption'>" . $page->getTitle() . "</div>";
$view->render();

$page->finishRender();
?>
