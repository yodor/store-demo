<?php
include_once("templates/admin/BeanListPage.php");
include_once("class/responders/OrderStatusRequestResponder.php");
include_once("class/beans/OrdersBean.php");

include_once("class/components/renderers/cells/OrderClientCellRenderer.php");

include_once("components/renderers/cells/BooleanCellRenderer.php");
include_once("components/renderers/cells/DateCellRenderer.php");

include_once("class/utils/OrderListSQL.php");


class OrdersListPage extends BeanListPage
{
    protected $orderList;

    public function __construct()
    {
        parent::__construct();

        $h_send = new OrderStatusRequestResponder();


        $this->keyword_search->getForm()->setFields(array("orderID", "items", "client", "delivery_address"));

        $this->orderList = new OrderListSQL();

        $this->getPage()->navigation()->clear();
        $this->getPage()->getActions()->removeByAction("Add");

        $listFields = array(
                            "order_date" => "Order Date",
                            "userID" => "Client",
//                            "items" => "Items",
//                            "note" => "Note",
//                            "require_invoice" => "Invoice",
//                            "delivery_type" => "Delivery Type",
                            "total" => "Total",
                            "status" => "Status");

        $this->setListFields($listFields);

        $this->setIterator(new SQLQuery($this->orderList, "orderID", "orders"));

    }

    public function getOrderListSQL(): OrderListSQL
    {
        return $this->orderList;
    }

    protected function initViewActions(ActionCollection $act)
    {
        $act->append(
            new Action(tr("Details"), "details.php",
                       array(new DataParameter("orderID"))
            ));

        $act->append(new RowSeparator());
    }

    public function initView()
    {
        parent::initView();

        $this->view->getColumn("order_date")->setCellRenderer(new DateCellRenderer());

        $this->view->getColumn("userID")->setCellRenderer(new OrderClientCellRenderer());

        $this->view->setDefaultOrder(" order_date DESC ");

    }

}