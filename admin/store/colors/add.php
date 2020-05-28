<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("class/forms/StoreColorInputForm.php");
include_once("class/beans/StoreColorsBean.php");

$cmp = new BeanEditorPage();
$cmp->setBean(new StoreColorsBean());
$cmp->setForm(new StoreColorInputForm());
$cmp->render();

?>
