<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("store/beans/ProductClassesBean.php");


$cmp = new BeanListPage();

$bean = new ProductClassesBean();
$qry = $bean->query();
$qry->select->from = " product_classes pc ";
$qry->select->fields()->set("pc.*");
$qry->select->fields()->setExpression("(SELECT GROUP_CONCAT(attribute_name SEPARATOR '<BR>') FROM class_attributes ca WHERE ca.pclsID=pc.pclsID)", "class_attributes");
$qry->select->order_by= $bean->key()."  DESC ";
$cmp->setIterator($qry);
$cmp->setListFields(array("class_name"=>"Class Name", "class_attributes"=>"Class Attributes"));
$cmp->setBean($bean);

$cmp->getPage()->navigation()->clear();
$cmp->render();

?>
