<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");







$page = new AdminPage();

$menu=array(
    new MenuItem("Секции", "sections/list.php", "class:store sections"),
    
    new MenuItem("Марки", "brands/list.php", "class:store brands"),
    new MenuItem("Категории", "categories/list.php", "class:store categories"),
    
    new MenuItem("Класове", "classes/list.php", "class:store classes"),
    new MenuItem("Атрибути", "attributes/list.php", "class:store attributes"),
    new MenuItem("Цветови кодове", "colors/list.php", "class:store colors"),
    new MenuItem("Оразмеряващи кодове", "sizes/list.php?prodID", "class:store sizes"),
    new MenuItem("Продукти", "products/list.php", "class:store products"),
  
);


$page->checkAccess(ROLE_CONTENT_MENU);


$page->beginPage($menu);

echo "Управление на магазина";

$page->finishPage();
?>
