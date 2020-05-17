<?php
include_once("session.php");

include_once("class/mailers/OrderConfirmationMailer.php");

include_once("class/pages/CheckoutPage.php");

include_once("class/utils/OrderProcessor.php");

include_once("class/mailers/OrderConfirmationAdminMailer.php");
include_once("class/mailers/OrderErrorAdminMailer.php");

$page = new CheckoutPage();

$page->ensureClient();

$orderID = 0;
if (isset($_GET["orderID"])) {
    $orderID = (int)$_GET["orderID"];
}

$page->startRender();

$page->setTitle(tr("Поръчката е завършена"));

echo "<div class='caption'>" . tr("Поръчката е завършена") . "</div>";
echo tr("Номер на поръчката") . ": " . $orderID;
echo "<BR><BR>";
echo tr("Благодарим Ви че пазарувахте при нас");
echo "<BR><BR>";
echo tr("Ще се свържем с Вас на посочения e-mail с детйли относно Вашата поръчка");
echo "<BR><BR>";
echo tr("Можете да разгледате поръчките си от секция - ") . "<a href='" . LOCAL . "/account/'>" . tr("Клиенти") . "</a>";
echo "<BR><BR>";

$page->finishRender();
?>
