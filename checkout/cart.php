<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");
include_once("store/utils/cart/Cart.php");
include_once("store/beans/SellableProducts.php");

$page = new CheckoutPage();
$page->modify_enabled = TRUE;


$cart = Cart::Instance();

if (isset($_GET["clear"])) {
    $cart->clear();
    $cart->store();
    header("Location: cart.php");
    exit;
}

$num = -1;
$piID = -1;

if (isset($_GET["piID"])) {
    $piID = (int)$_GET["piID"];

    $bean = new SellableProducts();
    $qry = $bean->query("piID");
    $qry->select->limit = " 1 ";

    $exception = null;
    try {
        $num = $qry->exec();
    }
    catch (Exception $e) {
        $exception = $e;
        throw $e;
    }
    if ($num<1 || !is_null($exception)) {
        $cart->remove($piID);
        $cart->store();

        Session::SetAlert(tr("Продуктът не е достъпен за продажба"));
        header("Location:cart.php");
        exit;
    }

}

//client adds product item to cart (lands from product details page)
if (isset($_GET["add"])) {

    //increment amount
    if ($cart->contains($piID)) {
        $item = $cart->get($piID);
        $item->increment();
    }
    else {
        try {
            $result = $qry->nextResult();
            $item = new CartItem($result->get("piID"), $result->get("sell_price"));
            $cart->addItem($item);
        }
        catch (Exception $e) {
            debug("Error adding product to cart: ".$e->getMessage());
        }
    }
    $cart->store();
    header("Location: cart.php");
    exit;
//manage stock amount is disabled
//        if ($item["stock_amount"] < 1) {
//            Session::SetAlert(tr("Съжаляваме в момента няма наличност от този артикул"));
//            header("Location: " . LOCAL . "/details.php?prodID=$prodID&piID=$piID");
//            exit;
//        }
//        if ($item["stock_amount"] - $page->getCart()->getItemQty($piID) - 1 < 0) {
//            Session::SetAlert(tr("Няма повече наличност от този артикул"));
//        }
//        else {
//        }
}
//client increase product item amount from the + button
else if (isset($_GET["increment"])) {
    if ($cart->contains($piID)) {
        $item = $cart->get($piID);
        $item->increment();
        $cart->store();
        header("Location: cart.php");
        exit;
    }
}
//client decrease product item amount from the - button
else if (isset($_GET["decrement"])) {
    if ($cart->contains($piID)) {
        $item = $cart->get($piID);
        $item->decrement();
        if ($item->getQuantity()<1) {
            $cart->remove($piID);
        }
        $cart->store();
        header("Location: cart.php");
        exit;
    }
}
else if (isset($_GET["remove"])) {
    $cart->remove($piID);
    $cart->store();
    header("Location: cart.php");
    exit;
}



$page->startRender();

$page->setTitle(tr("Съдържание на кошницата"));

/**
 * 1. cart.php
 * 2. customer.php
 * 3. chooser courier
 * 4. choose destination address option (personal, office)
 * 5. confirm
 */

echo "<h1 class='Caption'>" . $page->getTitle() . "</h1>";

$page->drawCartItems();

if ($page->total) {
    $action = $page->getAction(CheckoutPage::NAV_LEFT);
    $action->setTitle(tr("Изпразни кошницата"));
    $action->setClassName("empty");
    $action->getURLBuilder()->buildFrom("cart.php?clear");
}

$action = $page->getAction(CheckoutPage::NAV_CENTER);
$action->setTitle(tr("Продължи пазаруването"));
$action->setClassName("continue_shopping");
$href = Session::Get("shopping.list", LOCAL."/products/list.php");
$action->getURLBuilder()->buildFrom($href);

if ($page->total) {
    $action = $page->getAction(CheckoutPage::NAV_RIGHT);
    $action->setTitle(tr("Каса"));
    $action->setClassName("checkout");
    $action->getURLBuilder()->buildFrom("customer.php");
}

$page->renderNavigation();

Session::set("checkout.navigation.back", $page->getPageURL());

$page->finishRender();

?>
