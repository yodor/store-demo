<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("beans/LanguagesBean.php");
include_once("forms/LanguageInputForm.php");

$menu = array();

$page = new AdminPage("Add Language");
$page->checkAccess(ROLE_SETTINGS_MENU);

$view = new InputFormView(new LanguagesBean(), new LanguageInputForm());

$view->processInput();

$page->startRender($menu);

$view->render();

$page->finishRender();


?>
