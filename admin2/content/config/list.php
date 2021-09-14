<?php
include_once("session.php");
include_once("templates/admin/ConfigEditorPage.php");

include_once("class/forms/StoreConfigForm.php");

$cmp = new ConfigEditorPage();
$cmp->setConfigSection("store_config");
$cmp->setForm(new StoreConfigForm());

$cmp->getPage()->navigation()->clear();
$cmp->render();

?>