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
            parent::__construct(new UserAuthenticator(SITE_ROOT . "account/login.php"));
        }
        else {
            parent::__construct();
        }


        $this->account_menu = new MainMenu();

        $items = array();


        $items[] = new MenuItem("История на поръчките", "orders.php");
        $items[] = new MenuItem("Регистриран адрес", "registered_address.php");
        $items[] = new MenuItem("Детайли за фактуриране", "invoice_details.php");
        $items[] = new MenuItem("Редакция на профил", "profile.php");
        $items[] = new MenuItem("Изход", "logout.php");

        $this->account_menu->setMenuItems($items);

    }

    protected function dumpCSS()
    {
        parent::dumpCSS();

<<<<<<< HEAD
        //         echo "<link rel='stylesheet' href='".SITE_ROOT."lib/css/FormRenderer.css' type='text/css'>";

        echo "<link rel='stylesheet' href='" . SITE_ROOT . "css/account.css?ver=1.0' type='text/css'>";

=======
        echo "<link rel='stylesheet' href='".SITE_ROOT."css/account.css?ver=1.3' type='text/css'>";
        
>>>>>>> origin/master
    }


    public function beginPage()
    {

        parent::startRender();

        echo "<div class='columns'>";

        echo "<div class='column left'>";

        //             if ($this->is_auth) {
        //                 $bean = new UsersBean();
        //                 $fullname = $bean->fieldValue($this->getUserID(), "fullname");
        //                 echo "<div class='welcome caption'>";
        //                 echo tr("Добре дошли, ").$fullname;
        //                 echo "</div>";
        //             }
        //render menu items
        if ($this->is_auth) {
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

    public function finishPage()
    {
        echo "</div>";//column right
        echo "</div>";//columns
        parent::finishRender();
    }

}

?>
