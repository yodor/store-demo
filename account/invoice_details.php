<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");
include_once("class/beans/InvoiceDetailsBean.php");
include_once("class/forms/InvoiceDetailsInputForm.php");
include_once("class/forms/processors/InvoiceDetailsFormProcessor.php");

$page = new AccountPage();

$ccb = new InvoiceDetailsBean();
$form = new InvoiceDetailsInputForm();

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
$frend->setName("InvoiceDetails");

$form->setProcessor($proc);

$proc->process($form);

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    Session::SetAlert(tr("Детайлите за фактуриране бяха променени успешно"));
    header("Location: invoice_details.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}

$page->startRender();

$page->setPreferredTitle(tr("Детайли за фактуриране"));
echo "<div class='caption'>" . $page->getPreferredTitle() . "</div>";

$frend->render();

$page->finishRender();
?>
