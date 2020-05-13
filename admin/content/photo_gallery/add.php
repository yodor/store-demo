<?php
//$GLOBALS["DEBUG_OUTPUT"]=1;
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/GalleryPhotosBean.php");

include_once("forms/PhotoForm.php");


$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$event_photos = new GalleryPhotosBean();


//prefer db_row
$view = new BeanFormEditor($event_photos, new PhotoForm());

$form = $view->getForm()->getInput("photo")->getProcessor()->transact_mode = InputProcessor::TRANSACT_DBROW;

$view->processInput();

$page->startRender();

$view->render();

$page->finishRender();


?>
