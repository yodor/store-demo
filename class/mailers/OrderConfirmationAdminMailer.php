<?php
include_once("mailers/Mailer.php");

class OrderConfirmationAdminMailer extends Mailer
{

    public function __construct($orderID)
    {

        $this->to = ORDER_ADMIN_EMAIL;

        $this->subject = "Нова поръчка / New order - ID:$orderID ";

        $message = "OrderID: $orderID\r\n<BR>";

        $order_link = SITE_URL . LOCAL . "admin/orders/list.php?orderID=$orderID";

        $message .= "Можете да видите поръчката на адрес - ";
        $message .= "<a href='$order_link'>$order_link</a>";

        $this->body = $this->templateMessage($message);

    }

}

?>
