<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");

include_once("store/forms/StorePromoInputForm.php");
include_once("store/beans/StorePromosBean.php");

$cmp = new BeanEditorPage();
$cmp->setBean(new StorePromosBean());
$cmp->setForm(new StorePromoInputForm());
$cmp->render();


?>