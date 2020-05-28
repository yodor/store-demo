<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");
// include_once("class/beans/ProductsBean.php");
include_once("components/TableView.php");
include_once("components/renderers/cells/ImageCellRenderer.php");
include_once("components/renderers/cells/ColorCodeCellRenderer.php");

include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");

include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductsBean.php");

$page = new AdminPage();


$prodID = -1;

try {

    $rc = new BeanKeyCondition(new ProductsBean(), FALSE, array("product_name"));

    $prodID = (int)$rc->getID();

    $action_add = new Action("", "add.php?prodID=$prodID", array());
    $action_add->setAttribute("action", "add");
    $action_add->setAttribute("title", "Add Inventory");
    $page->getActions()->append($action_add);

}
catch (Exception $e) {

}

$cmp = new BeanListPage();


$bean = new ProductInventoryBean();

$search_fields = array("product_name", "category_name", "class_name", "product_summary", "keywords", "brand_name",
                       "section", "color", "inventory_attributes");

$cmp->getSearch()->getForm()->setFields($search_fields);


$piID = -1;
if (isset($_GET["piID"])) {
    $piID = (int)$_GET["piID"];
}

$qry = $bean->query();
$qry->select->fields()->set("pi.*", "pc.category_name", "pcp.pclrpID",  "sc.color_code",  "p.brand_name", "p.keywords",
                            "p.product_name", "p.product_summary", "p.class_name", "p.section");

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

    $page->setName(tr("Inventory") . ": " . $rc->getData("product_name"));
}
else {
    //$page->setName(tr("All Products Inventory"));
}

$view_inventory = new SQLSelect();
$view_inventory->fields()->set("*");
$view_inventory->from = "(" . $qry->select->getSQL(FALSE, FALSE) . ") as derived ";


$cmp->setListFields(array("piID"=>"ID", "prodID"=>"ProdID","section"=>"Section","pclrpID"=>"Color Scheme",
                        "product_name"=>"Product Name", "category_name"=>"Category Name","brand_name"=>"Brand Name",
                        "class_name"=>"Class", "color"=>"Color", "size"=>"Size","stock_amount"=>"Stock Amount", "price"=>"Price",
                        "bui_price"=>"Buy Price", "old_price"=>"Old Price", "weight"=>"Weight", "inventory_attributes"=>"Attributes"));

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

$act->append(new RowSeparator());

$act->append(new Action("Copy", "add.php", array(new DataParameter("prodID"),
                                                        new DataParameter("copyID", $bean->key()))));

$cmp->render();

?>
