<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("store/forms/StoreSizeInputForm.php");
include_once("store/beans/StoreSizesBean.php");

$cmp = new BeanEditorPage();
$cmp->setBean(new StoreSizesBean());
$cmp->setForm(new StoreSizeInputForm());

$cmp->render();

?>
