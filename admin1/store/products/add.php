<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("class/forms/ProductInputForm.php");
include_once("class/beans/ProductsBean.php");


$cmp = new BeanEditorPage();
$cmp->setBean(new ProductsBean());
$cmp->setForm(new ProductInputForm());
$cmp->initView();

$cmp->getEditor()->getTransactor()->assignInsertValue("insert_date", DBConnections::Get()->dateTime());
$cmp->render();
?>
