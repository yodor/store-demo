<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("beans/ConfigBean.php");
include_once("forms/SEOConfigForm.php");
include_once("forms/processors/ConfigFormProcessor.php");
include_once("forms/renderers/FormRenderer.php");


$page = new AdminPage("SEO");
$page->checkAccess(ROLE_SETTINGS_MENU);

$config = ConfigBean::Factory();
$config->setSection("seo");

$form = new SEOConfigForm();
$config->loadForm($form);


$rend = new FormRenderer();
$rend->setClassName("config_form");
$form->setRenderer($rend);

$proc = new ConfigFormProcessor();


$form->setProcessor($proc);


$proc->processForm($form);

if ($proc->getStatus() == IFormProcessor::STATUS_OK) {
    Session::SetAlert("Configuration Updated");
    header("Location: seo.php");
    exit;
}


$page->startRender();

$form->getRenderer()->renderForm($form);

$page->finishRender();
?>
