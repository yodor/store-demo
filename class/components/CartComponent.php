<?php
include_once("lib/components/MLTagComponent.php");
include_once("class/utils/Cart.php");

include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductInventoryBean.php");

class CartComponent extends MLTagComponent implements IHeadRenderer
{
    protected $cart = NULL;
    protected $heading_text = "";
    protected $modify_enabled = true;
    
    protected $inventory = NULL;
    protected $photos = NULL;
    protected $products = NULL;
    protected $total = 0.0;
    protected $order_total = 0.0;
    protected $delivery_price = NULL;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->inventory = new ProductInventoryBean();
        $this->photos = new ProductColorPhotosBean();
        $this->products = new ProductsBean();
    }
    
    public function renderScript()
    {}
	
    public function renderStyle()
    {
        echo "<link rel='stylesheet' href='".SITE_ROOT."css/CartComponent.css' type='text/css' >";
        echo "\n";
    }
    
    public function setCart(Cart $cart) 
    {
        $this->cart = $cart;
    }
    
    public function setHeadingText($heading_text)
    {
        $this->heading_text = $heading_text;
    }
    
    public function setModifyEnabled($mode)
    {
        $this->modify_enabled = (int)$mode;
    }
    
    public function getTotal()
    {
        return $this->total;
    }
    
    public function getOrderTotal()
    {
        return $this->order_total;
    }
    
    public function getDeliveryPrice()
    {
        return $this->delivery_price;
    }
    
    protected function renderCartItem($position, $piID, $qty)
    {
        $item = $this->inventory->getByID($piID);
                
        $prodID = $item["prodID"];
        
        $product = $this->products->getByID($prodID);

        //product inventory ID
        echo "<td field='position'>";
        if ($this->modify_enabled) {
            echo "<a class='item_remove' href='cart.php?clearItem&piID=$piID'>&#8855;</a>";
        }
        else {
            echo $position;
        }
        echo "</td>";

        //only one photo here
        echo "<td field='product_photo'>";
        $pclrID = $item["pclrID"];
        
        $pclrpID = $this->photos->getFirstPhotoID($pclrID);
        
        echo $this->photos->getThumb($pclrpID, 100);

        
        echo "</td>";

        echo "<td field='product_model'>";
//         trbean($prodID, "product_name", $product, $this->products);
        echo $product["product_name"]."<BR>".tr("Цвят").": ".$item["color"]."<BR>".tr("Размер").": ".$item["size_value"]."<BR>".tr("Код").": ".$piID."-".$prodID;
        
        echo "</td>";

        echo "<td field='qty'>";
//             echo "<div class='qty'>";
            if ($this->modify_enabled) {
                echo "<a class='qty_adjust minus' href='cart.php?removeItem&piID=$piID'>&#8854;</a>";
            }
            echo "<span class='cart_qty'>".$qty."</span>";
            if ($this->modify_enabled) {
                echo "<a class='qty_adjust plus' href='cart.php?addItem&piID=$piID'>&#8853;</a>";
            }
//             echo "</div>";
        echo "</td>";

        echo "<td field='price'>";

//                 $price = $currency_rates->getPrice($item["sell_price"]);
//                 echo sprintf("%0.2f ".$price["symbol"] , $price["price_value"]);
        $price = $item["price"];
        
        echo "<span>".sprintf("%0.2f лв.", $price)."</span>";
        

        echo "</td>";

        echo "<td field='line-total'>";
//                 $line_total = ($qty * (float)$price["price_value"]);
//                 echo sprintf("%0.2f ".$price["symbol"], $line_total );
        $line_total = ($qty * (float)$price);
        echo "<span>".sprintf("%0.2f лв.", $line_total)."</span>";
  
        echo "</td>";
        
//         echo "<td field='actions'>";
        
//         echo "</td>";

        return $line_total;
        
    }
    
    protected function renderImpl() 
    {
        
        $items = $this->cart->getItems();


        if ($this->heading_text) {
            echo "<div class='heading'>";
            echo $this->heading_text;
            echo "</div>";
        }



        echo "<table class='cart_view'>";

        echo "<tr label='heading'>";
        echo "
        <th>#</th>
        <th colspan=2 field='product'>".tr("Продукт")."</th>
        <th field='qty'>".tr("Количество")."</th>
        <th field='price'>".tr("Ед. цена")."</th>
        <th field='line_total'>".tr("Общо")."</th>
        ";
        echo "</tr>";


//global $products, $photos, $currency_rates;
        
        $total = 0;
        
        
        if (count($items)==0){
            echo "<tr>";
            echo "<td colspan=6 field='cart_empty'>";
            echo tr("Вашата кошница е празна");
            echo "</td>";
            echo "</tr>";
        }
        else {

            $items_listed = 0;
            $num_items_total = 0;

            foreach ($items as $piID=>$qty){

                $items_listed++;

                echo "<tr label='item'>";
                $line_total = $this->renderCartItem($items_listed, $piID, $qty);
                
                $total+=$line_total;

                $num_items_total += ($qty);
                
                echo "</tr>";

            }

        }



        $order_total = $total;
        
        if ($total>0) {
            $this->total = $total;

            echo "<tr label='summary-total'>";
//             echo "<td colspan=4 rowspan=3 field='items_total'>";
//             echo $num_items_total." ";
//             echo ($num_items_total>1)? tr("Продукта") : tr("Продукт");
//             echo "</td>";

            echo "<td colspan=5 class='label amount_total'>";
            echo tr("Сума").": ";
            echo "</td>";

            echo "<td class='value amount_total'>";
//          echo sprintf("%0.2f ".$price["symbol"], $total );
            echo sprintf("%0.2f лв.", $total );
            echo "</td>";

            
            echo "</tr>";

            
            $config = ConfigBean::factory();
            $config->setSection("delivery_prices");
            
            iF ($this->cart->getDeliveryType() != null){
                echo "<tr label='summary-delivery'>";
                
                echo "<td colspan=5 class='label delivery' >";	
                echo tr("Доставка").": ";
                echo "</td>";
                
                echo "<td class='value delivery'>";

                $delivery_price = $config->getValue($this->cart->getDeliveryType());
                $this->deliver_price = $delivery_price;
                
//              $price = $currency_rates->getPrice($delivery_price);
                $order_total = $order_total + $delivery_price;
                echo sprintf("%0.2f лв.", $delivery_price);

                echo "</td>";

                echo "</tr>";
                
                
                echo "<tr label='summary-order-total'>";
            
                echo "<td colspan=5 class='label order_total'>";	
                    echo tr("Поръчка общо").": ";
                echo "</td>";
                echo "<td class='value order_total'>";

    //              echo sprintf("%0.2f ".$price["symbol"] , $price["price_value"] + $total);
                    echo sprintf("%0.2f лв.", $order_total);

                echo "</td>";

                echo "</tr>";
                
            }

            


        }
        echo "</table>";
       
        
        $this->total = $total;
        $this->order_total = $order_total;
        
    }

}
