<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/DeliveryConfigForm.php");

include_once("beans/ConfigBean.php");
include_once("forms/processors/ConfigFormProcessor.php");
include_once("forms/renderers/FormRenderer.php");

$page = new AdminPage();

$config = ConfigBean::factory();
$config->setSection("delivery_prices");

$form = new DeliveryConfigForm();
$config->loadForm($form);

$rend = new FormRenderer($form);
$rend->setClassName("config_form");

$proc = new ConfigFormProcessor();

$form->setProcessor($proc);

$proc->process($form);

if ($proc->getStatus() === IFormProcessor::STATUS_OK) {
    Session::set("alert", "Configuration Updated");
    //   header("Location: delivery.php");
    //   exit;
}
else if ($proc->getStatus() === IFormProcessor::STATUS_ERROR) {
    Session::set("alert", $proc->getMessage());
    //   header("Location: delivery.php");
    //   exit;
}

$page->startRender();

$rend->render();

$page->finishRender();
?>
