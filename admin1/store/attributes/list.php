<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");
include_once("store/beans/AttributesBean.php");

$cmp = new BeanListPage();

$cmp->setListFields(array("name"=>"Name","unit"=>"Unit", "type"=>"Type"));

$cmp->setBean(new AttributesBean());

$cmp->initView();
$cmp->getView()->setDefaultOrder(" name ASC ");

$cmp->getPage()->navigation()->clear();

$cmp->render();


?>
