<?php
include_once("class/pages/ProductsPage.php");
include_once("class/utils/ProductsSQL.php");
include_once("components/NestedSetTreeView2.php");
include_once("components/renderers/items/TextTreeItemRenderer.php");


class ProductListPage extends ProductsPage
{

    public $treeView = NULL;

    public function __construct()
    {
        parent::__construct();

        $treeView = new NestedSetTreeView();
        $treeView->setSource($this->product_categories);
        $treeView->setName("products_tree");
        $treeView->open_all = false;
        $treeView->list_label = "category_name";

        //renderer for the tree view
        $ir = new TextTreeItemRenderer();
        $treeView->setItemRenderer($ir);

        $this->treeView = $treeView;

        $this->addCSS(LOCAL . "css/product_list.css?ver=1.0");
        $this->addJS(LOCAL . "js/product_list.js?ver=1.2");

    }

    protected function constructTitle()
    {
        $nodeID = $this->treeView->getSelectedID();
        $title = array();
        $title[] = $this->getSection();
        if ($nodeID > 0) {
            $category_path = $this->product_categories->parentCategories($nodeID);
            foreach ($category_path as $idx => $catinfo) {
                $title[] = $catinfo["category_name"];
            }
        }

        $this->setPreferredTitle(constructSiteTitle($title));
    }


    public function getCategoryPath()
    {
        $category_path = array();

        $nodeID = $this->treeView->getSelectedID();

        if ($nodeID > 0) {
            $category_path = $this->product_categories->parentCategories($nodeID);
        }

        return $category_path;
    }

}

?>
