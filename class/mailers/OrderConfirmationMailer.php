<?php
include_once("lib/mailers/Mailer.php");
include_once("class/beans/OrdersBean.php");
include_once("class/beans/OrderItemsBean.php");
include_once("lib/beans/UsersBean.php");
include_once("class/utils/Cart.php");

class OrderConfirmationMailer extends Mailer
{

    public function __construct(int $orderID)
    {

        $orders = new OrdersBean();

        $order = $orders->getByID($orderID);

        $userID = (int)$order["userID"];

        $users = new UsersBean();
        $user = $users->fieldValues($userID, array("userID", "fullname", "email", "phone"));

        $this->to = $user["email"];

        $this->subject = "Потвърждение на поръчка / Order Confirmation - OrderID $orderID";


        $message = "Здравейте, {$user["fullname"]}<br><br>\r\n\r\n";
        $message .= "Изпращаме Ви това съобщение за да Ви уведомим, че поръчка Ви е приета за обработка. ";
        $message .= "\r\n\r\n<br><br>";

        $order_link = SITE_URL . SITE_ROOT . "account/order_details.php?orderID=$orderID";

        $message .= "Можете да видите поръчката си на адрес - ";
        $message .= "<a href='$order_link'>$order_link</a>";

        $message .= "\r\n\r\n<br><br>";


        $message .= "Поръчка Номер: $orderID \r\n<br>";
        $message .= "Дата: {$order["order_date"]} \r\n<br>";
        $message .= "Начин на доставка: " . Cart::getDeliveryTypeText($order["delivery_type"]) . "\r\n<BR>";

        $message .= "\r\n<br>";

        $message .= "Поръчани продукти:\r\n<br>";

        $message .= "<table border=1>";
        $message .= "<tr>";
        $message .= "<th>#</th><th>Продукт</th><th>Брой</th><th>Ед.Цена</th><th>Сума</th>";
        $message .= "</tr>";

        $order_items = new OrderItemsBean();
        $qry = $order_items->queryField("orderID", $orderID);
        $qry->select->order_by = " position ASC ";
        $qry->exec();

        while ($item = $qry->next()) {

            $message .= "<tr>";

            $details = explode("//", $item["product"]);

            $message .= "<td>{$item["position"]}</td>";

            $message .= "<td>";
            foreach ($details as $index => $value) {
                $data = explode("||", $value);
                $message .= $data[0] . ": " . $data[1] . "<BR>";
            }
            $message .= "</td>";

            $message .= "<td>" . $item["qty"] . "</td>";
            $message .= "<td>" . sprintf("%0.2f лв.", $item["price"]) . "</td>";
            $message .= "<td>" . sprintf("%0.2f лв.", ((int)$item["qty"] * $item["price"])) . "</td>";

            $message .= "</tr>";
        }
        $message .= "\r\n<br>";
        $message .= "</table>";

        $message .= "Продкти общо: " . sprintf("%0.2f лв.", ($order["total"] - $order["delivery_price"])) . "\r\n<br>";
        $message .= "Цена доставка: " . sprintf("%0.2f лв.", $order["delivery_price"]) . "\r\n<br>";
        $message .= "Поръчка oбщо: " . sprintf("%0.2f лв.", $order["total"]) . "\r\n<br>";

        $message .= "\r\n<br>";
        $message .= "\r\n<br>";

        $message .= "С уважение,\r\n<BR>";
        $message .= SITE_DOMAIN;


        $this->body = $this->templateMessage($message);

        
    }	

}

?>
