<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");

include_once("class/forms/InvoiceDetailsInputForm.php");
include_once("class/beans/InvoiceDetailsBean.php");
include_once("class/forms/processors/InvoiceDetailsFormProcessor.php");
include_once("lib/db/DBTransactor.php");


$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$ccb = new InvoiceDetailsBean();
$form = new InvoiceDetailsInputForm();

$editID = -1;
$row = $ccb->findFieldValue("userID", $page->getUserID());
if ($row) {
    $editID = $row[$ccb->getPrKey()];
    $form->loadBeanData($editID, $ccb);
}

$proc = new InvoiceDetailsFormProcessor();
$proc->setEditID($editID);
$proc->setUserID($page->getUserID());
$proc->setBean($ccb);

$frend = new FormRenderer();
$frend->setName("InvoiceDetails");

$form->setRenderer($frend);
$form->setProcessor($proc);
$frend->setForm($form);


$proc->processForm($form, "submit_item");

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    header("Location: confirm.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::set("alert", $proc->getMessage());
}
$page->beginPage();


$page->setPreferredTitle(tr("Детайли за фактуриране"));


$page->drawCartItems();




echo "<div class='item invoice_details'>";

  echo "<div class='caption'>".$page->getPreferredTitle()."</div>";


  $frend->startRender();
  
  $frend->renderImpl();

  echo "<input type=hidden name='submit_item' value='submit'>";

  $frend->finishRender();
  
  
echo "</div>"; //invoice_details




echo "<div class='navigation'>";

    echo "<div class='slot left'>";
        echo "<a href='confirm.php'>";
        echo "<img src='".SITE_ROOT."images/cart_edit.png'>";
        echo "<div class='checkout_button' >".tr("Назад")."</div>";
        echo "</a>";
    echo "</div>";

    echo "<div class='slot center'>";

    echo "</div>";
  
    echo "<div class='slot right'>";
        echo "<a href='javascript:document.forms.InvoiceDetails.submit();'>";
        echo "<img src='".SITE_ROOT."images/cart_checkout.png'>";
        echo "<div class='checkout_button'  >".tr("Продължи")."</div>";
        echo "</a>";
    echo "</div>";


echo "</div>";


$page->finishPage();
?>
