<?php
include_once("session.php");
include_once("templates/admin/NestedSetViewPage.php");
include_once("store/beans/ProductCategoriesBean.php");

$cmp = new NestedSetViewPage();

$cmp->setBean(new ProductCategoriesBean());
$cmp->setListFields(array("category_name"=>"Category Name"));

$cmp->getPage()->navigation()->clear();

$cmp->render();


?>
