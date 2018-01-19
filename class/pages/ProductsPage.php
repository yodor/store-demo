<?php
include_once("class/pages/StorePage.php");
include_once("class/utils/ProductsQuery.php");
include_once("lib/components/renderers/ActionRenderer.php");
include_once("class/beans/ProductCategoriesBean.php");


class ProductsPage extends StorePage
{

    public $derived_table = NULL;
    public $derived = NULL;
    
    public $action_renderer = NULL;
    
    public $product_categories = NULL;
    
     
    public function __construct()
    {
        parent::__construct();
    
        $this->product_categories = new ProductCategoriesBean();
        
        $this->action_renderer = new ActionRenderer();


        $derived = new ProductsQuery();
        

        if ($this->section) {
            $derived->where=" p.section = '{$this->section}' ";
        }
        
        $this->derived = $derived;
        
    }

    public function renderCategoryPath()
    {
        $actions = $this->constructPathActions();
        
        echo "<div class='caption category_path'>";
        if ($actions) {
            $this->action_renderer->renderActions($actions);
        }
        echo "</div>";
    }
    
    //selected catID from tree or sellable catID
    protected function getCategoryPath()
    {
        return array();
    }
    
    protected function constructPathActions()
    {
        $actions = array();

//         $back_path = Session::get("shopping.list", "");
//         $back_action = NULL;
//         echo $back_path;

//         if (strlen($back_path)>0) {
//             $back_action = new Action("&larr;", $back_path, array());
//             $actions[]=$back_action;
//         }
        
        $actions[] = new Action(tr("Начало"), SITE_ROOT."index.php", array());

        
        $category_path = $this->getCategoryPath();
        
        foreach ($category_path as $idx=>$category) {
            $qarr["section"] = $this->section;
            $qarr["catID"] = $category["catID"];
            $link = SITE_ROOT."products.php".queryString($qarr);
            $actions[] = new Action($category["category_name"], $link, array());
        }
        
        return $actions;
    }
    
    protected function dumpCSS()
    {
        parent::dumpCSS();
//         echo "<link rel='stylesheet' href='".SITE_ROOT."css/ProductsPage.css?ver=1.0' type='text/css'>";
//         echo "\n";
    }
    
    protected function dumpJS()
    {
        parent::dumpJS();

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

}

?>
