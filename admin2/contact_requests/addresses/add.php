<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("class/forms/ContactAddressInputForm.php");
include_once("class/beans/ContactAddressesBean.php");


$cmp = new BeanEditorPage();

$cmp->setBean(new ContactAddressesBean());
$cmp->setForm(new ContactAddressInputForm());
$cmp->render();

?>
