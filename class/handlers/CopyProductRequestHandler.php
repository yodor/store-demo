<?php
include_once("lib/handlers/RequestHandler.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductFeaturesBean.php");
include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ClassAttributeValuesBean.php");

class CopyProductRequestHandler extends RequestHandler
{
  
  protected $item_id = -1;
  protected $bean = NULL;

  public function __construct()
  {
	  parent::__construct("copy_product");

	  $this->bean = new ProductsBean();
	  
	  $this->need_confirm = true;
  }
  public function getItemID()
  {
      return $this->item_id;
  }
  protected function parseParams() 
  {
	  if (!isset($_GET["item_id"])) throw new Exception("Item ID not passed");
	  $this->item_id = (int)$_GET["item_id"];
	  $arr = $_GET;
	  unset($arr["cmd"]);
	  unset($arr["item_id"]);
	  $this->cancel_url = queryString($arr);
	  $this->cancel_url = $_SERVER['PHP_SELF'].$this->cancel_url;
	  
  }
  public function createAction($title="Copy", $href_add="", $check_code="return 1;", $parameters_array=array() )
  {
	  $parameters = array(new ActionParameter("item_id",$this->bean->getPrKey()));
	  return new Action($title, "?cmd=copy_product$href_add",  array_merge($parameters,$parameters_array), $check_code );
  }
  protected function processConfirmation()
  {
      $this->drawConfirmDialog("Confirm Copy","Confirm you want to copy this item including the attributes and photos?");
  }
  protected function process() 
  {
      
      
      $db = DBDriver::factory();
      

      try {
	     $cbrow = $this->bean->getByID($this->item_id);
	     
	    //copy the product
	    unset($cbrow["prodID"]);
	    $lastID = $this->bean->insertRecord($cbrow, $db);
	    if ($lastID<1) throw new Exception("Unable to copy the product: ".$db->getError());

	    //copy attributes
	    $pa = new ProductFeaturesBean();
	    $pa->startIterator("WHERE prodID={$this->item_id}");
	    while ($pa->fetchNext($parow)) {
		    unset($parow[$pa->getPrKey()]);
		    $parow["prodID"]=$lastID;
		    if (!$pa->insertRecord($parow, $db))throw new Exception("Unable to copy features: ".$db->getError());
	    }
	    //copy photos
	    $pp = new ProductPhotosBean();
	    $pp->startIterator("WHERE prodID={$this->item_id}");

	    while ($pp->fetchNext($pprow)) {
		    unset($pprow[$pp->getPrKey()]);
		    $pprow["prodID"]=$lastID;
		    $pprow["photo"]=$db->escapeString($pprow["photo"]);
// var_dump($pprow);
		    $lastppID = $pp->insertRecord($pprow, $db);
		    if ($lastppID<1)throw new Exception("Unable to copy photo: ".$db->getError());
	    }

	    $ca = new ClassAttributeValuesBean();
	    $ca->startIterator("WHERE prodID={$this->item_id}");

	    while ($ca->fetchNext($carow)) {
		    unset($carow[$ca->getPrKey()]);
		    $carow["prodID"]=$lastID;
		    
		    $lastcaID = $ca->insertRecord($carow, $db);
		    if ($lastcaID<1)throw new Exception("Unable to copy class attibutes: ".$db->getError());
	    }
	    
	    $db->commit();
	    $success=true;
	    Session::set("alert","Product Copy Success. Click <a href='add.php?editID=$lastID&catID={$cbrow["catID"]}'>here</a> to edit the product copy.");
// 				header("Location: add.php?editID=$lastID&catID=".$cbrow["catID"]);
	    header("Location: {$this->cancel_url}");
	    exit;
      }
      catch (Exception $e) {
	      
	      $db->rollback();
	      throw $e;
      }
	
  }
  

  

}
?>