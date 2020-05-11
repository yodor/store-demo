<?php
include_once("mailers/Mailer.php");
include_once("class/beans/OrdersBean.php");
include_once("beans/UsersBean.php");

class OrderStatusMailer extends Mailer 
{

    public function __construct($orderID, $status)
    {

	$orders = new OrdersBean();
        $order = $orders->getByID($orderID);

        $userID = (int)$order["userID"];

        $users = new UsersBean();
        $user = $users->getByID($userID, array("userID", "fullname", "email", "phone"));
        
        $this->to = $user["email"];

        $this->subject = "Вашата поръчка / Your order ID:$orderID";
		
        $message = "Здравейте, {$user["fullname"]}<br><br>\r\n\r\n";

	$message.="Статусът на Вашата поръчка от ".SITE_DOMAIN." беше обновен на: ";

	$message.=$status."<br>\r\n";
	$message.="\r\n\r\n<br><br>";

	$message.="<BR><BR>\r\n\r\nС Уважение,<BR>\r\n";
	$message.=SITE_DOMAIN;

	$message.="<BR><BR>\r\n\r\n";

	$message.="Hello, {$user["fullname"]}<br><br>\r\n\r\n";

	$message.="The status of your order at ".SITE_DOMAIN." was updated to: ";

	$message.=$status."<br>\r\n";
	$message.="\r\n\r\n<br><br>";

	$message.="<BR><BR>\r\n\r\nSincerely,<BR>\r\n";
	$message.=SITE_DOMAIN;

	$this->body = $this->templateMessage($message);

    }

}
?>
