<?php
include_once("session.php");
include_once("store/pages/StorePage.php");

$page = new StorePage();

$page->startRender();
$page->finishRender();

?>
