<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
// include_once("class/beans/ProductsBean.php");
include_once("components/TableView.php");
include_once("components/renderers/cells/TableImageCellRenderer.php");
include_once("components/renderers/cells/ColorCodeCellRenderer.php");

include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");

include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductsBean.php");

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);



$prodID = -1;

try {

    $rc = new RequestBeanKey(new ProductsBean(), FALSE, array("product_name"));
    //   $menu=array(
    // 	  new MenuItem("Add Inventory", "add.php".$rc->qrystr, "list-add.png"),
    //   );
    $prodID = (int)$rc->getID();

    $action_add = new Action("", "add.php?prodID=$prodID", array());
    $action_add->setAttribute("action", "add");
    $action_add->setAttribute("title", "Add Inventory");
    $page->getActions()->append($action_add);

}
catch (Exception $e) {

}

$bean = new ProductInventoryBean();

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$search_fields = array("product_name", "category_name", "class_name", "product_summary", "keywords", "brand_name",
                       "section", "color", "inventory_attributes");

$ksc = new KeywordSearch($search_fields);
$ksc->getForm()->getRenderer()->setAttribute("method", "get");

$piID = -1;
if (isset($_GET["piID"])) {
    $piID = (int)$_GET["piID"];
}

// $search_fields = array("prodID", "product_code", "product_name", "color", "size");
// $ksc = new KeywordSearch($search_fields);

$select_inventory = $bean->select();
$select_inventory->fields = " pi.*, pc.category_name, pcp.pclrpID,  sc.color_code,  p.brand_name, p.keywords, p.product_name, p.product_summary, p.class_name, p.section, 
 (SELECT GROUP_CONCAT(CONCAT_WS(':', ia.attribute_name, ia.value) SEPARATOR '<BR>') COLLATE 'utf8_general_ci' 
 FROM inventory_attributes ia 
 WHERE ia.piID=pi.piID GROUP BY ia.piID )  as inventory_attributes  ";
$select_inventory->from = " product_inventory pi 
JOIN products p ON p.prodID = pi.prodID 
JOIN product_categories pc ON pc.catID=p.catID LEFT 
JOIN product_colors pclr ON pclr.pclrID = pi.pclrID LEFT 
JOIN product_color_photos pcp ON pcp.pclrID = pi.pclrID LEFT
JOIN store_colors sc ON sc.color=pclr.color LEFT  
JOIN color_chips cc ON cc.prodID = p.prodID LEFT 
JOIN product_photos pp ON pp.prodID = pi.prodID ";
$select_inventory->group_by = " pi.piID ";

if ($prodID > 0) {
    $select_inventory->where = " pi.prodID = '$prodID' ";
    $page->setName(tr("Inventory") . ": " . $rc->getData("product_name"));
}
else {
    $page->setName(tr("All Products Inventory"));
}

$view_inventory = new SQLSelect();
$view_inventory->from = "(" . $select_inventory->getSQL(FALSE, FALSE) . ") as derived ";

$ksc->processSearch($view_inventory);

$view = new TableView(new SQLQuery($view_inventory, "piID"));
$view->setCaption("Inventory List");
$view->setDefaultOrder(" prodID DESC ");

$view->addColumn(new TableColumn("piID", "ID"));

$view->addColumn(new TableColumn("prodID", "ProdID"));

$view->addColumn(new TableColumn("section", "Section"));

$view->addColumn(new TableColumn("pclrpID", "Color Scheme"));

$view->addColumn(new TableColumn("product_name", "Product Name"));

$view->addColumn(new TableColumn("category_name", "Category"));

$view->addColumn(new TableColumn("brand_name", "Brand"));

// $view->addColumn(new TableColumn("product_photo","Product Photo"));

$view->addColumn(new TableColumn("class_name", "Class"));

$view->addColumn(new TableColumn("color", "Color Name"));
$view->addColumn(new TableColumn("color_code", "Color Code"));
$view->addColumn(new TableColumn("size_value", "Size"));
$view->addColumn(new TableColumn("stock_amount", "Stock Amount"));

$view->addColumn(new TableColumn("price", "Price"));
$view->addColumn(new TableColumn("buy_price", "Buy Price"));
$view->addColumn(new TableColumn("old_price", "Old Price"));
$view->addColumn(new TableColumn("weight", "Weight"));

$view->addColumn(new TableColumn("inventory_attributes", "Attributes"));

$view->addColumn(new TableColumn("actions", "Actions"));

$ticr1 = new TableImageCellRenderer(-1, 64);
$ticr1->setBean(new ProductColorPhotosBean());
$ticr1->setBlobField("photo");
$view->getColumn("pclrpID")->setCellRenderer($ticr1);

$view->getColumn("color_code")->setCellRenderer(new ColorCodeCellRenderer());

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("prodID"),
                                                    new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());

$act->getActions()->append(new RowSeparator());

$act->getActions()->append(new Action("Add Copy", "add.php", array(new DataParameter("prodID"),
                                                        new DataParameter("copyID", $bean->key()))));

$view->getColumn("actions")->setCellRenderer($act);


$page->startRender();

$ksc->render();
$view->render();

$page->finishRender();

?>
