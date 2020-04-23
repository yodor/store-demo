<?php
include_once("class/utils/Cart.php");
include_once("class/beans/OrdersBean.php");
include_once("class/beans/OrderItemsBean.php");

include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("lib/beans/ConfigBean.php");


class OrderProcessor
{

    public function __construct()
    {


    }

    public function createOrder(Cart $cart, $userID)
    {
        //         throw new Exception("Not implemented yet");

        if (count($cart->getItems()) < 1) throw new Exception("Вашата кошница е празна");


        $auth_userID = SparkPage::Instance()->getUserID();

        if ($auth_userID != $userID) throw new Exception("Изисква регистриран потребител");

        $db = DBDriver::Get();
        $orderID = -1;

        try {

            $db->transaction();

            $inventory = new ProductInventoryBean();


            $orders = new OrdersBean();
            $cart_data = array();

            $items = $cart->getItems();

            $config = ConfigBean::factory();
            $config->setSection("delivery_prices");
            $delivery_price = $config->getValue($cart->getDeliveryType(), 1);

            $order = array();
            $order["delivery_price"] = sprintf("%0.2f", $delivery_price);
            $order["delivery_type"] = $cart->getDeliveryType();
            $order["note"] = $cart->getNote();
            $order["require_invoice"] = $cart->getRequireInvoice();
            $order["userID"] = $userID;

            $order_total = 0;

            foreach ($items as $piID => $qty) {

                $item = $inventory->getByID($piID, $db, " price ");
                $line_total = sprintf("%0.2f", ($qty * $item["price"]));
                $order_total = $order_total + $line_total;

            }
            $order_total = $order_total + $delivery_price;
            $order["total"] = $order_total;

            $orderID = $orders->insert($order, $db);
            if ($orderID < 1) throw new Exception("Unable to insert order: " . $db->getError());

            $order_items = new OrderItemsBean();
            $products = new ProductsBean();
            $photos = new ProductColorPhotosBean();

            $pos = 1;
            foreach ($items as $piID => $qty) {

                $item = $inventory->getByID($piID, $db);
                $prodID = (int)$item["prodID"];

                $product = $products->getByID($prodID, $db, " prodID, brand_name, product_name ");

                $product_details = "Продукт||{$product["product_name"]}//Цвят||{$item["color"]}//Размер||{$item["size_value"]}//Марка||{$product["brand_name"]}//Код|| {$piID}-{$prodID}";

                //get the inventory image raw data
                $pclrID = $item["pclrID"];
                $item_photo = NULL;

                try {
                    $pclrpID = $photos->getFirstPhotoID($pclrID);
                    $photo_row = $photos->getByID($pclrpID);
                    $item_photo = $photo_row["photo"];
                }
                catch (Exception $e) {

                }


                $order_item = array();
                $order_item["piID"] = $piID;
                $order_item["qty"] = $qty;
                $order_item["price"] = $item["price"];
                $order_item["position"] = $pos;
                $order_item["orderID"] = $orderID;
                $order_item["product"] = $product_details;
                $order_item["prodID"] = $prodID;
                $order_item["photo"] = DBDriver::Get()->escapeString($item_photo);

                $itemID = $order_items->insert($order_item, $db);
                if ($itemID < 1) throw new Exception("Unable to insert order item: " . $db->getError());

                $inventory_update = array("stock_amount" => ($item["stock_amount"] - $qty), "order_counter" => ($item["order_counter"] + 1));
                if (!$inventory->update($piID, $inventory_update, $db)) throw new Exception("Unable to update inventory statistics: " . $db->getError());

                $pos++;
            }

            $db->commit();
            $cart->clearCart();
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
