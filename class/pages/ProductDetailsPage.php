<?php
include_once("class/pages/ProductsPage.php");


class ProductDetailsPage extends ProductsPage
{

    protected $sellable = NULL;
    
    public function __construct()
    {
        parent::__construct();

    }

    protected function dumpCSS()
    {
        parent::dumpCSS();
        echo "<link rel='stylesheet' href='".SITE_ROOT."css/product_details.css?ver=1.5' type='text/css'>";
        echo "\n";
    }
    
    protected function dumpJS()
    {
        parent::dumpJS();
 	echo "<script type='text/javascript' src='".SITE_ROOT."js/product_details.js?ver=1.2'></script>";
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
    
    public function setSellableItem($sellable)
    {
        $this->setSection($sellable["section"]);
        $this->sellable = $sellable;
    }
    
    protected function constructTitle()
    {
        $title = array();
        $title[] = $this->sellable["section"];

        $category_path = $this->product_categories->parentCategories($this->sellable["catID"]);
        foreach ($category_path as $idx=>$catinfo) {
            $title[] = $catinfo["category_name"];
        }
        $title[] = $this->sellable["product_name"];
        $this->setPreferredTitle(constructSiteTitle($title));
    }
    
    protected function selectActiveMenu()
    {
        //$this->selectActiveMenus = false;
        $main_menu = $this->menu_bar->getMainMenu();
        $main_menu->setUnselectedAll();
        
        $items = $main_menu->getMenuItems();
        foreach ($items as $idx=>$item) {
            if (strcmp($item->getTitle(), $this->section)==0) {
                $main_menu->setSelectedItem($item);
            }
        }
        $main_menu->updateSelectedMenu();
    }
    
    public function getCategoryPath()
    {
        return $this->product_categories->parentCategories($this->sellable["catID"]);
    }
    
    protected function constructPathActions()
    {
    
        $actions = parent::constructPathActions();       

        $actions[] = new Action($this->sellable["product_name"], "", array());
            
        return $actions;
        
    }
}

?>
