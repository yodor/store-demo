<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

$page = new AdminPage();

$menu=array(
    new MenuItem("Потвърдени", "confirmed.php", "class:orders icon_confirmed"),
    new MenuItem("Изпратени", "sent.php", "class:orders icon_confirmed"),
    new MenuItem("Завършени", "completed.php", "class:orders icon_completed"),
    new MenuItem("Отказани", "canceled.php", "class:orders icon_completed"),
    new MenuItem("Всички", "all.php", "class:orders icon_all"),

  
);


$page->checkAccess(ROLE_CONTENT_MENU);


$page->beginPage($menu);

echo tr("Управление на поръчки");

$page->finishPage();
?>
