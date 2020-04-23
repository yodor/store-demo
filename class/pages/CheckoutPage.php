<?php
include_once("class/pages/StorePage.php");
include_once("class/components/CartComponent.php");


class CheckoutPage extends StorePage
{

    public $modify_enabled = false;
    public $total = 0.0;

    protected $ccmp = NULL;

    public function __construct()
    {

        parent::__construct();


        $this->ccmp = new CartComponent();
    }


    protected function dumpCSS()
    {
        parent::dumpCSS();

        echo "<link rel='stylesheet' href='" . SITE_ROOT . "css/checkout.css?ver=1.0' type='text/css'>";

    }


    public function beginPage()
    {
        parent::startRender();

        //         echo "<div class='column left'>";
        //$this->renderCategoryTree();
        //$this->renderNewsItems();

        //         echo "</div>";

        //         echo "<div class='column cart'>";

    }

    public function finishPage()
    {
        //         echo "</div>";//column cart
        parent::finishRender();
    }

    public function drawCartItems($heading_text = "")
    {

        $this->ccmp->setCart($this->cart);
        $this->ccmp->setHeadingText($heading_text);
        $this->ccmp->setModifyEnabled($this->modify_enabled);
        $this->ccmp->render();
        $this->total = $this->ccmp->getOrderTotal();
    }

    public function ensureCartItems()
    {

        $items = $this->cart->getItems();

        if (count($items) < 1) {
            Session::Set("alert", tr("Вашата кошница е празна"));
            header("Location: cart.php");
            exit;
        }
    }

    public function ensureClient()
    {

        if (!$this->is_auth) {
            Session::Set("alert", tr("Изисква регистрация"));
            header("Location: cart.php");
            exit;
        }
    }
}

?>
