<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");

include_once("class/forms/InvoiceDetailsInputForm.php");
include_once("class/beans/InvoiceDetailsBean.php");
include_once("class/forms/processors/InvoiceDetailsFormProcessor.php");
include_once("db/BeanTransactor.php");


$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$ccb = new InvoiceDetailsBean();
$form = new InvoiceDetailsInputForm();
$form->setName("InvoiceDetails");

$editID = -1;
$row = $ccb->findFieldValue("userID", $page->getUserID());
if ($row) {
    $editID = $row[$ccb->key()];
    $form->loadBeanData($editID, $ccb);
}

$proc = new InvoiceDetailsFormProcessor();
$proc->setEditID($editID);
$proc->setUserID($page->getUserID());
$proc->setBean($ccb);

$frend = new FormRenderer($form);
$frend->setClassName("InvoiceDetails");
$frend->getSubmitLine()->setEnabled(false);

$proc->process($form);

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    header("Location: confirm.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}
$page->startRender();


$page->setPreferredTitle(tr("Детайли за фактуриране"));


$page->drawCartItems();


echo "<div class='item invoice_details'>";

echo "<div class='caption'>" . $page->getPreferredTitle() . "</div>";


$frend->render();


echo "</div>"; //invoice_details


echo "<div class='navigation'>";

echo "<div class='slot left'>";
echo "<a href='confirm.php'>";
echo "<img src='" . LOCAL . "images/cart_edit.png'>";
echo "<div class='ColorButton checkout_button' >" . tr("Назад") . "</div>";
echo "</a>";
echo "</div>";

echo "<div class='slot center'>";
echo "</div>";

echo "<div class='slot right'>";
echo "<a href='javascript:document.forms.InvoiceDetails.submit();'>";
echo "<img src='" . LOCAL . "images/cart_checkout.png'>";
echo "<div class='ColorButton checkout_button'  >" . tr("Продължи") . "</div>";
echo "</a>";
echo "</div>";


echo "</div>"; //navigation


$page->finishRender();
?>
