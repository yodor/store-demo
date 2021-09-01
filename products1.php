<?php
include_once("session.php");

include_once("class/pages/ProductListPage.php");

include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductPhotosBean.php");

include_once("components/TableView.php");
include_once("components/ItemView.php");

include_once("iterators/SQLQuery.php");
include_once("iterators/ArrayDataIterator.php");

include_once("utils/RelatedSourceFilterProcessor.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/utils/filters/ProductFilters.php");

include_once("class/components/renderers/items/ProductListItem.php");
include_once("class/components/renderers/cells/ProductPhotoCellRenderer.php");


//1. prepare all products select
//2. apply filters colors,brands, attributes etc
//3. construct the aggregate tree view
//4. select child nodes from three with products select

$page = new ProductListPage();

$bean = $page->product_categories;

//construct tree view from the source bean and set tree text label field
$treeView = $page->treeView;

//construct initial relation query to aggregate with the tree view
$product_selector = new SQLSelect();

//color/size/price filters need NOT grouping! in the derived table
$derived = clone $page->derived;
$derived->group_by = " pi.prodID, pi.color ";

$product_selector->from = " ( " . $derived->getSQL(FALSE, FALSE) . " ) as relation ";

//process get filters
$proc = new RelatedSourceFilterProcessor($bean, "prodID");

//$proc->addFilter("keyword", $page->keyword_search);

$proc->addFilter("brand_name", "brand_name");
$proc->addFilter("color", new ColorFilter());
$proc->addFilter("size_value", new SizingFilter());
$proc->addFilter("price_range", new PricingFilter());
$proc->addFilter("ia", new InventoryAttributeFilter());

// $proc->addFilter("section", new SectionFilter());

//process filters before tree select ctor
$proc->process($treeView);

$num_filters = $proc->numFilters();

//apply all filter sql to the relation
if ($num_filters) {
    $filter = $proc->getFilterAll();
    //   echo "Num Filters: $num_filters";

    $product_selector = $product_selector->combineWith($filter);

    $treeView->open_all = TRUE;

    //   $inventory_selector = $inventory_selector->combineWith($filter);

}
//

//construct the aggregated tree query
$tree_selector = $bean->selectTreeRelation($product_selector, "relation", "prodID", array("category_name"));
// echo $tree_selector->getSQL();

//set the the iterator
$treeView->setIterator(new SQLQuery($tree_selector, $bean->key(), $bean->getTableName()));

$nodeID = $treeView->getSelectedID();

$product_selector->fields()->set(" relation.* "); //TODO list only needed fields here?
$product_selector = $bean->selectChildNodesWith($product_selector, "relation", $nodeID);

$product_selector->group_by = " relation.prodID, relation.color ";

// echo $product_selector->getSQL();

if (strcmp_isset("view", "list", $_GET)) {
    $view = new TableView(new SQLQuery($product_selector, "piID"));
    // $view->addColumn(new TableColumn("piID","ID"));
    // $view->addColumn(new TableColumn("prodID","ID"));
    $view->addColumn(new TableColumn("product_photo", "Снимка"));
    //   $view->addColumn(new TableColumn("product_code","Product Code"));
    $view->addColumn(new TableColumn("product_name", "Продукт"));
    $view->addColumn(new TableColumn("brand_name", "Марка"));
    // $view->addColumn(new TableColumn("category_name","Category Name"));
    $view->addColumn(new TableColumn("color", "Цвят"));
    $view->addColumn(new TableColumn("sell_price", "Цена"));
    // $view->addColumn(new TableColumn("size_values","Sizing"));
    $view->addColumn(new TableColumn("colors", "Цветове"));
    //   $view->addColumn(new TableColumn("color_ids","Colors"));

    $view->getColumn("product_photo")->setCellRenderer(new ProductPhotoCellRenderer(-1, 48));
    $view->getColumn("product_photo")->getHeaderCellRenderer()->setSortable(FALSE);
}
else {
    $view = new ItemView(new SQLQuery($product_selector, "piID"));
    $view->setItemRenderer(new ProductListItem());
}
$view->setItemsPerPage(12);

$sort_prod = new PaginatorSortField("relation.prodID", "Най-нови");
$view->getPaginator()->addSortField($sort_prod);
$sort_price = new PaginatorSortField("relation.sell_price", "Цена");
$view->getPaginator()->addSortField($sort_price);

$view->getTopPaginator()->view_modes_enabled = TRUE;
// $view->setCaption("Products List");

