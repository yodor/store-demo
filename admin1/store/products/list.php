<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("store/beans/ProductsBean.php");
include_once("store/beans/ProductPhotosBean.php");
include_once("store/beans/ProductColorPhotosBean.php");


$menu = array(
    new MenuItem("Inventory", "inventory/list.php", "list"),
);

$cmp = new BeanListPage();
$cmp->getPage()->setPageMenu($menu);

$bean = new ProductsBean();

$cmp->setListFields(
    array("section"=>"Section",
          "class_name"=>"Class",
          "category_name"=>"Category",
          "brand_name"=>"Brand",
          "product_name"=>"Product Name",
          "product_photos"=>"Product Photos",
          "color_photos"=>"Color Gallery",
          "sizes"=>"Sizing",
          "visible"=>"Visible",
          "stock_amount"=>"Stock Amount",
          "price_min"=>"Pirce Min",
          "price_max"=>"Price Max")
);

$search_fields = array("product_name", "category_name", "class_name", "product_summary", "keywords", "brand_name",
                       "section");

$cmp->getSearch()->getForm()->setFields($search_fields);

$qry = $bean->query();

$qry->select->fields()->setExpression("SUM(pi.stock_amount)", "stock_amount");
$qry->select->fields()->setExpression("MIN(pi.price)", "price_min");
$qry->select->fields()->setExpression("MAX(pi.price)", "price_max");
$qry->select->fields()->setExpression("group_concat(distinct(size_value) SEPARATOR '<BR>' )", "sizes");
$qry->select->fields()->set("p.prodID", "p.product_name", "p.class_name", "p.brand_name", "p.section", "pc.category_name", "p.visible",
"p.price", "p.promo_price", "p.buy_price");
$qry->select->fields()->setExpression("(SELECT pp.ppID FROM product_photos pp WHERE pp.prodID = p.prodID LIMIT 1)", "product_photos");
$qry->select->fields()->setExpression("(SELECT 
        GROUP_CONCAT(inventory_photos.pclrpID SEPARATOR '|') FROM 
        (SELECT pclrpID, prodID FROM product_color_photos pcp 
        LEFT JOIN product_colors pc ON pc.pclrID=pcp.pclrID GROUP BY pcp.pclrID ORDER BY pcp.position ASC) inventory_photos WHERE inventory_photos.prodID=pi.prodID )", "color_photos");

$qry->select->from = " products p 
                        LEFT JOIN product_inventory pi ON pi.prodID = p.prodID 
                        JOIN product_categories pc ON pc.catID=p.catID ";

$qry->select->group_by = "  p.prodID ";

//echo $qry->select->getSQL();

$cmp->setIterator($qry);
$cmp->setBean($bean);

$cmp->initView();




$view = $cmp->getView();

$ticr1 = new ImageCellRenderer(-1, 64);
$ticr1->setBean(new ProductPhotosBean());
$ticr1->setLimit(1);
$view->getColumn("product_photos")->setCellRenderer($ticr1);

$ticr2 = new ImageCellRenderer(-1, 64);
$ticr2->setBean(new ProductColorPhotosBean());
$ticr2->setLimit(0);
$view->getColumn("color_photos")->setCellRenderer($ticr2);

$view->getColumn("visible")->setCellRenderer(new BooleanCellRenderer("Yes", "No"));


$act = $cmp->viewItemActions();
$act->append(Action::RowSeparator());
$act->append(new Action("Inventory", "inventory/list.php", array(new DataParameter("prodID", $bean->key()))));
$act->append(Action::RowSeparator());
$act->append(new Action("Color Scheme", "color_gallery/list.php", array(new DataParameter("prodID", $bean->key()))));
$act->append(Action::RowSeparator());

$act->append(new Action("Photo Gallery", "gallery/list.php", array(new DataParameter("prodID", $bean->key()))));

$cmp->getPage()->navigation()->clear();
$cmp->render();

?>
