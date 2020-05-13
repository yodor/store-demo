<?php
// $GLOBALS["DEBUG_OUTPUT"] = 1;
$cdir = dirname(__FILE__);
$realpath = realpath($cdir . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR);
include_once($realpath . "/session.php");

include_once("buttons/StyledButton.php");
StyledButton::setDefaultClass("admin_button");


$all_roles = array(

    "ROLE_STORE_MENU", "ROLE_ORDERS_MENU", "ROLE_CLIENTS_MENU", "ROLE_CONTENT_MENU", "ROLE_SETTINGS_MENU");


foreach ($all_roles as $key => $val) {
    define($val, $val);
}


include_once("utils/MenuItem.php");
$admin_menu = array();

$admin_menu[] = new MenuItem("Магазин", ADMIN_LOCAL . "store/index.php", "class:icon_store");

$admin_menu[] = new MenuItem("Поръчки", ADMIN_LOCAL . "orders/index.php", "class:icon_orders");

$admin_menu[] = new MenuItem("Клиенти", ADMIN_LOCAL . "clients/index.php", "class:icon_clients");

$admin_menu[] = new MenuItem("Съдържание", ADMIN_LOCAL . "content/index.php", "class:icon_content");

$admin_menu[] = new MenuItem("Настройки", ADMIN_LOCAL . "settings/index.php", "class:icon_settings");


?>
