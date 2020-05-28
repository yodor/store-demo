<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("class/forms/StoreSizeInputForm.php");
include_once("class/beans/StoreSizesBean.php");

$cmp = new BeanEditorPage();
$cmp->setBean(new StoreSizesBean());
$cmp->setForm(new StoreSizeInputForm());

$cmp->render();

?>
