<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");
include_once ("components/renderers/cells/ColorCodeCellRenderer.php");
include_once("store/beans/StoreColorsBean.php");

$cmp = new BeanListPage();

$cmp->setListFields(array("color"=>"Color","color_code"=>"Color Code"));
$cmp->setBean(new StoreColorsBean());

$cmp->initView();
//$cmp->getView()->setDefaultOrder(" color ASC ");
$cmp->getView()->getColumn("color_code")->setCellRenderer(new ColorCodeCellRenderer());

$cmp->getPage()->navigation()->clear();

$cmp->render();

?>
