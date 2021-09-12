<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("components/renderers/cells/ImageCellRenderer.php");
include_once("components/renderers/cells/ColorCodeCellRenderer.php");

include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductsBean.php");

$prodID = -1;

$cmp = new BeanListPage();

$products = new ProductsBean();
$bean = new ProductInventoryBean();

$product = NULL;

if (isset($_GET["prodID"])) {
    $prodID = (int)$_GET["prodID"];
}

try {
    $product = $products->getByID($prodID);

    $pageName = tr("Inventory") . " - " . $product["section"] . " / " . $product["class_name"] . " / " . $product["brand_name"] . " / " . $product["product_name"];
    $cmp->getPage()->setName($pageName);

    $action_add = $cmp->getPage()->getActions()->getByAction("Add");
    $action_add->getURLBuilder()->add(new URLParameter("prodID", $prodID));

}
catch (Exception $e) {
    $prodID = -1;
    $cmp->getPage()->setName(tr("All Products Inventory"));
    $cmp->getPage()->getActions()->removeByAction("Add");
}

$search_fields = array("product_name", "category_name", "class_name", "product_summary", "keywords", "brand_name",
                       "section", "color", "inventory_attributes");

$cmp->getSearch()->getForm()->setFields($search_fields);

$qry = $bean->query();
$qry->select->fields()->set("pi.*", "pc.category_name", "pcp.pclrpID", "sc.color_code", "p.brand_name", "p.keywords", "p.product_name", "p.product_summary", "p.class_name", "p.section");

$qry->select->fields()->setExpression("(SELECT GROUP_CONCAT(CONCAT_WS(':', ia.attribute_name, ia.value) SEPARATOR '<BR>') COLLATE 'utf8_general_ci' 
 FROM inventory_attributes ia  
 WHERE ia.piID=pi.piID GROUP BY ia.piID )", "inventory_attributes");

$qry->select->from = " product_inventory pi 
JOIN products p ON p.prodID = pi.prodID 
JOIN product_categories pc ON pc.catID=p.catID LEFT 
JOIN product_colors pclr ON pclr.pclrID = pi.pclrID LEFT 
JOIN product_color_photos pcp ON pcp.pclrID = pi.pclrID LEFT
JOIN store_colors sc ON sc.color=pclr.color LEFT  
JOIN color_chips cc ON cc.prodID = p.prodID LEFT 
JOIN product_photos pp ON pp.prodID = pi.prodID ";
$qry->select->group_by = " pi.piID ";

if ($prodID > 0) {
    $qry->select->where()->add("pi.prodID", $prodID);

}

$view_inventory = new SQLSelect();
$view_inventory->fields()->set("*");
$view_inventory->from = "(" . $qry->select->getSQL(FALSE, FALSE) . ") as derived ";



$cmp->setListFields(array("piID"                 => "ID", "prodID" => "ProdID", "section" => "Section",
                          "pclrpID"              => "Color Scheme", "product_name" => "Product",
                          "category_name"        => "Category", "brand_name" => "Brand", "class_name" => "Class",
                          "color"                => "Color", "size_value" => "Size", "stock_amount" => "In Stock",
                          "price"                => "Price", "bui_price" => "Buy Price", "promo_price" => "Promo Price",
                          "weight"               => "Weight", "inventory_attributes" => "Attributes"));

if ($prodID > 0) {
    $cmp->removeListFields("product_name", "section", "class_name", "brand_name", "prodID");
}

$iterator = new SQLQuery($view_inventory, "piID", $bean->getTableName());
$cmp->setIterator($iterator);
$cmp->setBean($bean);

$cmp->initView();

$view = $cmp->getView();

$view->setDefaultOrder("prodID DESC");

$ticr1 = new ImageCellRenderer(-1, 64);
$ticr1->setBean(new ProductColorPhotosBean());
$ticr1->setBlobField("photo");
$view->getColumn("pclrpID")->setCellRenderer($ticr1);

//$view->getColumn("color_code")->setCellRenderer(new ColorCodeCellRenderer());

$act = $cmp->viewItemActions();
$edit_action = $act->getByAction("Edit");
$edit_action->getURLBuilder()->add(new DataParameter("prodID"));

$act->append(new RowSeparator());

$act->append(new Action("Copy", "add.php", array(new DataParameter("prodID"),
                                                 new DataParameter("copyID", $bean->key()))));

$cmp->render();

?>
