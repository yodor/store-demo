<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

$page = new AdminPage();

$menu=array(
    new MenuItem("Confirmed", "confirmed.php", "class:orders icon_confirmed"),
    new MenuItem("Completed", "completed.php", "class:orders icon_completed"),
    new MenuItem("All", "all.php", "class:orders icon_all"),

  
);


$page->checkAccess(ROLE_CONTENT_MENU);


$page->beginPage($menu);

echo "Orders Management";

$page->finishPage();
?>
