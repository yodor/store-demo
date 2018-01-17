<?php
include_once("session.php");

include_once("class/pages/CheckoutPage.php");


include_once("class/utils/OrderProcessor.php");
include_once("class/mailers/OrderConfirmationMailer.php");
include_once("class/mailers/OrderConfirmationAdminMailer.php");
include_once("class/mailers/OrderErrorAdminMailer.php");

// if (isset($_GET["mailer_test"])) {
//    try {
//         $mailer = new OrderConfirmationMailer(15);
//         $mailer->send();
//     }
//     catch (Exception $em) {
//         echo ("Unable to email confirmation message for order to user: ".$em->getMessage());
//     } 
//     exit;
// }

$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$orderID = -1;
//process order below


try {

    $oproc = new OrderProcessor();
    $orderID = $oproc->createOrder($page->getCart(), $page->getUserID());
    
    try {
        $mailer = new OrderConfirmationMailer($orderID);
        $mailer->send();
    }
    catch (Exception $em) {
        error_log("Unable to email confirmation message for order to user: ".$em->getMessage());
    }
    try {
        $mcopy = new OrderConfirmationAdminMailer($orderID);
        $mcopy->send();
    }
    catch (Exception $em) {
        error_log("Unable to email confirmation message for order to admin: ".$em->getMessage());
    }
    
}
catch (Exception $e) {
    Session::set("alert", tr("Възникна грешка при обработка на Вашата поръчка.")."<BR>".tr("Details").": ".$e->getMessage());
    try {
        $oem = new OrderErrorAdminMailer();
        $oem->send();
    }
    catch (Exception $oeme) {
        error_log("Unable to email order error message to admin: ".$oeme->getMessage());
    }
    header("Location: confirm.php");
    exit;
}


$page->beginPage();

$page->setPreferredTitle(tr("Поръчката е завършена"));

echo "<div class='caption'>".tr("Поръчката е завършена")."</div>";
echo tr("Номер на поръчката").": ".$orderID;
echo "<BR><BR>";
echo tr("Благодарим Ви че пазарувахте при нас");
echo "<BR><BR>";
echo tr("Ще се свържем с Вас на посочения e-mail с детйли относно Вашата поръчка");
echo "<BR><BR>";
echo tr("Можете да разгледата поръчките си от секция клиентските страници.");
echo "<BR><BR>";

$page->finishPage();
?>
