<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("class/forms/AttributeInputForm.php");
include_once("class/beans/AttributesBean.php");


$cmp = new BeanEditorPage();

$cmp->setBean(new AttributesBean());
$cmp->setForm(new AttributeInputForm());
$cmp->render();

?>
