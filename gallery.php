<?php
include_once("session.php");
include_once("class/pages/StorePage.php");

$page = new StorePage();

$page->startRender();
$page->finishRender();

?>