$derived = clone $page->derived;

$derived_table = $derived->getSQL(FALSE, FALSE);

//prepare filter fields source data
$brand_select = new SQLSelect();
$brand_select->fields()->set(" brand_name ");
$brand_select->from = " ($derived_table) as relation ";
$brand_select->group_by = " brand_name ";
$brand_value = $proc->applyFiltersOn($treeView, $brand_select, "brand_name");

$color_select = new SQLSelect();
$color_select->fields()->set(" color ");
$color_select->from = " ($derived_table) as relation ";

$color_select->order_by = " color ";
$color_select->group_by = " color ";
$color_value = $proc->applyFiltersOn($treeView, $color_select, "color");

$size_select = new SQLSelect();
$size_select->fields()->set(" size_value ");
$size_select->from = " ($derived_table) as relation ";

$size_select->group_by = " size_value ";
$size_select->order_by = " prodID ";
$size_value = $proc->applyFiltersOn($treeView, $size_select, "size_value");

$price_info = array();
$price_select = new SQLSelect();
$price_select->fields()->setExpression(" min(sell_price) " , "price_min");
$price_select->fields()->setExpression(" max(sell_price) " , "price_max");
$price_select->from = " ($derived_table) as relation ";

//apply the other filters but skip self - slider shows always min-max of all products
$price_info["price_range"] = $proc->applyFiltersOn($treeView, $price_select, "price_range", TRUE);

$db = DBConnections::Get();
$res = $db->query($price_select->getSQL());
if (!$res) throw new Exception ($db->getError());
if ($row = $db->fetch($res)) {
    $price_info["min"] = $row["price_min"];
    $price_info["max"] = $row["price_max"];
}
$db->free($res);

//dynamic filters from attributes
$dyn_filters = array();
try {

    $ia_name_select = new SQLSelect(); //clone $inventory_selector;
    $ia_name_select->from = " ($derived_table) as relation  ";


    $proc->applyFiltersOn($treeView, $ia_name_select, "ia", TRUE);

    $ia_name_select->fields()->setExpression(" distinct(relation.ia_name) ", "ia_name");
    $ia_name_select->where()->add("relation.ia_name", "NULL",   "IS NOT");
    // 		echo $ia_name_select->getSQL();

    $res = $db->query($ia_name_select->getSQL());
    if (!$res) throw new Exception ("Unable to query inventory attributes: " . $db->getError());
    while ($row = $db->fetch($res)) {
        $name = $row["ia_name"];
        $sel = new SQLSelect();
        $sel->from = " ($derived_table) as relation  ";

        $value = $proc->applyFiltersOn($treeView, $sel, "ia");

        $sel->fields()->setExpression(" distinct(relation.ia_value) " , "ia_value");

        $sel->where()->add("relation.ia_name", "'$name'");
        $sel->where()->add("relation.ia_value" , "''", ">");

        // 		  $sel->order_by = " CAST(relation.ia_value AS DECIMAL(10,2)) ";
        $sel->order_by = " relation.ia_value ASC ";

        // 		  echo $sel->getSQL()."<HR>";

        //parse value into name pairs - ia=Материал:1|Години:1
        if ($value) {
            $ia_values = explode("|", $value);
            if (count($ia_values) > 0) {
                foreach ($ia_values as $pos => $filter_value) {
                    if (!$filter_value) continue;
                    $group = explode(":", $filter_value);
                    if (is_array($group) && count($group) == 2) {
                        if (strcmp($name, $group[0]) == 0) {
                            $value = $group[1];
                        }
                    }
                }
            }
        }
        $dyn_filters[$name] = array("select" => $sel, "value" => $value);
    }
}
catch (Exception $e) {
    // 		echo $ia_name_select->getSQL();
    // 		echo $product_selector->getSQL();

}
if (is_resource($res)) $db->free($res);

$page->startRender();

//

// echo $product_selector->getSQL();
// echo $attributes_select->getSQL();
// echo "<HR>";

echo "<div class='column left'>";

echo "<div class='categories panel'>";
//   if ($num_filters>0) {
// 	echo "<a class='Action Clear' href='javascript:clearFilters()'>Show All Categories</a>";
//   }
echo "<div class='Caption'>" . tr("Категории") . "</div>";
$treeView->render();
echo "</div>"; //tree

//   echo "<BR>";

//   echo "<div>";
//   echo tr("Refine By");
//   echo "<HR>";
//   echo "</div>";

