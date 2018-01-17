<?php
include_once("class/pages/ProductsPage.php");
include_once("class/utils/ProductsQuery.php");
include_once("lib/components/NestedSetTreeView2.php");
include_once("lib/components/renderers/items/TextTreeItemRenderer.php");


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
    }

    protected function dumpCSS()
    {
        parent::dumpCSS();
        echo "<link rel='stylesheet' href='".SITE_ROOT."css/related_tree.css?ver=1.0' type='text/css'>";
        echo "\n";
    }
    
    protected function dumpJS()
    {
        parent::dumpJS();
 	echo "<script type='text/javascript' src='".SITE_ROOT."js/product_list.js?ver=1.2'></script>";
 	echo "\n";
    }

    protected function dumpMetaTags()
    {
        parent::dumpMetaTags();
    }

    public function beginPage()
    {
        parent::beginPage();
    }

    public function finishPage()
    {
	parent::finishPage();
    }

    protected function constructTitle()
    {
        $nodeID = $this->treeView->getSelectedID();
        $title = array();
        $title[] = $this->getSection();
        if ($nodeID>0) {
            $category_path = $this->product_categories->parentCategories($nodeID);
            foreach ($category_path as $idx=>$catinfo) {
                $title[] = $catinfo["category_name"];
            }
        }

        $this->setPreferredTitle(constructSiteTitle($title));
    }
    
    
    public function getCategoryPath()
    {
        $category_path = array();
        
        $nodeID = $this->treeView->getSelectedID();
        
        if ($nodeID>0) {
            $category_path = $this->product_categories->parentCategories($nodeID);
        }
        
        return $category_path;
    }

}

?>
