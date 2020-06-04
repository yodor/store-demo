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

        $this->addCSS(LOCAL . "/css/product_details.css");
        $this->addCSS(LOCAL . "/css/product_details.css");

        $this->addJS(LOCAL . "/js/product_details.js");
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

        $category_path = $this->product_categories->getParentNodes($this->sellable["catID"], array("category_name"));
        foreach ($category_path as $idx => $catinfo) {
            $title[] = $catinfo["category_name"];
        }
        $title[] = $this->sellable["product_name"];
        $this->setTitle(constructSiteTitle($title));
    }

    protected function selectActiveMenu()
    {
        //$this->selectActiveMenus = false;
        $main_menu = $this->menu_bar->getMainMenu();
        $main_menu->unselectAll();

        $items = $main_menu->getMenuItems();
        foreach ($items as $idx => $item) {
            if (strcmp($item->getTitle(), $this->section) == 0) {
                $main_menu->setSelectedItem($item);
            }
        }
        $main_menu->constructSelectedPath();
    }

    public function getCategoryPath()
    {
        return $this->product_categories->getParentNodes($this->sellable["catID"], array("category_name"));
    }

//    protected function constructPathActions()
//    {
//
//        $actions = parent::constructPathActions();
//
//        $actions[] = new Action($this->sellable["product_name"], "", array());
//
//        return $actions;
//
//    }

    public function renderSameCategoryProducts()
    {
        echo "<div class='caption'>" . tr("Още продукти от тази категория") . "</div>";

        $sel = new ProductsSQL();
        $sel->order_by = " pi.view_counter ";
        $sel->group_by = " pi.prodID, pi.color ";
        $sel->limit = "4";
        $sel->where()->add("p.section", "'{$this->section}'")->add("p.catID", $this->sellable["catID"]);

        $db = DBConnections::Get();

        //         echo $sel->getSQL();
        $res = $db->query($sel->getSQL());
        if (!$res) throw new Exception("Unable to query products from section='{$this->section}' and catID='{$this->sellable["catID"]}'. Error: " . $db->getError());

        while ($row = $db->fetch($res)) {
            $this->list_item->setData($row);
            $this->list_item->render();
        }
        $db->free($res);
    }

    public function renderMostOrderedProducts()
    {
        echo "<div class='caption'>" . tr("Най-продавани от тази секция") . "</div>";

        $sel = new ProductsSQL();
        $sel->order_by = " pi.order_counter ";
        $sel->group_by = " pi.prodID, pi.color ";
        $sel->limit = "4";
        $sel->where()->add("p.section", "'{$this->section}'");

        $db = DBConnections::Get();

        //         echo $sel->getSQL();
        $res = $db->query($sel->getSQL());
        if (!$res) throw new Exception("Unable to query products from section='{$this->section}' and catID='{$this->sellable["catID"]}'. Error: " . $db->getError());

        while ($row = $db->fetch($res)) {
            $this->list_item->setData($row);
            $this->list_item->render();
        }
        $db->free($res);
    }
}

?>
