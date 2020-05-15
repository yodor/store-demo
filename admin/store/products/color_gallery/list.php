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

$action_back = new Action("", Session::Get("products.list"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back to Products");
$page->addAction($action_back);

$rc = new RequestBeanKey(new ProductsBean(), "../list.php", array("product_name"));

$menu = array();

$action_add = new Action("", "add.php" . $rc->getQuery(), array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Color Scheme");
$page->addAction($action_add);

$page->setAccessibleTitle("Color Scheme");

$bean = new ProductColorsBean();

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

// $search_fields = array("prodID", "product_code", "product_name", "color", "size");
// $ksc = new KeywordSearch($search_fields);

$select_colors = $bean->select();
$select_colors->fields = " pclr.*, p.product_name ";
$select_colors->from = " product_colors pclr LEFT JOIN products p ON p.prodID = pclr.prodID ";
$select_colors->where = " pclr.prodID = " . $rc->getID();

$page->setCaption(tr("Color Scheme") . ": " . $rc->getData("product_name"));

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
$act->addAction(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->addAction(new PipeSeparator());
$act->addAction($h_delete->createAction());

$act->addAction(new RowSeparator());

$act->addAction(new Action("Photos", "gallery/list.php", array(new DataParameter($bean->key(), $bean->key()))));

$view->getColumn("actions")->setCellRenderer($act);

Session::Set("product.color_scheme", $page->getPageURL());

$page->startRender($menu);

// $ksc->render();
$view->render();

$page->finishRender();

?>
