<?php
include_once("components/Component.php");
include_once("class/utils/Cart.php");
include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductInventoryBean.php");

class CartComponent extends Component implements IHeadContents
{
    protected $cart = NULL;
    protected $heading_text = "";
    protected $modify_enabled = TRUE;

    protected $total = 0.0;
    protected $order_total = 0.0;
    protected $delivery_price = NULL;

    /**
     * Products
     * @var ProductsBean
     */
    protected $products;

    /**
     * Product inventories
     * @var ProductInventoryBean
     */
    protected $inventory;

    /**
     * color series
     * @var ProductColorPhotosBean
     */
    protected $color_photos;

    //
    /**
     * product gallery photos (for non color serires)
     * @var ProductPhotosBean
     */
    protected $product_photos;

    /**
     * @var ImagePopup
     */
    protected $image_popup;

    public function __construct()
    {
        parent::__construct();

        $this->inventory = new ProductInventoryBean();
        $this->color_photos = new ProductColorPhotosBean();
        $this->products = new ProductsBean();
        $this->product_photos = new ProductPhotosBean();
        $this->image_popup = new ImagePopup();
        $this->image_popup->setPhotoSize(100, 100);

    }

    public function requiredStyle() : array
    {
        $arr = parent::requiredStyle();
        $arr[] = LOCAL . "/css/CartComponent.css";
        return $arr;
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

        $this->image_popup->setAttribute("href", LOCAL . "/details.php?prodID=$prodID&piID=$piID");

        $pclrID = (int)$item["pclrID"];

        $pclrpID = -1;

        if ($pclrID > 0) {
            $pclrpID = $this->color_photos->getFirstPhotoID($pclrID);

            if ($pclrpID > 0) {
                $this->image_popup->setID($pclrpID);
                $this->image_popup->setBeanClass(get_class($this->color_photos));
            }
        }
        else {
            $ppID = $this->product_photos->getFirstPhotoID($prodID);
            if ($ppID > 0) {
                $this->image_popup->setID($ppID);
                $this->image_popup->setBeanClass(get_class($this->product_photos));
            }
        }

        $this->image_popup->render();

        echo "</td>";

        echo "<td field='product_model'>";
        //         trbean($prodID, "product_name", $product, $this->products);
        echo $product["product_name"] . "<BR>" . tr("Цвят") . ": " . $item["color"] . "<BR>" . tr("Размер") . ": " . $item["size_value"] . "<BR>" . tr("Код") . ": " . $piID . "-" . $prodID;

        echo "</td>";

        echo "<td field='qty'>";
        echo "<label>" . tr("Количество") . ": </label>";
        //             echo "<div class='qty'>";
        if ($this->modify_enabled) {
            echo "<a class='qty_adjust minus' href='cart.php?removeItem&piID=$piID'>&#8854;</a>";
        }
        echo "<span class='cart_qty'>" . $qty . "</span>";
        if ($this->modify_enabled) {
            echo "<a class='qty_adjust plus' href='cart.php?addItem&piID=$piID'>&#8853;</a>";
        }
        //             echo "</div>";
        echo "</td>";

        echo "<td field='price'>";
        echo "<label>" . tr("Цена") . ": </label>";
        //                 $price = $currency_rates->getPrice($item["sell_price"]);
        //                 echo sprintf("%0.2f ".$price["symbol"] , $price["price_value"]);
        $price = $item["price"];

        echo "<span>" . formatPrice($price) . "</span>";

        echo "</td>";

        echo "<td field='line-total'>";
        echo "<label>" . tr("Общо") . ": </label>";
        //                 $line_total = ($qty * (float)$price["price_value"]);
        //                 echo sprintf("%0.2f ".$price["symbol"], $line_total );
        $line_total = ($qty * (float)$price);
        echo "<span>" . formatPrice($line_total) . "</span>";

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
        <th colspan=2 field='product'>" . tr("Продукт") . "</th>
        <th field='qty'>" . tr("Количество") . "</th>
        <th field='price'>" . tr("Ед. цена") . "</th>
        <th field='line_total'>" . tr("Общо") . "</th>
        ";
        echo "</tr>";

        //global $products, $photos, $currency_rates;

        $total = 0;

        if (count($items) == 0) {
            echo "<tr>";
            echo "<td colspan=6 field='cart_empty'>";
            echo tr("Вашата кошница е празна");
            echo "</td>";
            echo "</tr>";
        }
        else {

            $items_listed = 0;
            $num_items_total = 0;

            foreach ($items as $piID => $qty) {

                $items_listed++;

                echo "<tr label='item'>";
                $line_total = $this->renderCartItem($items_listed, $piID, $qty);

                $total += $line_total;

                $num_items_total += ($qty);

                echo "</tr>";

            }

        }

        $order_total = $total;

        if ($total > 0) {
            $this->total = $total;

            echo "<tr label='summary-total'>";
            //             echo "<td colspan=4 rowspan=3 field='items_total'>";
            //             echo $num_items_total." ";
            //             echo ($num_items_total>1)? tr("Продукта") : tr("Продукт");
            //             echo "</td>";

            echo "<td colspan=5 class='label amount_total'>";
            echo tr("Продукти общо") . ": ";
            echo "</td>";

            echo "<td class='value amount_total'>";
            echo formatPrice($total);
            echo "</td>";

            echo "</tr>";

            $config = ConfigBean::Factory();
            $config->setSection("delivery_prices");

            if ($this->cart->getDeliveryType() != NULL) {

                echo "<tr label='summary-delivery'>";

                echo "<td colspan=5 class='label delivery' >";
                echo tr("Доставка") . ": ";
                echo "</td>";

                echo "<td class='value delivery'>";
                $delivery_price = $config->get($this->cart->getDeliveryType());
                $this->deliver_price = $delivery_price;

                //              $price = $currency_rates->getPrice($delivery_price);
                $order_total = $order_total + $delivery_price;
                echo formatPrice($delivery_price);
                echo "</td>";

                echo "</tr>";

                echo "<tr label='summary-order-total'>";

                echo "<td colspan=5 class='label order_total'>";
                echo tr("Поръчка общо") . ": ";
                echo "</td>";

                echo "<td class='value order_total'>";
                echo formatPrice($order_total);
                echo "</td>";

                echo "</tr>";

            }

        }
        echo "</table>";

        $this->total = $total;
        $this->order_total = $order_total;

    }

}
