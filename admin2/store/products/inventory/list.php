<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("components/renderers/cells/ImageCellRenderer.php");
include_once("components/renderers/cells/ColorCodeCellRenderer.php");

include_once("store/beans/ProductInventoryBean.php");
include_once("store/beans/ProductPhotosBean.php");
include_once("store/beans/ProductColorPhotosBean.php");
include_once("store/beans/ProductsBean.php");
include_once("store/beans/ProductCategoriesBean.php");
include_once("store/beans/ProductColorsBean.php");
include_once("store/beans/StoreSizesBean.php");
include_once("store/beans/SectionsBean.php");

class InventoryFilterInputForm extends InputForm {
    public function __construct(int $prodID)
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


        $field = DataInputFactory::Create(DataInputFactory::SELECT, "filter_color" , "Цвят", 0);
        $colors = new ProductColorsBean();
        $qry = $colors->query("pclrID", "color");

        if ($prodID>0) {
            $qry->select->where()->add("prodID", $prodID);
        }
        $qry->select->group_by = " color ";

        $field->getRenderer()->setIterator($qry);

        $field->getRenderer()->getItemRenderer()->setValueKey("color");
        $field->getRenderer()->getItemRenderer()->setLabelKey("color");
        $field->getRenderer()->na_label = "--- Всички ---";

        $field->getRenderer()->setInputAttribute("onChange", "this.form.submit()");

        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "filter_size" , "Размер", 0);
        $bean = new ProductInventoryBean();
        $qry_sizes = $bean->query( "pi.piID", "pi.size_value", "sz.position");
        $qry_sizes->select->from.=" pi  JOIN store_sizes sz ON sz.size_value = pi.size_value ";
        if ($prodID>0) {
            $qry_sizes->select->where()->add("prodID", $prodID);
        }
        $qry_sizes->select->group_by = " pi.size_value ";

        $qry_sizes->select->order_by = " sz.position ASC ";

        $field->getRenderer()->setIterator($qry_sizes);
        $field->getRenderer()->getItemRenderer()->setValueKey("size_value");
        $field->getRenderer()->getItemRenderer()->setLabelKey("size_value");
        $field->getRenderer()->na_label = "--- Всички ---";

        $field->getRenderer()->setInputAttribute("onChange", "this.form.submit()");

        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::HIDDEN, "prodID", "prodID", 0);
        $this->addInput($field);
    }

}

$prodID = -1;

$cmp = new BeanListPage();


$products = new ProductsBean();
$cats = new ProductCategoriesBean();
$bean = new ProductInventoryBean();

$product = NULL;

if (isset($_GET["prodID"])) {
    $prodID = (int)$_GET["prodID"];

}

//$form = new InventoryFilterInputForm($prodID);


try {
    $product = $products->getByID($prodID);
    $category_name = $cats->getValue($product["catID"], "category_name");

    $pageName = tr("Inventory") . ": " . $product["section"] . " / " . $category_name . " / " . $product["product_name"];
    $cmp->getPage()->setName($pageName);

    $cmp->getPage()->getActions()->removeByAction("Add");


}
catch (Exception $e) {
    $prodID = -1;
    $cmp->getPage()->setName(tr("All Products Inventory"));
    $cmp->getPage()->getActions()->removeByAction("Add");
}




$qry = $bean->query();
$qry->select->fields()->set("pi.*",
                            "pc.category_name",
                            //"pcp.pclrpID",
                            //"sc.color_code",
                            //"p.brand_name",
                            //"p.keywords",
                            "p.product_name"
                            //"p.product_description",
                            //"p.class_name",
                            //"p.section"
    );

//$qry->select->fields()->setExpression("(SELECT GROUP_CONCAT(CONCAT_WS(':', ia.attribute_name, ia.value) SEPARATOR '<BR>') COLLATE 'utf8_general_ci'
// FROM inventory_attributes ia
// WHERE ia.piID=pi.piID GROUP BY ia.piID )", "inventory_attributes");

