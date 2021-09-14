<?php
include_once("pages/SparkPage.php");

include_once("utils/MainMenu.php");
include_once("components/MenuBarComponent.php");
include_once("components/KeywordSearch.php");

include_once("forms/InputForm.php");
include_once("forms/renderers/FormRenderer.php");
include_once("forms/processors/FormProcessor.php");
include_once("input/DataInputFactory.php");

include_once("beans/MenuItemsBean.php");
include_once("class/beans/SectionsBean.php");

include_once("auth/UserAuthenticator.php");

include_once("utils/CurrencyConverter.php");

include_once("class/beans/ProductCategoriesBean.php");

include_once("utils/Cart.php");

class StorePage extends SparkPage
{

    protected $menu_bar = NULL;



    /**
     * @var KeywordSearch|null
     */
    protected $keyword_search = NULL;

    public $client_name = "";

    public function __construct()
    {

        $this->auth = new UserAuthenticator();
        $this->loginURL = LOCAL . "/account/login.php";

        parent::__construct();

        $this->authorize();

        if ($this->context) {

            $this->client_name = $this->context->getData()->get(SessionData::FULLNAME);

        }

        $menu = new MainMenu();

        $menu->setBean(new MenuItemsBean());
        $menu->construct();

        $this->menu_bar = new MenuBarComponent($menu);

        $this->menu_bar->setName("StorePage");
        $this->menu_bar->toggle_first = TRUE;

        $ksc = new KeywordSearch();
        //just initialize the keyword form here. Search fields are initialized in ProductsListPage as form is posted there
        $ksc->getForm()->getInput("keyword")->getRenderer()->setInputAttribute("placeholder", "Търси ...");
        $ksc->getForm()->getRenderer()->setAttribute("method", "get");
        $ksc->getForm()->getRenderer()->setAttribute("action", LOCAL . "/products/list.php");

        $ksc->getButton("search")->setContents("");

        $this->keyword_search = $ksc;

        $this->addCSS(LOCAL . "/css/store.css");
        //$this->addCSS("//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css");

        //$this->addJS("//code.jquery.com/ui/1.11.4/jquery-ui.js");
        $this->addJS(SPARK_LOCAL . "/js/URI.js");

        $pc = new ProductCategoriesBean();
        $qry = $pc->query("category_name");
        $num = $qry->exec();
        $kewords = array();
        while ($result = $qry->next()) {
            $keywords[] = mb_strtolower($result["category_name"]);
        }

        $this->keywords = implode(", ", $keywords);

        $this->addOGTag("title", "%title%");
        $this->addOGTag("description", "%meta_description%");
        $this->addOGTag("url", fullURL($this->getPageURL()));
        $this->addOGTag("site_name", SITE_TITLE);
        $this->addOGTag("type", "website");
        //<meta name="twitter:card" content="summary_large_image" />
        //meta name="twitter:description" content="&nbsp; Продукти от категория &#8222;Джинси&#8220; Нови продукти Категории продукти Последно от блога [carousel_slide id=&#8217;1344&#8242;]" />
        //<meta name="twitter:title" content="Начало - ВИКИ МАШИНИ" />
    }

    protected function headStart()
    {
        parent::headStart();

        $cfg = new ConfigBean();
        $cfg->setSection("store_config");
        $phone = $cfg->get("phone", "");


        $org_data = array(
            "@context"=>"http://schema.org",
            "@type"=>"Organization",
            "name"=> SITE_TITLE,
            "url"=> SITE_URL,
            "logo"=> SITE_URL."/images/logo_header.svg",
            "contactPoint"=> array(
                "@type"=>"ContactPoint",
                "telephone"=>$phone,
                "contactType"=>"sales",
                "areaServed"=>substr(DEFAULT_LANGUAGE_ISO3, 0,2),
                "availableLanguage"=>DEFAULT_LANGUAGE
            )
        );

        $this->renderLDJSON($org_data);

    }

    public function renderLDJSON(array $data)
    {
        echo "<script type='application/ld+json'>";
        echo json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        echo "</script>";
    }

    public function getMenuBar()
    {
        return $this->menu_bar;
    }

