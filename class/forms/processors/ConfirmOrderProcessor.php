<?php
class ConfirmOrderProcessor extends FormProcessor 
{
    public function processImpl(InputForm $form)
    {

        parent::processImpl($form);
        
        if ($this->getStatus() != FormProcessor::STATUS_OK) return;
        
        //try {
    $db = DBDriver::get();
    $db->transaction();
  
    $inventory = new ProductInventoryBean();
//     $photos = new ProductColorPhotosBean();
    $products = new ProductsBean();
    $categories = new ProductCategoriesBean();
    
    $orders = new OrdersBean();
    $cart_data=array();

    $pos = 1;
    foreach ($items as $piID=>$qty) {

        $item = $inventory->getByID($piID);
                
        $prodID = $item["prodID"];
        
        $product = $products->getByID($prodID, false, " prodID, brand_name, product_code, product_name, catID, price, old_price ");

        $category = $categories->getByID($product["catID"]);

        $category_name = $category["category_name"];
        $brand_name = $product["brand_name"];

        $product_details = $product["product_name"]." // ".$item["color"]." // ".$item["size_value"];
        
        //$price = $currency_rates->getPrice($prod_row["sell_price"]);
        //$line_total = sprintf("%0.2f",($qty * $price["price_value"]));
        $line_total = sprintf("%0.2f",($qty * $item["price"]));
        $total=$total+$line_total;

        $line_data = array();
        $line_data[] = tr("Pos").": ".$pos;
        $line_data[] = tr("Product-Code").": ".$piID;
        $line_data[] = tr("Category").": ".$category_name;
        $line_data[] = tr("Brand").": ".$brand_name;
        $line_data[] = tr("Product").": ".$product_details;
        $line_data[] = tr("QTY").": ".$qty;
        $line_data[] = tr("Line-Total").": ".$line_total;
        
        
        $cart_data[] =  implode("||", $line_data);
 
        $pos++;

        try {
            $qty_update = array("stock_amount"=>($item["stock_amount"]-$qty));
            $inventory->updateRecord($piID, $qty_update, $db);
        }
        catch (Exception $e) {}
        
    }



    $delivery_price = $config->getValue("delivery_price",1);

    //$delivery_fee = $currency_rates->getPrice($delivery_price);

    $row["order_total"] = sprintf("%0.2f",($total+$delivery_price));

    $cart_details = implode("\r\n", $cart_data);

    $row["cart_data"] = $db->escapeString($cart_details);

    $order_identifier = md5(date(DATE_RFC822));

    $row["order_identifier"] = $order_identifier;
    $row["client_identifier"] = $form->getField("email")->getValue();
//     $row["active_currency"]= $price["currency_code"];
    $row["need_register"] = $form->getField("need_register")->getValue();

    $delivery_type = $delivery_form->getField("delivery_type")->getValue();
    $row["delivery_type"] = $delivery_type[0];

    $row["delivery_details"] = $db->escapeString($form->serializeXML());

    $row["ekont_office"] = $db->escapeString($delivery_form->getField("ekont_address")->getValue());

    $row["delivery_note"] = $db->escapeString($delivery_form->getField("delivery_note")->getValue());


    $auth = new UserAuthenticator();
    if ($auth->checkAuthState()) {
        $row["userID"] = $_SESSION[CONTEXT_USER]["id"]; 
    }

    $orderID = $orders->insertRecord($row, $db);
    if ($orderID<1) throw new Exception("Unable to insert your order: ".$db->getError());




    $db->commit();

    if (isset($_SESSION["order_address"])) {
        unset($_SESSION["order_address"]);
    }

    $cart->clearCart();

    $mailer = new OrderConfirmationMailer($orderID);
    $mailer->send();

    $mcopy = new OrderConfirmedAdminMailer($orderID,false);
    $mcopy->send();

}
catch (Exception $e) {

  $db->rollback();

  $row["error"]=$e->getMessage();

  ob_start();
  echo $e->getTraceAsString();
  $row["stacktrace"] = ob_get_contents();
  ob_end_clean();

  include_once("class/mailers/OrderErrorMailer.php");
  $m = new OrderErrorMailer($row);
  $m->send();
}
    }
}
?>
