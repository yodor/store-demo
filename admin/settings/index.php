<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

$page = new AdminPage("Settings");

$menu = array(

    new MenuItem("Administrators", "admins/list.php", "irc-operator.png"),
    new MenuItem("Languages", "languages/list.php", "applications-education-language.png"),
    new MenuItem("SEO", "seo.php", "applications-education-language.png"),

);

$page->setPageMenu($menu);

$page->startRender();

$page->finishRender();

?>
