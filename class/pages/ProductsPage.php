<?php
include_once("class/pages/StorePage.php");
include_once("class/utils/ProductsSQL.php");
include_once("class/beans/ProductCategoriesBean.php");
include_once("components/Action.php");

class ProductsPage extends StorePage
{


    public $derived;

    public $product_categories;

    public function __construct()
    {
        parent::__construct();

        $this->product_categories = new ProductCategoriesBean();

        $derived = new ProductsSQL();

        if ($this->section) {
            $derived->where()->add("p.section", "'{$this->section}'");
        }

        $this->derived = $derived;

    }

    public function renderCategoryPath()
    {
        $actions = $this->constructPathActions();

        echo "<div class='caption category_path'>";
        if ($actions) {
            Action::RenderActions($actions);
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

        $actions[] = new Action(tr("Начало"), LOCAL . "/index.php", array());

        if ($this->section) {
            $qarr = array();
            $qarr["section"] = $this->section;
            $link = LOCAL . "/products.php" . queryString($qarr);
            $actions[] = new Action($this->section, $link, array());
        }

        $category_path = $this->getCategoryPath();

        foreach ($category_path as $idx => $category) {
            $qarr = array();
            $qarr["section"] = $this->section;
            $qarr["catID"] = $category["catID"];
            $link = LOCAL . "/products.php" . queryString($qarr);
            $actions[] = new Action($category["category_name"], $link, array());
        }

        return $actions;
    }

}

?>
