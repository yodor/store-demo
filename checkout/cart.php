<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");
include_once("store/utils/cart/Cart.php");
include_once("store/beans/SellableProducts.php");
include_once("store/utils/cart/ICartListener.php");

$page = new CheckoutPage();
$page->modify_enabled = TRUE;

$cart = Cart::Instance();

$num = -1;
$piID = -1;

try {

    $redirect = FALSE;

    if (isset($_GET["piID"])) {
        $piID = (int)$_GET["piID"];

        $bean = new SellableProducts();
        $query = $bean->query("piID");
        $query->select->where()->add("piID", $piID);
        $query->select->limit = " 1 ";

        $num = $query->exec();

        if ($num < 1) {
            throw new Exception("SellableProducts returned non positive result count");
        }

        //client add product to cart
        else if (isset($_GET["add"])) {

            if ($result = $query->nextResult()) {

                $stock_amount = $result->get("stock_amount");
                if ($stock_amount < 1) throw new Exception("SellableProducts returned stock amount low");

                $item = new CartItem($result->get("piID"), $result->get("sell_price"));
                $cart->addItem($item);
                $redirect = TRUE;
            }
            else {
                throw new Exception("Unable to fetch required SellableProduct to do add");
            }
        }
        //client increase product item amount from the + button
        else if (isset($_GET["increment"])) {
            $cart->increment($piID);
            $redirect = TRUE;
        }
        //client decrease product item amount from the - button
        else if (isset($_GET["decrement"])) {
            $cart->decrement($piID);
            $redirect = TRUE;
        }
        //client remove product item from the x button
        else if (isset($_GET["remove"])) {
            $cart->remove($piID);
            $redirect = TRUE;
        }
    }
    else if (isset($_GET["clear"])) {
        $cart->clear();
        $redirect = true;
    }

    if ($redirect) {
        $cart->store();
        header("Location: cart.php");
        exit;
    }
}
catch (Exception $e) {
    Session::SetAlert("Грешка: " . $e->getMessage());
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