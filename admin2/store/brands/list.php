<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");
include_once("class/beans/BrandsBean.php");

$cmp = new BeanListPage();
$cmp->setListFields(array("brand_name"=>"Brand Name", "summary"=>"Summary", "url"=>"URL"));
$cmp->setBean(new BrandsBean());
$cmp->getPage()->navigation()->clear();
$cmp->render();

?>
