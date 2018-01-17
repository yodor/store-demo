<?php
class OrderConfirmProcessor
{

  public $confirmed_order = NULL;
  public $user_details = NULL;
  public $orderID = -1;
  public $udID = -1;
  public $userID = -1;
  public $confirm_ticket = NULL;
  
  public function process()
  {
      debug("------------------------------");
      debug("OrderConfirmProcessor::process");
      
      $db = DBDriver::factory();
      $order_row = false;
      $udrow = false;
      
      $orderID = -1;
      $userID = -1;
      $udID = -1;
  
      try {

	$db->transaction();
	
	if (!isset($_GET["ticket"]) || !isset($_GET["email"])) {
	    throw new Exception("Required input data missing.");
	}
	
	$ticket = $db->escapeString(strip_tags(strtolower(trim($_GET["ticket"]))));
	$this->confirm_ticket = $ticket;
	
	$email = $db->escapeString(strip_tags(strtolower(trim($_GET["email"]))));
	
	$orders = new OrdersBean();

	$num = $orders->startIterator("WHERE order_identifier='$ticket' AND client_identifier='$email'");

	debug("OrderConfirmProcessor::process Checking ticket:$ticket email:$email");
	
	if ($num < 1) {
	    throw new Exception("Requested order was not found");
	}

	if (!$orders->fetchNext($order_row)) {
	    throw new Exception("Unable to access the requested order.");
	}

	if ($order_row["is_complete"]>0) {
	    throw new Exception("This order is already completed.");
	}
	if ($order_row["is_confirmed"]>0) {
	    throw new Exception("This order is already confirmed.");
	}

	
	
	$orderID = $order_row[$orders->getPrKey()];
	
	$update_order = array();
	$update_order["is_confirmed"]=1;
      
	if (!$orders->updateRecord($orderID, $update_order, $db)) {
	    debug("Unable to update order confirmation status: ".$db->getError());
	    throw new Exception("Unable to update order confirmation status.");
	}
	
	$order_row["is_confirmed"] = 1;
	
	debug("OrderConfirmProcessor::process OrderID:$orderID is now confirmed");
	
	if (isset($order_row["need_register"]) && $order_row["need_register"]>0) {

	    $random_pass = Authenticator::generateRandomAuth(8);

	    $form = new OrderAddressInputForm();
	    $form->unserializeXML($order_row["delivery_details"]);

	    $urow = array();
	    $urow["is_confirmed"] = 1;
	    $urow["email"] = $form->getField("email")->getValue();
	    $urow["password"] = md5($random_pass);
	    $urow["date_signup"] = $db->dateTime();

	    $users = new UsersBean();
	    $userID = $users->insertRecord($urow, $db);
	    if ($userID<1) {
	      debug("Unable to insert user: ".$db->getError());
	      throw new Exception("Unable to insert user.");
	    }
	    
	    $udrow = $form->getFieldValues();
	    $udrow["userID"] = $userID;
	    
	    $ud = new UserDetailsBean();
	    $udID = $ud->insertRecord($udrow, $db);
	    
	    if ($udID<1) {
	      debug("Unable to insert user details: ".$db->getError());
	      throw new Exception("Unable to insert user details.");
	    }
	    debug("OrderConfirmProcessor::process Registered details userID:$userID udID:$udID orderID:$orderID");
	    
	    debug("OrderConfirmProcessor::process RegisterDetailsPasswordMailer starting ...");
	
	    $mailer = new RegisterDetailsPasswordMailer($userID, $random_pass, $db);
	    $mailer->send();

	    debug("OrderConfirmProcessor::process RegisterDetailsPasswordMailer completed.");
	}
	else {
	    debug("OrderConfirmProcessor::process No registrer_details requested for this order.");
	}
	

	
	debug("OrderConfirmProcessor::process OrderConfirmedAdminMailer starting ...");
	
	$mcopy = new OrderConfirmedAdminMailer($orderID);
	$mcopy->send();

	debug("OrderConfirmProcessor::process OrderConfirmedAdminMailer completed.");
	
// 	$have_error = error_get_last();
// 	if (is_array($have_error)) {
// 	    throw new Exception("Error Code: ".$have_error["type"]." - ".$have_error["message"]." - Line: ".$have_error["line"]);
// 	}
  
	$db->commit();
	debug("OrderConfirmProcessor::process Transaction commit success.");
	debug("----------------------------------------------------------");
	
	$this->confirmed_order = $order_row;
	$this->user_details = $udrow;
	$this->orderID = $orderID;
	$this->udID = $udID;
	$this->userID = $userID;
	
	
	//process ok
    }
    catch (Exception $e) {
      debug("OrderConfirmProcessor::process Error: ". $e->getMessage());
      $db->rollback();
      throw $e;
    }

  }
}
?>
