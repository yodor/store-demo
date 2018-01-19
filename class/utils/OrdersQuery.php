<?php
include_once("lib/utils/SelectQuery.php");

class OrdersQuery extends SelectQuery
{
    public function __construct()
    {
        parent::__construct();

        //select additionaly the items and client - allow search
        $this->fields = " *, (SELECT GROUP_CONCAT('-oi-', oi.product) FROM  order_items oi WHERE oi.orderID=o.orderID) as items, (SELECT CONCAT_WS('--', u.fullname, u.email, u.phone) FROM users u WHERE u.userID=o.userID) as client ";
        $this->from = " orders o  ";
        $this->where = "";
    }
}
?>
