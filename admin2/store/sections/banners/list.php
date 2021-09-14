<?php
include_once("session.php");
include_once("templates/admin/GalleryViewPage.php");
include_once("class/beans/SectionBannersBean.php");
include_once("class/beans/SectionsBean.php");


$rc = new BeanKeyCondition(new SectionsBean(), "../list.php", array("section_title"));


$cmp = new GalleryViewPage();
$cmp->setRequestCondition($rc);

$cmp->getPage()->setName(tr("Banners Gallery") . ": " . $rc->getData("section_title"));

$cmp->setBean(new SectionBannersBean());

$cmp->render();
?>
