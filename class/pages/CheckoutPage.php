<?php
include_once("class/pages/StorePage.php");
include_once("class/components/CartComponent.php");
include_once("components/ClosureComponent.php");
include_once("components/Action.php");

class CheckoutPage extends StorePage
{

    public $modify_enabled = FALSE;
    public $total = 0.0;

    protected $ccmp = NULL;

    const NAV_LEFT = "left";
    const NAV_CENTER = "center";
    const NAV_RIGHT = "right";

    protected $navigation = NULL;

    protected $nav_actions = array();

    public function __construct()
    {
        parent::__construct();

        $this->ccmp = new CartComponent();

        $this->addCSS(LOCAL . "/css/checkout.css");

        $this->navigation = new Container();
        $this->navigation->setClassName("navigation");

        $this->nav_actions[CheckoutPage::NAV_LEFT] = new Action();
        $this->nav_actions[CheckoutPage::NAV_CENTER] = new Action();
        $this->nav_actions[CheckoutPage::NAV_RIGHT] = new Action();

        $render = function(ClosureComponent $cmp)  {
            $action = $this->nav_actions[$cmp->getName()];
            if (!$action instanceof Action) return;
            if ($action->getTitle()) {
                ob_start();
                echo "<span class='icon'></span>";
                echo "<div class='ColorButton checkout_button' >" . tr($action->getTitle()) . "</div>";
                $contents = ob_get_contents();
                ob_end_clean();
                $action->setContents($contents);
                $action->render();
            }
        };

        $left_space = new ClosureComponent($render);
        $left_space->setClassName("slot left");
        $left_space->setName(CheckoutPage::NAV_LEFT);
        $this->navigation->append($left_space);

        $center_space = new ClosureComponent($render);
        $center_space->setClassName("slot center");
        $center_space->setName(CheckoutPage::NAV_CENTER);
        $this->navigation->append($center_space);

        $right_space = new ClosureComponent($render);
        $right_space->setClassName("slot right");
        $right_space->setName(CheckoutPage::NAV_RIGHT);
        $this->navigation->append($right_space);
    }

    public function getAction(string $name) : Action
    {
        return $this->nav_actions[$name];
    }

    public function getNavigation() : Container
    {
        return $this->navigation;
    }

    public function drawCartItems($heading_text = "")
    {
        //$this->ccmp->setCart($this->cart);
        $this->ccmp->setHeadingText($heading_text);
        $this->ccmp->setModifyEnabled($this->modify_enabled);
        $this->ccmp->render();
        $this->total = $this->ccmp->getOrderTotal();
    }

    public function ensureCartItems()
    {
        if (Cart::Instance()->itemsCount() < 1) {
            Session::SetAlert(tr("Вашата кошница е празна"));
            header("Location: cart.php");
            exit;
        }
    }

    public function ensureClient()
    {
        if (!$this->context) {
            Session::SetAlert(tr("Изисква регистрация"));
            header("Location: cart.php");
            exit;
        }
    }


    public function renderNavigation()
    {
        $this->navigation->render();
    }
}

?>
