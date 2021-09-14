<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/SectionsBean.php");
include_once("class/beans/ProductCategoriesBean.php");

class ProductFilterInputForm extends InputForm {
    public function __construct()
    {
        parent::__construct();

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "filter_section", "Section", 0);
        $rend = $field->getRenderer();
        $sb = new SectionsBean();

        $rend->setIterator($sb->query($sb->key(),"section_title"));
        $rend->getItemRenderer()->setValueKey("section_title");
        $rend->getItemRenderer()->setLabelKey("section_title");

        $field->getRenderer()->na_label = "--- Всички ---";

        $field->getRenderer()->setInputAttribute("onChange", "this.form.submit()");

        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::NESTED_SELECT, "filter_catID", "Category", 0);
        $bean1 = new ProductCategoriesBean();
        $rend = $field->getRenderer();

        $rend->setIterator(new SQLQuery($bean1->selectTree(array("category_name")), $bean1->key(), $bean1->getTableName()));
        $rend->getItemRenderer()->setValueKey("catID");
        $rend->getItemRenderer()->setLabelKey("category_name");

        $field->getRenderer()->na_label = "--- Всички ---";

        $field->getRenderer()->setInputAttribute("onChange", "this.form.submit()");


        $this->addInput($field);
    }

}

$menu = array(
    new MenuItem("Inventory", "inventory/list.php", "list"),
);

$cmp = new BeanListPage();
$cmp->getPage()->setPageMenu($menu);

$bean = new ProductsBean();

$cmp->setListFields(
    array(
        //"section"=>"Section",
//          "class_name"=>"Class",
          "category_name"=>"Category",
//          "brand_name"=>"Brand",
          "product_name"=>"Product Name",
//          "author"=>"Author",
//          "publisher"=>"Publisher",
          "cover"=>"Cover",
//          "color_photos"=>"Color Gallery",
//          "sizes"=>"Sizing",
          "visible"=>"Visible",
//          "stock_amount"=>"Stock Amount",
          "price"=>"Price",
          "promo_price"=>"Promo Price",
          "insert_date"=>"Insert Date",
          "update_date"=>"Update Date",
          //"inventory_attributes" => "Attributes"
    )
//          "price_min"=>"Pirce Min",
//          "price_max"=>"Price Max")
);

$search_fields = array("product_name", "category_name", "product_description", "long_description", "keywords",
                       );

$cmp->getSearch()->getForm()->setFields($search_fields);

$qry = $bean->query();

//$qry->select->fields()->set("pi.*",
  //                          "pc.category_name",
    //"pcp.pclrpID",
    //"sc.color_code",
    //"p.brand_name",
    //"p.keywords",
    //                        "p.product_name"
//"p.product_description",
//"p.class_name",
//"p.section"
//);
//$qry->select->fields()->setExpression("(SELECT GROUP_CONCAT(CONCAT_WS(':', ia.attribute_name, ia.value) SEPARATOR '<BR>') COLLATE 'utf8_general_ci'
// FROM inventory_attributes ia
// WHERE ia.piID=pi.piID GROUP BY ia.piID )", "inventory_attributes");


$form = new ProductFilterInputForm();

$frend = new FormRenderer($form);

$frend->getSubmitLine()->setEnabled(false);
$frend->setMethod(FormRenderer::METHOD_GET);
$frend->setAttribute("autocomplete", "off");
$cmp->append($frend);


$proc = new FormProcessor();

$proc->process($form);


//$qry->select->fields()->setExpression("SUM(pi.stock_amount)", "stock_amount");
//$qry->select->fields()->setExpression("MIN(pi.price)", "price_min");
//$qry->select->fields()->setExpression("MAX(pi.price)", "price_max");
//$qry->select->fields()->setExpression("group_concat(distinct(ssz.size_value) ORDER BY ssz.position ASC SEPARATOR '<BR>')", "sizes");
//$qry->select->fields()->setExpression("replace(cc.colors, '|','<BR>')",  "colors");
$qry->select->fields()->set("p.prodID", "p.product_name",  "pc.category_name", "p.visible",
"p.price", "p.promo_price", "p.insert_date", "p.update_date");
//"pp.product_photo");
$qry->select->fields()->setExpression("(SELECT ppID FROM product_photos pp WHERE pp.prodID = p.prodID LIMIT 1)", " cover ");
$qry->select->from = " products p 
LEFT JOIN product_inventory pi ON pi.prodID = p.prodID 
LEFT JOIN product_categories pc ON pc.catID=p.catID
";
//LEFT JOIN store_sizes ssz ON ssz.size_value = pi.size_value

$qry->select->group_by = "  p.prodID ";

//echo $qry->select->getSQL();
if ($proc->getStatus() === IFormProcessor::STATUS_OK) {
    $filter_section = $form->getInput("filter_section")->getValue();
    $filter_catID = $form->getInput("filter_catID")->getValue();

    if ($filter_catID>0) {
        $qry->select->where()->add("p.catID", $filter_catID);
    }
    if ($filter_section) {
        $qry->select->where()->add("p.section", "'".$filter_section."'");
    }
}

$cmp->setBean($bean);
$cmp->setIterator($qry);

$cmp->initView();




$view = $cmp->getView();

$ticr1 = new ImageCellRenderer(-1, 128);
$ticr1->setBean(new ProductPhotosBean());
$ticr1->setLimit(1);
$view->getColumn("cover")->setCellRenderer($ticr1);

//$ticr2 = new ImageCellRenderer(-1, 32);
//$ticr2->setBean(new ProductColorPhotosBean());
//$ticr2->setLimit(0);
//$view->getColumn("color_photos")->setCellRenderer($ticr2);

$view->getColumn("visible")->setCellRenderer(new BooleanCellRenderer("Yes", "No"));


$act = $cmp->viewItemActions();
$act->append(new RowSeparator());

$act->append(new Action("Photo Gallery", "gallery/list.php", array(new DataParameter("prodID", $bean->key()))));


//$act->append(new RowSeparator());
//$act->append(
//    new Action("Color Scheme", "color_gallery/list.php",
//       array(
//        new DataParameter("prodID", $bean->key()),
//        )
//    )
//);
$act->append(new RowSeparator());
$act->append(new Action("Inventory", "inventory/list.php", array(new DataParameter("prodID", $bean->key()))));


$cmp->getPage()->navigation()->clear();
$cmp->render();

?>
