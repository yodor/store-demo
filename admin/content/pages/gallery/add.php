<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("beans/DynamicPagesBean.php");
include_once("beans/DynamicPagePhotosBean.php");

include_once("forms/PhotoForm.php");


$ref_key = "";
$ref_val = "";
$qrystr = refkeyPageCheck(new DynamicPagesBean(), "list.php", $ref_key, $ref_id);

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$event_photos = new DynamicPagePhotosBean();
$event_photos->select()->where = "$ref_key='$ref_id'";

$view = new BeanFormEditor($event_photos, new PhotoForm());

//current version of dynamic page photos table is set to DBROWS
$view->getForm()->getInput("photo")->getProcessor()->transact_mode = InputProcessor::TRANSACT_DBROW;

$view->getTransactor()->appendValue($ref_key, $ref_id);

$view->processInput();

$page->startRender($menu);

$view->render();

$page->finishRender();


?>