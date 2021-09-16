<?php
// $GLOBALS["DEBUG_OUTPUT"] = 1;
if (!isset($info)) {
    $info = pathinfo($_SERVER['SCRIPT_FILENAME']);
}
$info = pathinfo($info["dirname"]);
$parent = $info["dirname"];
include_once($parent."/session.php");

include_once("utils/MenuItem.php");
$admin_menu = array();

$admin_menu[] = new MenuItem("Магазин", ADMIN_LOCAL . "/store/index.php", "class:icon_store");

$admin_menu[] = new MenuItem("Поръчки", ADMIN_LOCAL . "/orders/index.php", "class:icon_orders");

$admin_menu[] = new MenuItem("Клиенти", ADMIN_LOCAL . "/clients/index.php", "class:icon_clients");

$admin_menu[] = new MenuItem("Съдържание", ADMIN_LOCAL . "/content/index.php", "class:icon_content");

$admin_menu[] = new MenuItem("Настройки", ADMIN_LOCAL . "/settings/index.php", "class:icon_settings");

$admin_menu[] = new MenuItem("Контакти", ADMIN_LOCAL . "/contact_requests/list.php", "class:icon_settings");

?>
