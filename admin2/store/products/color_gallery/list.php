<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("store/beans/ProductsBean.php");
include_once("store/beans/ProductColorsBean.php");

include_once("store/beans/ProductColorPhotosBean.php");
include_once("components/renderers/cells/ColorCodeCellRenderer.php");

$rc = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));

$cmp = new BeanListPage();
$cmp->getPage()->setName(tr("Color Scheme") . ": " . $rc->getData("product_name"));

$bean = new ProductColorsBean();

$select_colors = $bean->select();
$select_colors->fields()->set("pclr.*", "p.product_name", "sc.color_code");
$select_colors->fields()->setExpression("(SELECT pcp.pclrpID FROM product_color_photos pcp WHERE pcp.pclrID = pclr.pclrID ORDER BY position ASC LIMIT 1)", "pclrpID");

$select_colors->from = " product_colors pclr LEFT JOIN products p ON p.prodID = pclr.prodID LEFT JOIN store_colors sc ON sc.color=pclr.color ";
$select_colors->where()->add("pclr.prodID", $rc->getID());

$iterator = new SQLQuery($select_colors, $bean->key(), $bean->getTableName());
//echo $iterator->select->getSQL();

$cmp->setIterator($iterator);

$cmp->setListFields(array("pclrpID"=>"Scheme Photo", "color"=>"Color", "color_photo"=>"Color Chip", "color_code"=>"Color Code"));

$cmp->setBean($bean);

$cmp->initView();

$ccode = new ColorCodeCellRenderer();
$cmp->getView()->getColumn("color_code")->setCellRenderer($ccode);

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
$action_edit = $act->getByAction("Edit");
if ($action_edit instanceof Action) {
    $action_edit->getURLBuilder()->add(new DataParameter($rc->getBean()->key(), $rc->getID()));
}

$act->append(new RowSeparator());

$act->append(
    new Action("Photos", "gallery/list.php",
               array(
                   new DataParameter($bean->key(), $bean->key()),
                   new DataParameter($rc->getBean()->key(), $rc->getID())
               )
    )
);

$cmp->render();

?>
