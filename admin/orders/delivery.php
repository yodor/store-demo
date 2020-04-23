<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/forms/DeliveryConfigForm.php");

include_once("lib/beans/ConfigBean.php");
include_once("lib/forms/processors/ConfigFormProcessor.php");
include_once("lib/forms/renderers/FormRenderer.php");


$page = new AdminPage();
$page->checkAccess(ROLE_ORDERS_MENU);

$config = ConfigBean::factory();
$config->setSection("delivery_prices");

$form = new DeliveryConfigForm();
$config->loadForm($form);


$rend = new FormRenderer();
$rend->setClassName("config_form");
$form->setRenderer($rend);

$proc = new ConfigFormProcessor();


$form->setProcessor($proc);


$proc->processForm($form);

if ($proc->getStatus() === IFormProcessor::STATUS_OK) {
    Session::SetAlert("Configuration Updated");
    //   header("Location: delivery.php");
    //   exit;
}
else if ($proc->getStatus() === IFormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
    //   header("Location: delivery.php");
    //   exit;
}


$page->startRender();

$form->getRenderer()->renderForm($form);

$page->finishRender();
?>
