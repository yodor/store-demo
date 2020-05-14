<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");
include_once("class/forms/ClientAddressInputForm.php");
include_once("class/beans/ClientAddressesBean.php");
include_once("class/forms/processors/ClientAddressFormProcessor.php");


$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$cab = new ClientAddressesBean();
$form = new ClientAddressInputForm();


$editID = -1;
$row = $cab->findFieldValue("userID", $page->getUserID());
if ($row) {
    $editID = $row[$cab->key()];
    $form->loadBeanData($editID, $cab);
}

$proc = new ClientAddressFormProcessor();
$proc->setEditID($editID);
$proc->setUserID($page->getUserID());
$proc->setBean($cab);

$frend = new FormRenderer($form);
$frend->getSubmitLine()->setEnabled(false);

$form->setProcessor($proc);

$proc->process($form);

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    //   Session::set("DeliveryDetailsForm", serialize($dform));
    header("Location: confirm.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}


$page->startRender();


$page->setPreferredTitle(tr("Адрес за доставка"));


$page->drawCartItems();


echo "<div class='delivery_details'>";

echo "<div class='caption'>" . tr("Адрес за доставка") . "</div>";


$frend->render();

// $back_url = Session::get("checkout.navigation.back", $page->getPageURL());

echo "<div class='navigation'>";

    echo "<div class='slot left'>";
        echo "<a href='delivery.php'>";
        echo "<img src='".LOCAL."images/cart_edit.png'>";
        echo "<div class='ColorButton checkout_button' >".tr("Назад")."</div>";
        echo "</a>";
    echo "</div>";

    echo "<div class='slot center'>";
    echo "</div>";

    echo "<div class='slot right'>";
        echo "<a href='javascript:document.forms.ClientAddressInputForm.submit();'>";
            echo "<img src='" . LOCAL . "images/cart_checkout.png'>";
            echo "<div class='ColorButton checkout_button'  >" . tr("Продължи") . "</div>";
        echo "</a>";
    echo "</div>";

echo "</div>";


$page->finishRender();
?>