//TODO: filters as links option
echo "<div class='filters panel'>";
echo "<div class='Caption'>" . tr("Филтри") . "</div>";
echo "<form name='filters' autocomplete='off'>";
echo "<div class='InputComponent'>";
echo "<span class='label'>" . tr("Марка") . "</span>";

$field = DataInputFactory::Create(DataInputFactory::SELECT, "brand_name", "Марка", 0);
$rend = $field->getRenderer();
$rend->setIterator(ArrayDataIterator::FromSelect($brand_select, "brand_name", "brand_name"));
$rend->getItemRenderer()->setValueKey("brand_name");
$rend->getItemRenderer()->setLabelKey("brand_name");
$rend->setInputAttribute("onChange", "javascript:filterChanged(this)");
$field->setValue($brand_value);

$rend->render();

echo "</div>";//InputComponent

echo "<div class='InputComponent'>";
echo "<span class='label'>" . tr("Цвят") . "</span>";
$field = DataInputFactory::Create(DataInputFactory::SELECT, "color", "Цвят", 0);
$rend = $field->getRenderer();
$rend->setIterator(ArrayDataIterator::FromSelect($color_select, "color", "color"));
$rend->getItemRenderer()->setValueKey("color");
$rend->getItemRenderer()->setLabelKey("color");
$rend->setInputAttribute("onChange", "javascript:filterChanged(this)");
$field->setValue($color_value);

$rend->render();
echo "</div>";//InputComponent

echo "<div class='InputComponent'>";
echo "<span class='label'>" . tr("Размер") . "</span>";
$field = DataInputFactory::Create(DataInputFactory::SELECT, "size_value", "Размер", 0);
$rend = $field->getRenderer();
$rend->setIterator(ArrayDataIterator::FromSelect($size_select, "size_value", "size_value"));
$rend->getItemRenderer()->setValueKey("size_value");
$rend->getItemRenderer()->setLabelKey("size_value");
$rend->setInputAttribute("onChange", "javascript:filterChanged(this)");
$field->setValue($size_value);

$rend->render();
echo "</div>";//InputComponent

echo "<div class='InputComponent Slider'>";
echo "<span class='label'>" . tr("Цена") . "</span>";
$value_min = $price_info["min"];
$value_max = $price_info["max"];

if ($price_info["price_range"]) {
    $price_range = explode("|", trim($price_info["price_range"]));
    if (count($price_range) == 2) {
        $value_min = (float)$price_range[0];
        $value_max = (float)$price_range[1];
    }
}

$value_min = sprintf("%1.2f", $value_min);
$value_max = sprintf("%1.2f", $value_max);

echo "<span class='value' id='amount'>$value_min - $value_max</span>";
echo "<div class='InputField'>";
echo "<div class='drag' min='{$price_info["min"]}' max='{$price_info["max"]}'></div>";
echo "<input type='hidden' name='price_range' value='$value_min|$value_max'>";
echo "</div>";
echo "</div>";//InputComponent

try {
    foreach ($dyn_filters as $name => $item) {
        echo "<div class='InputComponent'>";
        echo "<span class='label'>" . tr($name) . "</span>";
        $field = DataInputFactory::Create(DataInputFactory::SELECT, "$name", "$name", 0);
        $rend = $field->getRenderer();
        $sel = $item["select"];
        // 		echo $sel->getSQL();
        $rend->setIterator(ArrayDataIterator::FromSelect($item["select"], "ia_value", "ia_value"));
        $rend->getItemRenderer()->setValueKey("ia_value");
        $rend->getItemRenderer()->setLabelKey("ia_value");
        $rend->setInputAttribute("onChange", "javascript:filterChanged(this, 'ia', true)");
        $rend->setInputAttribute("filter_group", "ia");
        $field->setValue($item["value"]);

        $rend->render();
        echo "</div>";//InputComponent
    }
}
catch (Exception $e) {
    echo $e;
}

echo "</form>";

echo "<button class='ColorButton' onClick='javascript:clearFilters()'>" . tr("Изчисти филтрите") . "</button>";

echo "</div>";//filters

echo "</div>"; //column categories

echo "<div class='column product_list'>";
//    Session::set("search_home", false);
$page->renderCategoryPath($nodeID);

//     $ksc->render();

echo "<div class='clear'></div>";
//   $view->enablePaginators(false);
$view->render();

echo "</div>";

Session::set("shopping.list", $page->getPageURL());

$page->finishRender();
?>
