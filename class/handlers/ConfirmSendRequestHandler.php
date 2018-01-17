<?php
include_once("lib/handlers/RequestHandler.php");

include_once("class/mailers/OrderCompletionMailer.php");

class ConfirmSendRequestHandler extends RequestHandler
{

    protected $orderID = -1;

    public function __construct()
    {
        parent::__construct("confirm_send");
    }

    protected function parseParams() 
    {
        if (!isset($_GET["orderID"])) throw new Exception("Order ID not passed");
        $this->orderID = (int)$_GET["item_id"];
        $arr = $_GET;
        unset($arr["cmd"]);
        unset($arr["orderID"]);
        $this->cancel_url = queryString($arr);
        $this->cancel_url = $_SERVER['PHP_SELF'].$this->cancel_url;
        
  }
  protected function process() 
  {


      $db = DBDriver::factory();


      try {
// 	  $field_name = $db->escapeString($this->field_name);
// 
// 	  $update_row = array();
// 	  
// 	  $update_row[$field_name]=$this->status;
// 	  $update_row["completion_date"]=$db->dateTime();		
// 	  
// 	  if (!$this->bean->updateRecord($this->item_id, $update_row, $db))throw new Exception("Unable to update this order: ".$db->getError());
// 
// 
// 	  $m = new OrderCompletionMailer($this->item_id);
// 	  $m->send();
// 
// 	  
// 	  $db->commit();

	  Session::set("alert", tr("Поръчката беше маркирана като изпратена")."<BR>".tr("Потвърждаващ e-mail беше изпратен на клиента"));
	  
      }
      catch (Exception $e) {

	  $db->rollback();
	  throw $e;
      }


  }
  

  

}
?>
