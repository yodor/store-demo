<?php
include_once("session.php");
include_once("templates/admin/GalleryViewPage.php");

include_once("class/beans/GalleryPhotosBean.php");

$cmp = new GalleryViewPage();

$cmp->setBean(new GalleryPhotosBean());

$cmp->getPage()->navigation()->clear();


$cmp->render();

?>