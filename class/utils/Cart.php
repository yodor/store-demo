<?php

class Cart1
{
    private $items = array();

    //reference enum delivery_type in orders 
    //TODO make this available from admin to set delivery price
    const DELIVERY_USERADDRESS = "UserAddress";
    const DELIVERY_EKONTOFFICE = "EkontOffice";

    protected $delivery_options = NULL;

    protected $delivery_type = NULL;
    protected $note = "";

    protected $require_invoice = FALSE;

    public function __construct()
    {
        $this->items = array();
        $this->total = 0;
        $this->loadCart();
        $this->delivery_options = array(Cart::DELIVERY_USERADDRESS, Cart::DELIVERY_EKONTOFFICE);
    }

    public function setDeliveryType(?string $delivery_type)
    {
         if (!in_array($delivery_type, $this->delivery_options) || !is_null($delivery_type)) {
             $this->delivery_type = NULL;
         }

        $this->delivery_type = $delivery_type;
        $this->storeCart();
    }

    public function getDeliveryType()
    {
        return $this->delivery_type;
    }

    public static function getDeliveryTypeText($delivery_type)
    {
        if (strcmp($delivery_type, Cart::DELIVERY_USERADDRESS) == 0) {
            return "Доставка до регистриран адрес";
        }
        else if (strcmp($delivery_type, Cart::DELIVERY_EKONTOFFICE) == 0) {
            return "Доставка до офис на 'Eконт'";
        }
        else {
            return "Непозант";
        }
    }

    public function setNote($text)
    {
        $this->note = mb_substr($text, 0, 255);
        $this->storeCart();
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setRequireInvoice($mode)
    {
        $this->require_invoice = (int)$mode;
        $this->storeCart();
    }

    public function getRequireInvoice()
    {
        return $this->require_invoice;
    }

    private function loadCart()
    {
        if (isset($_SESSION["cart"])) {
            @$cart = unserialize($_SESSION["cart"]);
            if (is_array($cart)) {
                $this->items = $cart["items"];
                $this->delivery_type = $cart["delivery_type"];
                $this->note = $cart["note"];
                $this->require_invoice = $cart["require_invoice"];

            }

        }
    }

    private function storeCart()
    {
        $cart = array();
        $cart["items"] = $this->items;
        $cart["delivery_type"] = $this->delivery_type;
        $cart["note"] = $this->note;
        $cart["require_invoice"] = $this->require_invoice;
        $_SESSION["cart"] = serialize($cart);
    }

    public function addItem($piID, $qnt = 1)
    {
        if (!isset($this->items[$piID])) {
            $this->items[$piID] = 0;
        }
        $this->items[$piID] += $qnt;

        $this->storeCart();
    }

    public function removeItem($piID, $qnt = 1)
    {

        if (isset($this->items[$piID])) {

            $qt = $this->items[$piID];

            if ($qnt === 0) {
                $qnt = $qt;
            }

            if ($qnt > 0 && $qnt <= $qt) {
                $qt -= $qnt;
                $this->items[$piID] = $qt;
            }

            if ($qt === 0) {
                unset($this->items[$piID]);
            }

            $this->storeCart();

        }
        else {
            throw new Exception("Продуктът не беше намерен");
        }

    }

    public function clearItem($piID)
    {

        if (isset($this->items[$piID])) {

            unset($this->items[$piID]);

            $this->storeCart();

        }
        else {
            throw new Exception("Продуктът не беше намерен");
        }

    }

    public function getItems()
    {
        return $this->items;
    }

    public function getItemQty($piID)
    {
        $qty = 0;
        if (isset($this->items[$piID])) {
            $qty = $this->items[$piID];
        }
        return $qty;
    }

    public function getItemCount()
    {
        $num_items = 0;
        foreach ($this->items as $piID => $qty) {
            $num_items = $num_items + $qty;
        }
        return $num_items;
    }

    public function clearCart()
    {
        $this->items = array();
        $this->note = "";
        $this->require_invoice = FALSE;
        $this->delivery_type = "";
        $this->storeCart();
    }

}

?>
