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
    $editID = $row[$cab->key()];
    $form->loadBeanData($editID, $cab);
}

$proc = new ClientAddressFormProcessor();
$proc->setEditID($editID);
$proc->setUserID($page->getUserID());
$proc->setBean($cab);

$frend = new FormRenderer($form);
$frend->setName("ClientAddress");

$form->setProcessor($proc);

$proc->process($form);

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    Session::SetAlert(tr("Вашият адрес беше успешно променен"));
    header("Location: registered_address.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}

$page->startRender();

$page->setPreferredTitle(tr("Регистриран адрес"));
echo "<div class='caption'>" . $page->getPreferredTitle() . "</div>";


$frend->render();

$page->finishRender();
?>
