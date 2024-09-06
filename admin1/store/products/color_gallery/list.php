<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("store/beans/ProductsBean.php");
include_once("store/beans/ProductColorsBean.php");

include_once("store/beans/ProductColorPhotosBean.php");


$rc = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));

$cmp = new BeanListPage();
$cmp->getPage()->setName(tr("Color Scheme") . ": " . $rc->getData("product_name"));

$bean = new ProductColorsBean();

$select_colors = $bean->select();
$select_colors->fields()->set("pclr.*", "p.product_name");
$select_colors->fields()->setExpression("(SELECT pcp.pclrpID FROM product_color_photos pcp WHERE pcp.pclrID = pclr.pclrID ORDER BY position ASC LIMIT 1)", "pclrpID");

$select_colors->from = " product_colors pclr LEFT JOIN products p ON p.prodID = pclr.prodID ";
$select_colors->where()->add("pclr.prodID", $rc->getID());

$cmp->setIterator(new SQLQuery($select_colors, $bean->key(), $bean->getTableName()));

$cmp->setListFields(array("pclrpID"=>"Scheme Photo", "color"=>"Color", "color_photo"=>"Color Chip"));

$cmp->setBean($bean);

$cmp->initView();

$ticr2 = new ImageCellRenderer(-1, 64);
$ticr2->setBean(new ProductColorPhotosBean());
$ticr2->setLimit(0);
$cmp->getView()->getColumn("pclrpID")->setCellRenderer($ticr2);

//color chip is blob in ProductColorsBean
$ticr1 = new ImageCellRenderer(-1, 32);
$ticr1->setBean($bean);
$ticr1->setBlobField("color_photo");
$cmp->getView()->getColumn("color_photo")->setCellRenderer($ticr1);


$act = $cmp->viewItemActions();

$act->getByAction("Edit")->getURL()->add(new DataParameter("prodID", $rc->getID()));

$act->append(Action::RowSeparator());

$act->append(new Action("Photos", "gallery/list.php", array(new DataParameter($bean->key(), $bean->key()))));

$cmp->render();

?>
