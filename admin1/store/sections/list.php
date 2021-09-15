<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");
include_once("store/beans/SectionsBean.php");

$cmp = new BeanListPage();

$cmp->setListFields(array("position"=>"#", "section_title"=>"Section"));

$bean = new SectionsBean();
$cmp->setBean($bean);

$cmp->initView();
//$cmp->getView()->setDefaultOrder(" position ASC ");
$cmp->viewItemActions()->append(new RowSeparator());
$cmp->viewItemActions()->append(new Action("Banners Gallery", "banners/list.php", array(new DataParameter("secID", $bean->key()))));

$cmp->getPage()->navigation()->clear();
$cmp->render();
?>
