<?php
include_once("class/pages/ProductsPage.php");
include_once("class/utils/ProductsSQL.php");
include_once("components/NestedSetTreeView.php");
include_once("components/renderers/items/TextTreeItem.php");

class ProductListPage extends ProductsPage
{

    public $treeView = NULL;

    public function __construct()
    {
        parent::__construct();

        //renderer for the tree view
        $ir = new TextTreeItem();
        $ir->setLabelKey("category_name");

        $treeView = new NestedSetTreeView();
        $treeView->setItemRenderer($ir);

        $treeSelect = $this->product_categories->selectTree(array("category_name"));

        $treeQry = new SQLQuery($treeSelect, $this->product_categories->key(), $this->product_categories->getTableName());

        $treeView->setIterator($treeQry);

        $treeView->setName("products_tree");
        $treeView->open_all = FALSE;

        $this->treeView = $treeView;

        $this->addCSS(LOCAL . "/css/product_list.css");
        $this->addJS(LOCAL . "/js/product_list.js");

    }

    protected function constructTitle()
    {
        $nodeID = $this->treeView->getSelectedID();
        $title = array();
        $title[] = $this->getSection();
        if ($nodeID > 0) {
            $category_path = $this->product_categories->getParentNodes($nodeID, array("category_name"));
            foreach ($category_path as $idx => $catinfo) {
                $title[] = $catinfo["category_name"];
            }
        }

        $this->setTitle(constructSiteTitle($title));
    }

    public function getCategoryPath()
    {
        $category_path = array();

        $nodeID = $this->treeView->getSelectedID();

        if ($nodeID > 0) {
            $category_path = $this->product_categories->getParentNodes($nodeID, array("category_name"));
        }

        return $category_path;
    }

}

?>
