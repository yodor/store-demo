<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");
include_once("class/forms/RegisterClientInputForm.php");
include_once("class/forms/processors/RegisterClientFormProcessor.php");

$page = new AccountPage();

$form = new RegisterClientInputForm();

$form->loadBeanData($page->getUserID(), new UsersBean());

$frend = new FormRenderer($form);
$frend->setLayout(FormRenderer::FIELD_VBOX);
$frend->getSubmitButton()->setContents("Update");

$proc = new RegisterClientFormProcessor();
$proc->setEditID($page->getUserID());

$proc->process($form);

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    Session::SetAlert(tr("Профилът беше променен успешно"));
    header("Location: profile.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
    $form->loadBeanData($page->getUserID(), new UsersBean());
}

$page->startRender();

$page->setTitle(tr("Клиентски профил"));

echo "<div class='caption'>" . $page->getTitle() . "</div>";

$frend->render();

echo "<div class='caption'>" . tr("Парола за достъп") . "</div>";

echo "<a class='ColorButton' href='generate_password.php'>";
echo tr("Генерирай нова парола");
echo "</a>";

$page->finishRender();
?>
