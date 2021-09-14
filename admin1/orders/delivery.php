<?php
include_once("session.php");
include_once("templates/admin/ConfigEditorPage.php");
include_once("class/forms/DeliveryConfigForm.php");

include_once("beans/ConfigBean.php");
include_once("forms/processors/ConfigFormProcessor.php");
include_once("forms/renderers/FormRenderer.php");

$cmp = new ConfigEditorPage();
$cmp->setConfigSection("delivery_prices");

$form = new DeliveryConfigForm();
$cmp->setForm($form);

$cmp->render();
?>
