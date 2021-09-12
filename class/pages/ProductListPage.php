<?php
include_once("class/pages/StorePage.php");

include_once("components/NestedSetTreeView.php");
include_once("components/renderers/items/TextTreeItem.php");
include_once("components/Action.php");

include_once("class/components/ProductListFilter.php");

include_once("components/TableView.php");
include_once("components/ItemView.php");

include_once("class/components/renderers/items/ProductListItem.php");
include_once("class/beans/SellableProducts.php");

class ProductListPage extends StorePage
{

    /**
     * @var SectionsBean|null
     */
    protected $sections = NULL;

    /**
     * Currently selected section (processed as get variable)
     * @var string
     */
    protected $section = "";


    /**
     * @var ProductCategoriesBean
     */
    public $product_categories;

    /**
     * @var NestedSetTreeView|null
     */
    protected $treeView = NULL;

    /**
     * filtered version of products_select
     * @var SQLSelect
     */
    protected $select;

    /**
     * Filters form component
     * @var ProductListFilter
     */
    protected $filters;

    /**
     * Products list component
     * @var ItemView
     */
    protected $view;

    const SECTION_KEY = "section";

    /**
     * @var SellableProducts|null
     */
    protected $bean = null;

    public function __construct(SellableProducts $bean=NULL)
    {
        parent::__construct();

        $this->bean = $bean;
        if (is_null($bean)) {
//            $this->product_select = new ProductsSQL();
//            $psql = new ProductsSQL();
//            $psql->createView("sellable_inventory");

            $this->bean = new SellableProducts();

        }



        //echo $this->product_select->getSQL();
        //        exit;

        $this->sections = new SectionsBean();

        $this->product_categories = new ProductCategoriesBean();


        $search_fields = array("product_name", "product_description", "long_description", "keywords");
        //$search_fields = array("p.product_name", "p.product_description", "p.long_description", "p.keywords");

        $this->keyword_search->getForm()->setFields($search_fields);

        //$this->keyword_search->getForm()->setCompareExpression("relation.inventory_attributes", array("%:{keyword}|%", "%:{keyword}"));


        //Initialize product categories tree
        $treeView = new NestedSetTreeView();
        $treeView->setName("products_tree");
        $treeView->open_all = FALSE;

        //default - all categories not filtered or aggregated
        $treeSelect = $this->product_categories->selectTree(array("category_name"));
        $treeQry = new SQLQuery($treeSelect, $this->product_categories->key(), $this->product_categories->getTableName());
        $treeView->setIterator($treeQry);

        //item renderer for the tree view
        $ir = new TextTreeItem();
        $ir->setLabelKey("category_name");
        $ir->getTextAction()->addDataAttribute("title", "category_name");
        $ir->getTextAction()->getURLBuilder()->add(new DataParameter("catID"));
        $ir->getTextAction()->getURLBuilder()->setClearPageParams(false);
        $ir->getTextAction()->getURLBuilder()->setClearParams(...array("page"));
        $treeView->setItemRenderer($ir);

        $this->treeView = $treeView;


        //main products select - no grouping here as filters are not applied yet
        $this->select = clone $this->bean->select();

        //empty filters - renderer iterators are not set yet
        $this->filters = new ProductListFilter();

        //default products select all products from all categories
        $products_list = clone $this->select;
        $products_list->group_by = " prodID, color ";


//echo $products_list->getSQL();

        $this->view = new ItemView(new SQLQuery($products_list, "prodID"));

        $this->view->setItemRenderer(new ProductListItem());
        $this->view->setItemsPerPage(12);

        $sort_price = new PaginatorSortField("sell_price", "Цена", "", "ASC");
        $this->view->getPaginator()->addSortField($sort_price);

        $sort_prod = new PaginatorSortField("prodID", "Най-нови", "", "DESC");
        $this->view->getPaginator()->addSortField($sort_prod);

        $this->view->getTopPaginator()->view_modes_enabled = TRUE;

        $this->addCSS(LOCAL . "/css/product_list.css");
        $this->addJS(LOCAL . "/js/product_list.js");
    }

