<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("class/forms/BrandInputForm.php");
include_once("class/beans/BrandsBean.php");

$cmp = new BeanEditorPage();

$cmp->setBean(new BrandsBean());
$cmp->setForm(new BrandInputForm());
$cmp->render();

?>
