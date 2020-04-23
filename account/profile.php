<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");
include_once("class/forms/RegisterClientInputForm.php");
include_once("class/forms/processors/RegisterClientFormProcessor.php");

$page = new AccountPage();

$form = new RegisterClientInputForm();
$form->loadBeanData($page->getUserID(), new UsersBean());


$frend = new FormRenderer();
$frend->setAttribute("name", "RegisterClient");
$frend->setForm($form);

$form->setRenderer($frend);


$proc = new RegisterClientFormProcessor();
$proc->setEditID($page->getUserID());


$proc->processForm($form);

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    Session::Set("alert", tr("Профилът беше променен успешно"));
    header("Location: profile.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::Set("alert", $proc->getMessage());
    $form->loadBeanData($page->getUserID(), new UsersBean());
}

$page->startRender();

$page->setPreferredTitle(tr("Клиентски профил"));

echo "<div class='caption'>" . $page->getPreferredTitle() . "</div>";

$frend->renderForm($form);

echo "<div class='caption'>" . tr("Парола за достъп") . "</div>";

echo "<a class='DefaultButton' href='generate_password.php'>";
echo tr("Генерирай нова парола");
echo "</a>";

$page->finishRender();
?>