    //process get vars
    public function processInput()
    {

        //filter precedence
        // 1 section
        // 2 keyword search
        // 3 category
        // 4 filters

        if (isset($_GET[self::SECTION_KEY])) {
            $section = DBConnections::Get()->escape($_GET[self::SECTION_KEY]);
            $qry = $this->sections->queryField("section_title", $section, 1);
            $num = $qry->exec();
            if ($num < 1) {
                $this->section = "";
            }
            else {
                $this->section = $section;

            }
        }

        if ($this->section) {
            $this->select->where()->add("section", "'{$this->section}'");
        }

        $this->keyword_search->processInput();

        if ($this->keyword_search->isProcessed()) {
            $this->keyword_search->getForm()->prepareClauseCollection("AND")->copyTo($this->select->where());
        }

        //select the active category node
        if (isset($_GET[$this->product_categories->key()])) {
            $this->treeView->setSelectedID((int)$_GET[$this->product_categories->key()]);
        }

        //update filters to select values from aggregated products and selected category
        $nodeID = $this->treeView->getSelectedID();

        if ($this->filters instanceof ProductListFilter) {
            $filters_select = clone $this->select;
            //$filters_select = $this->select;

            if ($nodeID > 0) {
                //match p.catID to be inside nodeID and its child node IDs
                $filters_select = $this->product_categories->selectChildNodesWith($filters_select, "sellable_products", $nodeID);

                $this->loadCategoryPath($nodeID);
            }

            $filters_select->fields()->unset("catID");
            $filters_select->fields()->unset("category_name");

            $this->filters->getForm()->setSQLSelect($filters_select);
            $this->filters->getForm()->createAttributeFilters();
            $this->filters->getForm()->updateIterators();

            $this->filters->processInput();

            //update the main select filtered by color and/or size
            $filters_where = $this->filters->getForm()->prepareClauseCollection(" AND ");
            //var_dump($filters_where);
            $filters_where->copyTo($this->select->where());
        }

//        $having_clauses = $this->filters->getForm()->prepareHavingClause();
//        echo $clauses->getSQL(false);

//        $this->select->having = $clauses->getSQL(false);
//        echo $this->select->getSQL()."<HR>";




        $products_list = clone $this->select;

        $products_list->fields()->unset("catID");
        $products_list->fields()->unset("category_name");

        //match all products whose catID is nodeID or nodeID child nodes
        $products_list = $this->product_categories->selectChildNodesWith($products_list, "sellable_products", $nodeID, array("catID", "category_name"));
        $products_list->group_by = " prodID, color ";

        //echo $products_list->getSQL();

        //primary key is prodID as we group by prodID(Products) not piID(ProductInventory)
        $this->view->setIterator(new SQLQuery($products_list, "prodID"));



        //$this->select is already filtered at this point from filters_processor
        $products_tree = clone $this->select;
        //select only required fields for the treeView
        $products_tree->fields()->reset();
        $products_tree->fields()->set("prodID", "catID");
        //
        $products_tree->group_by = " prodID, color ";

        $products_tree = $products_tree->getAsDerived();
        $products_tree->fields()->set("relation.prodID", "relation.catID");

        //needs getAsDerived - sets grouping and ordering on the returned select, suitable as treeView iterator
        $aggregateSelect = $this->product_categories->selectTreeRelation($products_tree, "relation", "prodID", array("category_name"));
        //echo $aggregateSelect->getSQL();

        //$aggregateSelect->fields()->removeValue("related_count");
        $this->treeView->setIterator(new SQLQuery($aggregateSelect, $this->product_categories->key()));

    }

    public function isProcessed(): bool
    {
        return $this->keyword_search->isProcessed();

    }

    public function renderCategoriesTree()
    {
        $this->treeView->render();
    }

