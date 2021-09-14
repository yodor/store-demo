<?php
include_once("mailers/Mailer.php");
include_once("class/beans/OrdersBean.php");
include_once("class/beans/OrderItemsBean.php");
include_once("beans/UsersBean.php");
include_once("utils/Cart.php");

class OrderConfirmationMailer extends Mailer
{

    public function __construct(int $orderID)
    {

        parent::__construct();

        debug ("Accessing OrderBean with orderID: $orderID");
        $orders = new OrdersBean();
        $order = $orders->getByID($orderID);

        $userID = (int)$order["userID"];


        debug ("Accessing UsersBean with order userID: $userID");

        $users = new UsersBean();
        $user = $users->getByID($userID, "userID", "fullname", "email", "phone");

        debug ("Preparing message ...");

        $this->to = $user["email"];
        $this->subject = "Потвърждение на поръчка от ".SITE_DOMAIN;

        $message = "Здравейте, {$user["fullname"]}\r\n\r\n";
        $message .= "Изпращаме Ви това съобщение за да Ви уведомим, че поръчка Ви е приета за обработка. ";
        $message .= "\r\n\r\n";

        $order_link = SITE_URL . LOCAL . "/account/order_details.php?orderID=$orderID";

        $message .= "Можете да видите поръчката си в меню ";
        $message .= "<a href='$order_link'>моят профил -> поръчки</a>";

        $message .= "\r\n\r\n";

        $message .= "Поръчка Номер: $orderID \r\n";
        $message .= "Дата: {$order["order_date"]} \r\n";

        $delivery = new Delivery();
        $delivery->setSelectedCourier($order["delivery_courier"]);
        $courier = $delivery->getSelectedCourier();

        $courier->setSelectedOption($order["delivery_option"]);
        $option = $courier->getSelectedOption();

        $message .= "Куриер: " . $courier->getTitle() . "\r\n";
        $message .= "Начин на доставка: " . $option->getTitle() . "\r\n";

        $message .= "\r\n";

        $message .= "Поръчани продукти:\r\n\r\n";

        $message .= "<table border=1>";
        $message .= "<tr>";
        $message .= "<th>#</th><th>Продукт</th><th>Брой</th><th>Ед.Цена</th><th>Сума</th>";
        $message .= "</tr>";

        debug ("Preparing order items table ...");

        $order_items = new OrderItemsBean();
        $qry = $order_items->query("product", "position", "qty", "price");
        $qry->select->where()->add("orderID", $orderID);
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


        $message .= "</table>";

        $message .= "\r\n";
        $message .= "\r\n";

        $message .= "Продкти общо: " . sprintf("%0.2f лв.", ($order["total"] - $order["delivery_price"])) . "\r\n";
        $message .= "Цена доставка: " . sprintf("%0.2f лв.", $order["delivery_price"]) . "\r\n";
        $message .= "Поръчка oбщо: " . sprintf("%0.2f лв.", $order["total"]) . "\r\n";


        $message .= "\r\n";
        $message .= "\r\n";

        $message .= "Поздрави,\r\n";
        $message .= SITE_DOMAIN;

        $this->body = $this->templateMessage($message);

        debug ("Message contents prepared ...");


    }

}

?>
