<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductColorsBean.php");

include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductColorPhotosBean.php");

include_once("components/TableView.php");
include_once("components/renderers/cells/TableImageCellRenderer.php");
include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$rc = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name"));

$page->setAccessibleTitle("Color Scheme");

$bean = new ProductColorsBean();

$h_delete = new DeleteItemResponder($bean);


// $search_fields = array("prodID", "product_code", "product_name", "color", "size");
// $ksc = new KeywordSearch($search_fields);

$select_colors = $bean->select();
$select_colors->fields = " pclr.*, p.product_name ";
$select_colors->from = " product_colors pclr LEFT JOIN products p ON p.prodID = pclr.prodID ";
$select_colors->where = " pclr.prodID = " . $rc->getID();

$page->setName(tr("Color Scheme") . ": " . $rc->getData("product_name"));

// $ksc->processSearch($select_products);

$view = new TableView(new SQLQuery($select_colors, $bean->key()));
$view->setCaption("Color Schemes List");
// $view->setDefaultOrder(" ORDER BY item_date DESC ");
// $view->search_filter = " ORDER BY day_num ASC ";
$view->addColumn(new TableColumn($bean->key(), "ID"));

// if ($prodID<1) {
//   $view->addColumn(new TableColumn("product_name","Product Name"));
// }

$view->addColumn(new TableColumn("photo", "Scheme Photos"));

$view->addColumn(new TableColumn("color", "Color Name"));

$view->addColumn(new TableColumn("color_photo", "Color Chip"));

$view->addColumn(new TableColumn("actions", "Actions"));

$ticr1 = new TableImageCellRenderer(-1, 64);
$ticr1->setBean(new ProductColorsBean(), "color_photo");
$view->getColumn("color_photo")->setCellRenderer($ticr1);

$ticr2 = new TableImageCellRenderer(-1, 64);
$ticr2->setBean(new ProductColorPhotosBean());
$ticr2->setLimit(0);
$view->getColumn("photo")->setCellRenderer($ticr2);

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());

$act->getActions()->append(new RowSeparator());

$act->getActions()->append(new Action("Photos", "gallery/list.php", array(new DataParameter($bean->key(), $bean->key()))));

$view->getColumn("actions")->setCellRenderer($act);


$page->startRender();

// $ksc->render();
$view->render();

$page->finishRender();

?>
