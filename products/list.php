<?php
include_once("session.php");
include_once("class/pages/ProductListPage.php");

$page = new ProductListPage();

$page->processInput();

$page->startRender();

$page->renderContents();

$page->finishRender();
?>
