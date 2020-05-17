<?php
include_once("session.php");
include_once("class/pages/StorePage.php");

$page = new StorePage();
$page->startRender();
$page->setTitle("Контакти");
echo "<div class='caption'>" . tr($page->getTitle()) . "</div>";
$page->finishRender();
?>
