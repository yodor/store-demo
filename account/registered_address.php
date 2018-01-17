<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");
include_once("class/forms/ClientAddressInputForm.php");
include_once("class/beans/ClientAddressesBean.php");
include_once("class/forms/processors/ClientAddressFormProcessor.php");

$page = new AccountPage();

$cab = new ClientAddressesBean();
$form = new ClientAddressInputForm();

$editID = -1;
$row = $cab->findFieldValue("userID", $page->getUserID());
if ($row) {
    $editID = $row[$cab->getPrKey()];
    $form->loadBeanData($editID, $cab);
}

$proc = new ClientAddressFormProcessor();
$proc->setEditID($editID);
$proc->setUserID($page->getUserID());
$proc->setBean($cab);

$frend = new FormRenderer();
$frend->setName("ClientAddress");

$form->setRenderer($frend);
$form->setProcessor($proc);
$frend->setForm($form);


$proc->processForm($form);

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    Session::set("alert", tr("Вашият адрес беше успешно променен"));
    header("Location: registered_address.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::set("alert", $proc->getMessage());
}

$page->beginPage();

$page->setPreferredTitle(tr("Регистриран адрес"));
echo "<div class='caption'>".$page->getPreferredTitle()."</div>";


$frend->renderForm($form);

$page->finishPage();
?>
