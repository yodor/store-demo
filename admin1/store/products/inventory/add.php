<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("ProductInventoryInputForm.php");
include_once("store/beans/ProductInventoryBean.php");
include_once("store/beans/ProductsBean.php");

$rc = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name", "class_name", "brand_name",
                                                                    "section"));
$prodID = (int)$rc->getID();

$cmp = new BeanEditorPage();
$cmp->setRequestCondition($rc);

$pageName = $rc->getData("section") . " / " . $rc->getData("class_name") .
    " / " . $rc->getData("brand_name") . " / " . $rc->getData("product_name");

$cmp->getPage()->setName($pageName);

$form = new ProductInventoryInputForm();
$form->setProductID($prodID);

$bean = new ProductInventoryBean();

$copyID = -1;
if (isset($_GET["copyID"])) {
    $copyID = (int)$_GET["copyID"];
}

$cmp->setBean($bean);
$cmp->setForm($form);

$cmp->initView();

//$cmp->getEditor()->getTransactor()->appendValue("prodID", $prodID);

if ($copyID > 0) {
    $form->loadBeanData($copyID, $bean);
}

$cmp->render();

?>