$qry->select->from = " product_inventory pi 
LEFT JOIN products p ON p.prodID = pi.prodID 
LEFT JOIN product_categories pc ON pc.catID=p.catID 
 ";
//LEFT JOIN product_colors pclr ON pclr.pclrID = pi.pclrID
//LEFT JOIN product_color_photos pcp ON pcp.pclrID = pi.pclrID
//LEFT JOIN store_colors sc ON sc.color=pclr.color
//LEFT JOIN color_chips cc ON cc.prodID = p.prodID

//$qry->select->group_by = " pi.piID ";

if ($prodID > 0) {
    $qry->select->where()->add("pi.prodID", $prodID);
}

//apply filtering
//if ($proc->getStatus() === IFormProcessor::STATUS_OK) {
//
//    if ($form->haveInput("filter_color")) {
//        $filter_color = $form->getInput("filter_color")->getValue();
//        if ($filter_color) {
//            $qry->select->where()->add("pi.color", "'" . $filter_color . "'");
//        }
//    }
//
//    if ($form->haveInput("filter_size")) {
//        $filter_size = $form->getInput("filter_size")->getValue();
//        if ($filter_size) {
//            $qry->select->where()->add("pi.size_value", "'" . $filter_size . "'");
//        }
//    }
//
//    if ($form->haveInput("filter_section")) {
//        $filter_section = $form->getInput("filter_section")->getValue();
//        if ($filter_section) {
//            $qry->select->where()->add("p.section", "'" . $filter_section . "'");
//        }
//    }
//
//    if ($form->haveInput("filter_catID")) {
//        $filter_catID = $form->getInput("filter_catID")->getValue();
//        if ($filter_catID > 0) {
//            $qry->select->where()->add("p.catID", $filter_catID);
//        }
//    }
//
//}

//echo $qry->select->getSQL();

$view_inventory = new SQLSelect();
$view_inventory->fields()->set("derived.*");
$view_inventory->from = "(" . $qry->select->getSQL() . ") as derived ";

//echo $view_inventory->getSQL();

$cmp->setListFields(array("piID" => "ID",
                          "prodID" => "ProdID",
                          //"section" => "Section",
                          "category_name" => "Category",
                          "product_name" => "Product",
                          //"color" => "Color",
                          //"pclrpID" => "Color Photo",
                          //"color_code" => "Color Code",
                          //"size_value" => "Size",

                          //"brand_name" => "Brand",
                          //"class_name" => "Class",

                          "stock_amount" => "In Stock",
                          //"price"                => "Price", "buy_price" => "Buy Price", "promo_price" => "Promo Price",
                          //"weight"               => "Weight", "inventory_attributes" => "Attributes"
                          //"inventory_attributes" => "Attributes",
                    ));

if ($prodID > 0) {
    //$cmp->removeListFields("product_name", "section");
}

$iterator = new SQLQuery($view_inventory, "piID", $bean->getTableName());

$cmp->setBean($bean);
$cmp->setIterator($iterator);


$cmp->initView();

$view = $cmp->getView();

$view->setDefaultOrder("prodID DESC");

//$ccode = new ColorCodeCellRenderer();
//$view->getColumn("color_code")->setCellRenderer($ccode);
//
//$ticr1 = new ImageCellRenderer(-1, 64);
//$ticr1->setBean(new ProductColorPhotosBean());
//$ticr1->setBlobField("photo");
//$view->getColumn("pclrpID")->setCellRenderer($ticr1);

//$view->getColumn("color_code")->setCellRenderer(new ColorCodeCellRenderer());

$act = $cmp->viewItemActions();
$edit_action = $act->getByAction("Edit");
$edit_action->getURL()->add(new DataParameter("prodID"));

$act->removeByAction("Delete");

//$act->append(Action::RowSeparator());

//$act->append(new Action("Copy", "add.php", array(new DataParameter("prodID"),
//                                                 new DataParameter("copyID", $bean->key()))));

$cmp->render();



?>
<script type="text/javascript">
    function deleteAll()
    {
        alert("deleteAll");
    }
</script>
