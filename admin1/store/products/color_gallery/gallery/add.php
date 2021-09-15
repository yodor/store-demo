<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("store/beans/ProductColorsBean.php");
include_once("store/beans/ProductColorPhotosBean.php");

include_once("forms/PhotoForm.php");

$rc = new BeanKeyCondition(new ProductColorsBean(), "../list.php");

$cmp = new BeanEditorPage();
$cmp->setRequestCondition($rc);

$photos = new ProductColorPhotosBean();
$cmp->setBean($photos);
$cmp->setForm(new PhotoForm());
$cmp->render();

?>
