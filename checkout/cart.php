<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductInventoryBean.php");

include_once("class/utils/Cart.php");

$page = new CheckoutPage();
$page->modify_enabled = true;


$products = new ProductsBean();
$inventory = new ProductInventoryBean();

if (isset($_GET["addItem"])) {
    $piID = -1;
    if (isset($_GET["piID"])) {
        $piID = (int)$_GET["piID"];
    }

    try {
        $item = $inventory->getByID($piID);
        $prodID = $item["prodID"];

        if ($item["stock_amount"] < 1) {
            Session::SetAlert(tr("Съжаляваме в момента няма наличност от този артикул"));
            header("Location: " . LOCAL . "details.php?prodID=$prodID&piID=$piID");
            exit;
        }
        if ($item["stock_amount"] - $page->getCart()->getItemQty($piID) - 1 < 0) {
            Session::SetAlert(tr("Няма повече наличност от този артикул"));
        }
        else {
            $page->getCart()->addItem($piID);
            Session::Set("last_added", serialize(array("piID" => $piID, "prodID" => $item["prodID"])));
        }

    }
    catch (Exception $e) {
        //incorrect product id
    }

    header("Location:cart.php");
    exit;
}
else if (isset($_GET["removeItem"])) {

    $piID = -1;
    if (isset($_GET["piID"])) {
        $piID = (int)$_GET["piID"];
    }
    try {
        $item = $inventory->getByID($piID);
        $page->getCart()->removeItem($piID);
    }
    catch (Exception $e) {

    }

    header("Location:cart.php");
    exit;

}
else if (isset($_GET["clearItem"])) {
    $piID = -1;
    if (isset($_GET["piID"])) {
        $piID = (int)$_GET["piID"];
    }
    try {
        $item = $inventory->getByID($piID);
        $page->getCart()->clearItem($piID);
    }
    catch (Exception $e) {

    }

    header("Location:cart.php");
    exit;
}
else if (isset($_GET["clear"])) {

    $page->getCart()->clearCart();

    header("Location:cart.php");
    exit;

}


$page->startRender();

$page->setPreferredTitle(tr("Съдържание на кошницата"));


echo "<div class='caption'>" . $page->getPreferredTitle() . "</div>";

$page->drawCartItems();


if ($page->total) {


    echo "<div class='navigation'>";

    echo "<div class='slot left'>";
    echo "<a href='cart.php?clear'>";
    echo "<img src='" . LOCAL . "images/cart_clear.png'>";
    echo "<div class='DefaultButton checkout_button'  >" . tr("Изпразни кошницата") . "</div>";
    echo "</a>";
    echo "</div>";

    echo "<div class='slot center'>";
    $href = Session::Get("shopping.list");

    echo "<a class='DefaultButton' href='$href'>";
    echo tr("Продължи пазаруването");
    echo "</a>";
    echo "</div>";

    echo "<div class='slot right'>";
    echo "<a href='customer.php'>";
    echo "<img src='" . LOCAL . "images/cart_checkout.png'>";
    echo "<div class='DefaultButton checkout_button'  >" . tr("Каса") . "</div>";
    echo "</a>";
    echo "</div>";

    echo "</div>";
}
else {
    $href = Session::Get("shopping.list");

    echo "<a class='DefaultButton' href='$href'>";
    echo tr("Продължи пазаруването");
    echo "</a>";
}


Session::set("checkout.navigation.back", $page->getPageURL());

$page->finishRender();

?>
