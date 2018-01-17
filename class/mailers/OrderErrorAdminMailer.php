<?php
include_once("lib/mailers/Mailer.php");

class OrderErrorAdminMailer extends Mailer 
{

    public function __construct()
    {

	$this->to = ORDER_ERROR_EMAIL;
	$this->subject = "Order Error";

// 	ob_start();
// 	var_dump($row);
// 	$message = ob_get_contents();
// 	ob_end_clean();

        $message = "";
        
	$this->body = $this->templateMessage($message);

    }

}
?>