    public function startRender()
    {
        parent::startRender();

        echo "\n<!-- startRender StorePage-->\n";

        $this->selectActiveMenu();

        echo "<div class='section header'>";
            echo "<div class='full'>";

            echo "<div class='space left'></div>";

            echo "<div class='content'>";

            $logo_href=LOCAL."/home.php";
            echo "<a class='logo' href='{$logo_href}' title='logo'></a>";

            $cfg = new ConfigBean();
            $cfg->setSection("store_config");

            echo "<div class='marquee'>";

            echo "<marquee>".$cfg->get("marquee_text")."</marquee>";
            echo "</div>";

            echo "</div>";//content

            echo "<div class='space right'></div>";

            echo "</div>"; //full
        echo "</div>"; //section header

        echo "<div class='section menu'>";
            echo "<div class='full'>";
            echo "<div class='space left'></div>";
            echo "<div class='content' >";

                echo "<div class='menuwrap'>";

                    $this->menu_bar->render();

                    echo "<div class='group'>";
                        echo "<div class='search_pane'>";
                        $this->keyword_search->render();
                        echo "<div class='clear'></div>";
                        echo "</div>";

                        echo "<div class='customer_pane'>";


                            $icon_contents = "<span class='icon'></span>";
                            $button_account = new Action();
                            $button_account->getURLBuilder()->buildFrom(LOCAL . "/account/login.php");
                            $button_account->setAttribute("title", tr("Account"));
                            $button_account->setClassName("button account");
                            $button_account->setContents($icon_contents);
                            if ($this->context) {
                                $button_account->getURLBuilder()->buildFrom(LOCAL . "/account/");
                                $button_account->addClassName("logged");
                            }
                            $button_account->render();


                            $button_cart = new Action();
                            $button_cart->getURLBuilder()->buildFrom(LOCAL . "/checkout/cart.php");
                            $button_cart->setAttribute("title", tr("Cart"));
                            $button_cart->addClassName("button cart");

                            $button_contents = $icon_contents;
                            $cart_items = Cart::Instance()->itemsCount();
                            if ($cart_items>0) {
                                $button_cart->setAttribute("item_count", $cart_items);
                                $button_contents.= "<span class='items_dot'>$cart_items</span>";
                            }
                            $button_cart->setContents($button_contents);
                            $button_cart->render();

                        echo "</div>"; //customer_pane
                    echo "</div>";//group

                echo "</div>";//menuwrap

            echo "</div>"; //content
            echo "<div class='space right'></div>";
            echo "</div>";//full
        echo "</div>";//section menu

        echo "<div class='section main'>";
            echo "<div class='full page'>";
            echo "<div class='space left'></div>";
            echo "<div class='content'>";


    }

    protected function selectActiveMenu()
    {

        $main_menu = $this->menu_bar->getMainMenu();
        $main_menu->selectActive(array(MainMenu::MATCH_FULL,MainMenu::MATCH_PARTIAL));

    }

    protected function constructTitle()
    {
        if (strlen($this->getTitle()) > 0) return;

        $main_menu = $this->menu_bar->getMainMenu();

        $this->setTitle(constructSiteTitle($main_menu->getSelectedPath()));
    }

    public function finishRender()
    {

        echo "</div>"; //page_content
        echo "<div class='space right'></div>";
        echo "</div>"; //full page
        echo "</div>";//section main

        echo "<div class='section footer'>";
            echo "<div class='full'>";
            echo "<div class='space left'></div>";
                echo "<div class='content'>";
    //

        $cfg = new ConfigBean();
        $cfg->setSection("store_config");
        $facebook_href = $cfg->get("facebook_url", "/");
        $instagram_href = $cfg->get("instagram_url", "/");
        $youtube_href = $cfg->get("youtube_url", "/");
        $phone = $cfg->get("phone", "");

                echo "<div class='social'>";
                    echo "<a class='slot facebook' title='facebook' href='{$facebook_href}'></a>";
                    echo "<a class='slot instagram' title='instagram' href='{$instagram_href}'></a>";
                    echo "<a class='slot youtube' title='youtube' href='{$youtube_href}'></a>";
                    echo "<a class='slot contacts' title='contacts' href='".LOCAL."/contacts.php'></a>";
                    echo "<a class='slot terms' title='terms' href='".LOCAL."/terms_usage.php"."'></a>";
                    echo "<a class='slot phone' title='phone' href='tel:$phone'></a>";
                echo "</div>";

                echo "</div>";//content
            echo "<div class='space right'></div>";
            echo "</div>";//full
        echo "</div>"; //section footer

        echo "\n";
        echo "\n<!-- finishRender StorePage-->\n";

        $this->constructTitle();
?>
        <script type="text/javascript">

            //to check when element get's position sticky
            var observer = new IntersectionObserver(function(entries) {
                // no intersection
                if (entries[0].intersectionRatio === 0)
                    document.querySelector(".section.menu").classList.add("sticky");
                // fully intersects
                else if (entries[0].intersectionRatio === 1)
                    document.querySelector(".section.menu").classList.remove("sticky");
            }, {
                threshold: [0, 1]
            });


            observer.observe(document.querySelector(".section.header"));
        </script>
<?php
        parent::finishRender();

    }

}

?>
