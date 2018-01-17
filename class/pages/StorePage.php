<?php
include_once("lib/pages/SimplePage.php");

include_once("lib/utils/MainMenu.php");
include_once("lib/components/MenuBarComponent.php");

include_once("lib/forms/InputForm.php");
include_once("lib/forms/renderers/FormRenderer.php");
include_once("lib/forms/processors/FormProcessor.php");
include_once("lib/input/InputFactory.php");

include_once("lib/beans/MenuItemsBean.php");
include_once("class/beans/SectionsBean.php");

class StorePage extends SimplePage
{

    protected $menu_bar = NULL;

    public $sections = NULL;

    protected $section = "";
    
    public function __construct()
    {

	parent::__construct();

	$menu = new MainMenu();
	
	$menu->setMenuBeanClass("MenuItemsBean", "");
        $menu->constructMenuItems(0, NULL, "menuID", "menu_title");

	$this->menu_bar = new MenuBarComponent($menu);

	$this->menu_bar->setName("StorePage");

        $this->sections = new SectionsBean();
        
// 	$this->menu_bar->getItemRenderer()->disableSubmenuRenderer();
	
        $this->addMeta("viewport", "width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0");
        
        
        if (isset($_GET["section"])) {
            $section = DBDriver::get()->escapeString($_GET["section"]);
            $num = $this->sections->startIterator("WHERE section_title = '$section' LIMIT 1");
            if ($num < 1) {
                $this->section = "";
            }
            else {
                $this->section = $section;
            }
        }
    }

    public function getMenuBar()
    {
        return $this->menu_bar;
    }
    protected function dumpCSS()
    {
	parent::dumpCSS();

	echo "<link rel='stylesheet' href='".SITE_ROOT."css/store.css' type='text/css'>";
	echo "<link rel='stylesheet' href='//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css'>";

	echo "\n";

    }
    protected function dumpJS()
    {
	parent::dumpJS();
	
	echo "<script src='//code.jquery.com/ui/1.11.4/jquery-ui.js'></script>";
	
	echo "\n";
    }

    protected function dumpMetaTags()
    {
            parent::dumpMetaTags();

    }
    
    public function getSection()
    {
        return $this->section;
    }
    
    public function setSection($section)
    {
        $this->section = $section;
    }

    public function beginPage()
    {
	parent::beginPage();

	echo "\n<!--beginPage StorePage-->\n";

	
	$this->selectActiveMenu();
	
	//$this->constructTitle();

	echo "<div align=center>";

            $this->menu_bar->render();
            
            echo "<div class='main_content'>"; //inner contents
          
    }

    protected function selectActiveMenu()
    {
        $main_menu = $this->menu_bar->getMainMenu();
   
        $main_menu->selectActiveMenus(MainMenu::FIND_INDEX_LOOSE);

    }
    protected function constructTitle()
    {
        if (strlen($this->getPreferredTitle())>0) return;
        
        $main_menu = $this->menu_bar->getMainMenu();
        
        $this->setPreferredTitle(constructSiteTitle($main_menu->getSelectedPath()));
    }
    public function finishPage()
    {

	  
            echo "</div>"; //main_content
	echo "</div>"; //align=center


	echo "\n";
	echo "\n<!--finishPage StorePage-->\n";

	$this->constructTitle();
	
	parent::finishPage();


    }

}

?>
