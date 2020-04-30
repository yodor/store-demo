<?php
// define("DEBUG_OUTPUT", 1);

include_once("class/utils/Cart.php");
include_once("class/beans/OrdersBean.php");
include_once("class/beans/OrderItemsBean.php");

include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("lib/beans/ConfigBean.php");


class OrderProcessor {

    public function __construct()
    {


    }

    public function createOrder(Cart $cart, $userID)
    {
        debug("OrderProcessor::createOrder(UserID=$userID)");

        //         throw new Exception("Not implemented yet");

        if (count($cart->getItems())<1) throw new Exception("Вашата кошница е празна");


        if (!UserAuthenticator::checkAuthState()) {
            debug("OrderProcessor::createOrder() Login required ... ");
            throw new Exception("Login required");
        }

        $auth_userID = (int)$_SESSION[CONTEXT_USER]["id"];
        if ($auth_userID!=$userID) throw new Exception("Изисква регистриран потребител");

        $db = DBDriver::get();
        $orderID = -1;

        try {

            $db->transaction();

            $inventory = new ProductInventoryBean();


            $orders = new OrdersBean();
            $cart_data=array();

            $items = $cart->getItems();

            $config = ConfigBean::factory();
            $config->setSection("delivery_prices");
            $delivery_price = $config->getValue($cart->getDeliveryType(),1);

            $order = array();
            $order["delivery_price"] = sprintf("%0.2f", $delivery_price);
            $order["delivery_type"] = $cart->getDeliveryType();
            $order["note"] = $cart->getNote();
            $order["require_invoice"] = $cart->getRequireInvoice();
            $order["userID"] = $userID;

            $order_total = 0;

            foreach ($items as $piID=>$qty) {

                $item = $inventory->getByID($piID, $db, " price ");
                $line_total = sprintf("%0.2f",($qty * $item["price"]));
                $order_total = $order_total + $line_total;

            }
            $order_total = $order_total + $delivery_price;
            $order["total"] = $order_total;

            $orderID = $orders->insertRecord($order, $db);
            if ($orderID<1) throw new Exception("Unable to insert order: ".$db->getError());

            $order_items = new OrderItemsBean();
            $products = new ProductsBean();
            $photos = new ProductColorPhotosBean();
            $gallery_photos = new ProductPhotosBean();

            $pos = 1;
            foreach ($items as $piID=>$qty) {

                $item = $inventory->getByID($piID, $db);
                $prodID = (int)$item["prodID"];

                $product = $products->getByID($prodID, $db, " prodID, brand_name, product_name ");

                $product_details = "Продукт||{$product["product_name"]}//Цвят||{$item["color"]}//Размер||{$item["size_value"]}//Марка||{$product["brand_name"]}//Код|| {$piID}-{$prodID}";

                //try inventory image raw data else product photos
                $item_photo = null;

                $pclrID = $item["pclrID"];
                $pclrpID = $photos->getFirstPhotoID($pclrID);

                try {
                    debug("OrderProcessor::createOrder() Doing copy of product photo to order ");

                    //try product gallery photos
                    if ($pclrpID<1) {
                        $ppID = $gallery_photos->getFirstPhotoID($prodID);
                        //no photo here too
                        if ($ppID<1) {
                            debug("No product source photo to store into order items. ProdID=$prodID | InvID=$piID ");
                        }
                        else {
                            //copy
                            $photo_row = $gallery_photos->getByID($ppID);
                            $item_photo = $photo_row["photo"];
                        }

                    }
                    else {
                        $photo_row = $photos->getByID($pclrpID);
                        $item_photo = $photo_row["photo"];
                    }
                }
                catch (Exception $e) {
                    debug("Unable to copy source product photos. ProdID=$prodID | InvID=$piID | Exception: ".$e->getMessage());
                }










                $order_item = array();
                $order_item["piID"] = $piID;
                $order_item["qty"] = $qty;
                $order_item["price"] = $item["price"];
                $order_item["position"] = $pos;
                $order_item["orderID"] = $orderID;
                $order_item["product"] = $product_details;
                $order_item["prodID"] = $prodID;
                $order_item["photo"] = DBDriver::get()->escapeString($item_photo);

                $itemID = $order_items->insertRecord($order_item, $db);
                if ($itemID<1)throw new Exception("Unable to insert order item: ".$db->getError());

                $inventory_update = array(
                    "stock_amount"=>($item["stock_amount"]-$qty),
                    "order_counter"=>($item["order_counter"]+1)
                );
                if (!$inventory->updateRecord($piID, $inventory_update, $db)) throw new Exception("Unable to update inventory statistics: ".$db->getError());

                $pos++;
            }

            debug("OrderProcessor::createOrder() finalizing transaction ... ");
            $db->commit();
            $cart->clearCart();

            debug("OrderProcessor::createOrder() order completed ... ");
        }
        catch (Exception $e) {
            $orderID = -1;
            $db->rollback();
            throw new Exception($e->getMessage());

        }
        return $orderID;
    }

}
?>
