<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("store/beans/StoreSizesBean.php");

$cmp = new BeanListPage();

$bean = new StoreSizesBean();

$cmp->setBean($bean);

$cmp->setListFields(array("position"=>"Position", "size_value"=>"Size"));

$cmp->initView();
$cmp->getView()->setItemsPerPage(-1);

$cmp->getPage()->navigation()->clear();
$cmp->render();

?>
