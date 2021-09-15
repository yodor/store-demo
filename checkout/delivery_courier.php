<?php
include_once("session.php");
include_once("store/pages/CheckoutPage.php");

$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$cart = Cart::Instance();

$courier = $cart->getDelivery()->getSelectedCourier();

include_once("courier_".$courier->getID().".php");

?>
