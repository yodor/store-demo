<?php
include_once("session.php");
include_once("store/pages/CheckoutPage.php");

include_once("store/forms/InvoiceDetailsInputForm.php");
include_once("store/beans/InvoiceDetailsBean.php");
include_once("store/forms/processors/InvoiceDetailsFormProcessor.php");
include_once("db/BeanTransactor.php");

$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$ccb = new InvoiceDetailsBean();
$form = new InvoiceDetailsInputForm();
$form->setName("InvoiceDetails");

$editID = -1;
$row = $ccb->getResult("userID", $page->getUserID());
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
$frend->getSubmitLine()->setEnabled(FALSE);

$proc->process($form);

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    header("Location: confirm.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}
$page->startRender();

$page->setTitle(tr("Детайли за фактуриране"));

$page->drawCartItems();

echo "<div class='item invoice_details'>";

echo "<h1 class='Caption'>" . $page->getTitle() . "</h1>";

$frend->render();

echo "</div>"; //invoice_details




$back_url = Session::get("checkout.navigation.back", "cart.php");

$action = $page->getAction(CheckoutPage::NAV_LEFT);
$action->setTitle(tr("Назад"));
$action->setClassName("edit");
$action->getURLBuilder()->buildFrom($back_url);

$action = $page->getAction(CheckoutPage::NAV_RIGHT);
$action->setTitle(tr("Продължи"));
$action->setClassName("checkout");
$action->getURLBuilder()->buildFrom("javascript:document.forms.InvoiceDetails.submit()");

$page->renderNavigation();


$page->finishRender();
?>
