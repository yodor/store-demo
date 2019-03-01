<?php
include_once("class/pages/ProductsPage.php");
include_once("class/components/renderers/items/ProductListItem.php");

class ProductDetailsPage extends ProductsPage
{

    protected $sellable = NULL;
    protected $list_item = NULL;
    
    public function __construct()
    {
        parent::__construct();
        $this->list_item = new ProductListItem();
    }

    protected function dumpCSS()
    {
        parent::dumpCSS();
        echo "<link rel='stylesheet' href='".SITE_ROOT."css/product_details.css?ver=1.7' type='text/css'>";
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
    
//     protected function constructPathActions()
//     {
//     
//         $actions = parent::constructPathActions();       
// 
//         $actions[] = new Action($this->sellable["product_name"], "", array());
//             
//         return $actions;
//         
//     }
    public function renderSameCategoryProducts()
    {
        echo "<div class='caption'>".tr("Още продукти от тази категория")."</div>";
        
        
        $sel = new ProductsQuery();
        $sel->order_by = " pi.view_counter ";
        $sel->group_by = " pi.prodID, pi.color ";
        $sel->limit = "4";
        $sel->where = " p.section='{$this->section}' AND p.catID='{$this->sellable["catID"]}' ";

        $db = DBDriver::get();
        
//         echo $sel->getSQL();
        $res = $db->query($sel->getSQL());
        if (!$res) throw new Exception("Unable to query products from section='{$this->section}' and catID='{$this->sellable["catID"]}'. Error: ".$db->getError());
        
        while ($row = $db->fetch($res)) {
            $this->list_item->setItem($row);
            $this->list_item->render();
        }
        $db->free($res);
    }
    public function renderMostOrderedProducts()
    {
        echo "<div class='caption'>".tr("Най-продавани от тази секция")."</div>";
        
        
        $sel = new ProductsQuery();
        $sel->order_by = " pi.order_counter ";
        $sel->group_by = " pi.prodID, pi.color ";
        $sel->limit = "4";
        $sel->where = " p.section='{$this->section}'  ";

        $db = DBDriver::get();
        
//         echo $sel->getSQL();
        $res = $db->query($sel->getSQL());
        if (!$res) throw new Exception("Unable to query products from section='{$this->section}' and catID='{$this->sellable["catID"]}'. Error: ".$db->getError());
        
        while ($row = $db->fetch($res)) {
            $this->list_item->setItem($row);
            $this->list_item->render();
        }
        $db->free($res);
    }
}

?>
