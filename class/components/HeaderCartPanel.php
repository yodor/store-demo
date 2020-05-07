<?php
include_once("components/Component.php");
include_once("class/Cart.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/CurrencyRatesBean.php");

class HeaderCartPanel extends Component
{


    public function renderImpl()
    {
        echo "<a href='" . SITE_ROOT . "checkout/cart.php'>";

        echo "<div class='cell icon'>";
        echo "<div class='cart_icon'></div>";
        echo "</div>";

        echo "<div class='cell contents'>";

        $cart = new Cart();
        global $currency_rates, $products;
        //= SitePage::getInstance()->prods;

        $items = $cart->getItems();
        $num_items = count($items);
        if (count($items) > 0) {

            $total = 0;
            $total_items = 0;
            foreach ($items as $prodID => $qty) {
                $prod_row = $products->getByID($prodID);
                $total_items += (int)$qty;
                $price = $currency_rates->getPrice($prod_row["sell_price"]);
                $total += $price["price_value"] * $qty;
            }
            echo "<span class='cell item_count'>";
            echo tr("Продукти") . ": " . $total_items;
            echo "</span>";

            echo "<span class='cell cart_total'>" . sprintf("%0.2f " . $price["symbol"], $total) . "</span>";


        }
        else {

            echo "<span class='cart_empty'>" . tr("View your cart") . "</span>";
        }

        echo "</div>";

        echo "</a>";

    }

}