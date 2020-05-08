<?php
include_once("pages/SparkPage.php");

include_once("utils/MainMenu.php");
include_once("components/MenuBarComponent.php");
include_once("components/KeywordSearchComponent.php");

include_once("forms/InputForm.php");
include_once("forms/renderers/FormRenderer.php");
include_once("forms/processors/FormProcessor.php");
include_once("input/DataInputFactory.php");

include_once("beans/MenuItemsBean.php");
include_once("class/beans/SectionsBean.php");

include_once("class/utils/Cart.php");
include_once("auth/UserAuthenticator.php");

class StorePage extends SparkPage
{

    protected $menu_bar = NULL;

    public $sections = NULL;

    protected $section = "";

    public $keyword_search = NULL;

    protected $cart = NULL;

    public $client_name = "";

    public function __construct()
    {
        $this->auth = new UserAuthenticator();
        $this->loginURL = LOCAL."account/login.php";

        parent::__construct();

        if ($this->context) {

            $this->client_name = $this->context->getData()->get(SessionData::FULLNAME);

        }

        $this->cart = new Cart();

        $menu = new MainMenu();

        $menu->setMenuBeanClass("MenuItemsBean", "");
        $menu->constructMenuItems(0, NULL, "menuID", "menu_title");

        $this->menu_bar = new MenuBarComponent($menu);

        $this->menu_bar->setName("StorePage");

        $this->sections = new SectionsBean();

        // 	$this->menu_bar->getItemRenderer()->disableSubmenuRenderer();

        $this->addMeta("viewport", "width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0");


        if (isset($_GET["section"])) {
            $section = DBConnections::Get()->escape($_GET["section"]);
            $qry = $this->sections->queryField("section_title", $section, 1);
            $num = $qry->exec();
            if ($num < 1) {
                $this->section = "";
            }
            else {
                $this->section = $section;
            }
        }

        //construct filters 
        $search_fields = array("relation.product_name", "relation.product_summary", "relation.keywords", "relation.color", "relation.inventory_attributes");

        $ksc = new KeywordSearchComponent($search_fields, "relation");
        $ksc->getForm()->getInput("keyword")->getRenderer()->setInputAttribute("placeholder", "Търси ...");
        $ksc->getForm()->getRenderer()->setAttribute("method", "get");
        $ksc->getForm()->getRenderer()->setAttribute("action", LOCAL . "products.php");
        $ksc->getForm()->setCompareExpression("relation.inventory_attributes", array("%:{keyword}|%", "%:{keyword}"));
        $this->keyword_search = $ksc;


        $this->addCSS(LOCAL . "/css/store.css");
        $this->addCSS("//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css");

        $this->addJS("//code.jquery.com/ui/1.11.4/jquery-ui.js");
        $this->addJS(SPARK_LOCAL . "/js/URI.js");
        $this->addJS(SPARK_LOCAL . "/js/GalleryView.js");
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function getMenuBar()
    {
        return $this->menu_bar;
    }

    public function getSection()
    {
        return $this->section;
    }

    public function setSection($section)
    {
        $this->section = $section;
    }

    public function startRender()
    {
        parent::startRender();

        echo "\n<!-- startRender StorePage-->\n";


        $this->selectActiveMenu();


        echo "<div class='full' align=center>";

        echo "<div class='header '>";
        echo "<div class='aside'>";

        echo "<div class='links'>";
        echo "<div class='login_pane'>";
        if ($this->context) {
            echo "<a href='" . LOCAL . "account/' class='account_link'>{$this->client_name}</a>";
        }
        else {
            echo "<a href='" . LOCAL . "account/login.php' class='account_link'>" . tr("Вход") . " / " . tr("Регистрация") . "</a>";
        }
        echo "</div>";

        echo "<a href='" . LOCAL . "checkout/cart.php' class='checkout_link'>" . tr("Кошница") . " (" . $this->cart->getItemCount() . ")</a>";
        echo "<a href='" . LOCAL . "contacts.php' class='contacts_link'>" . tr("Контакти") . "</a>";

        echo "</div>";

        echo "<div class='search_pane'>";
        $this->keyword_search->render();
        echo "<div class='clear'></div>";
        echo "</div>";

        echo "</div>";

        echo "<div class='clear'></div>";

        echo "<a class='logo' href='" . LOCAL . "'></a>";
        echo "</div>";

        echo "</div>";

        echo "<div class='full border_bottom' align=center>";
        $this->menu_bar->render();
        echo "</div>";

        echo "<div class='full' align=center>";
        echo "<div class='main_content container'>"; //inner contents

    }

    protected function selectActiveMenu()
    {
        $main_menu = $this->menu_bar->getMainMenu();

        $main_menu->selectActiveMenus(MainMenu::FIND_INDEX_LOOSE);

    }

    protected function constructTitle()
    {
        if (strlen($this->getPreferredTitle()) > 0) return;

        $main_menu = $this->menu_bar->getMainMenu();

        $this->setPreferredTitle(constructSiteTitle($main_menu->getSelectedPath()));
    }

    public function finishRender()
    {


        echo "</div>"; //main_content
        echo "</div>"; //full align=center

        echo "<div class='full black' align=center>";
        echo "<div class='footer container'>";

        echo "<div class='links'>";
        //main menu and other links
        echo "<div class='menu'>";
        $items = $this->menu_bar->getMainMenu()->getMenuItems();
        foreach ($items as $idx => $item) {
            echo "<a href='{$item->getHref()}'>" . tr($item->getTitle()) . "</a>";
        }
        echo "</div>";//menu

        echo "<div class='other'>";
        echo "<a href='" . LOCAL . "terms_usage.php'>" . tr("Условия за ползване") . "</a>";
        echo "<a href='" . LOCAL . "terms_delivery.php'>" . tr("Условия за доставка") . "</a>";
        echo "</div>";

        echo "</div>";

        echo "<div class='social_links'>";
        //social media links
        echo "</div>";

        echo "<div class='logo'></div>";

        echo "</div>";//footer
        echo "</div>";//full

        echo "<div class='full black footer_bottom' align=center>";
        echo tr("MM Fashion Shop. Web Design - saturnosoft.biz");
        echo "</div>";

        echo "\n";
        echo "\n<!-- finishRender StorePage-->\n";

        $this->constructTitle();

        parent::finishRender();


    }

}

?>