    public function renderProductFilters()
    {
        if ($this->filters instanceof ProductListFilter) {
            $this->filters->render();
            echo "<button class='ColorButton' onClick='clearFilters()'>" . tr("Изчисти филтрите") . "</button>";
        }
    }

    public function renderProductsView()
    {

        $this->view->render();
    }

    protected function constructTitle()
    {
        if ($this->keyword_search->isProcessed()) {
            $this->setTitle("Резултати от търсене");
            return;
        }

        $title = array();

        if ($this->section) {
            $title[] = $this->section;
        }
        else {
            $title[] = "Продукти";
        }

        foreach ($this->category_path as $idx => $catinfo) {
            $title[] = $catinfo["category_name"];
        }

//        $nodeID = $this->treeView->getSelectedID();
//        if ($nodeID > 0) {
//
//        }

        $this->setTitle(constructSiteTitle($title));
    }

    protected $category_path = array();

    public function getCategoryPath()
    {
        return $this->category_path;
    }

    protected function loadCategoryPath(int $nodeID)
    {
        $category_path = $this->product_categories->getParentNodes($nodeID, array("catID", "category_name"));

        if ($category_path) {
            $this->category_path = $category_path;
        }
        else {
            $this->category_path = array();
        }
    }

    public function renderCategoryPath()
    {
        $actions = $this->constructPathActions();

        echo "<h1 class='Caption category_path'>";
        if ($actions) {
            Action::RenderActions($actions);
        }
        echo "</h1>";
    }

    protected function constructPathActions()
    {
        $actions = array();

        $actions[] = new Action(tr("Начало"), LOCAL . "/home.php", array());

        $link = new URLBuilder();
        $link->buildFrom(LOCAL."/products/list.php");
        if ($this->section) {
            $link->add(new URLParameter("section", $this->section));
        }

        if ($this->keyword_search->isProcessed()) {
            $actions[] = new Action(tr("Резултати от търсене"), $this->getPageURL(), array());
        }
        else if ($this->section) {
            $actions[] = new Action($this->section, $link->url(), array());
        }
        else {
            $actions[] = new Action(tr("Продукти"), $link->url(), array());
        }

        $link->add(new DataParameter("catID"));

        foreach ($this->category_path as $idx => $category) {

            $link->setData($category);

            $category_action = new Action($category["category_name"], $link->url(), array());
            $category_action->translation_enabled = false;
            $actions[] = $category_action;

        }

        return $actions;
    }

    /**
     * Return the active selected section for products filtering
     * @return string
     */
    public function getSection() : string
    {
        return $this->section;
    }

    public function renderContents()
    {
        $this->renderCategoryPath();

        echo "<div class='column left' section='{$this->section}'>";

            echo "<div class='categories panel'>";

                echo "<div class='Caption' ><div class='toggle' onclick='togglePanel(this)'><div></div></div>" . tr("Категории") . "</div>";

                echo "<div class='viewport'>";
                $this->renderCategoriesTree();
                echo "</div>";

            echo "</div>"; //tree

            echo "<div class='filters panel'>";

                echo "<div class='Caption' ><div class='toggle' onclick='togglePanel(this)'><div></div></div>" . tr("Филтри") . "</div>";

                echo "<div class='viewport'>";
                $this->renderProductFilters();
                echo "</div>";

            echo "</div>";//filters

        echo "</div>"; //column left

        echo "<div class='column product_list'>";

        $this->renderProductsView();

        echo "</div>";

        Session::set("shopping.list", $this->getPageURL());

        ?>
        <script type="text/javascript">
            function togglePanel(elm)
            {
                let e = $(elm).parents(".panel").first().children(".viewport").first();
                let is_hidden = e.css("display");
                if (is_hidden == "none") {
                    e.css("display", "inline-block");
                }
                else {
                    e.css("display", "none");
                }


            }
        </script>
        <?php
    }
}

?>
