<?php
include_once("class/pages/StorePage.php");
include_once("lib/beans/ConfigBean.php");

include_once("lib/auth/UserAuthenticator.php");
include_once("lib/beans/UsersBean.php");

include_once("lib/utils/MainMenu.php");
include_once("lib/utils/MenuItem.php");

class AccountPage extends StorePage
{
    protected $account_menu = NULL;

    public function __construct($need_auth = true)
    {


        if ($need_auth) {

            $this->auth = new UserAuthenticator();

        }

        parent::__construct();

        $this->account_menu = new MainMenu();

        $items = array();

        $items[] = new MenuItem("История на поръчките", "orders.php");
        $items[] = new MenuItem("Регистриран адрес", "registered_address.php");
        $items[] = new MenuItem("Детайли за фактуриране", "invoice_details.php");
        $items[] = new MenuItem("Редакция на профил", "profile.php");
        $items[] = new MenuItem("Изход", "logout.php");

        $this->account_menu->setMenuItems($items);

        $this->addCSS(SITE_ROOT."css/account.css");
    }

    public function startRender()
    {

        parent::startRender();

        echo "<div class='columns'>";

        echo "<div class='column left'>";

        //render menu items
        if ($this->context) {
            echo "<div class='account_menu'>";
            $menu_items = $this->account_menu->getMenuItems();
            foreach ($menu_items as $idx => $item) {
                echo "<a class='item' href='" . $item->getHref() . "'>";
                echo $item->getTitle();
                echo "</a>";
            }
            echo "</div>";
        }
        echo "</div>";

        echo "<div class='column right'>";

    }

    public function finishRender()
    {
        echo "</div>";//column right
        echo "</div>";//columns
        parent::finishRender();
    }

}

?>
