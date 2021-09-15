<?php
include_once("session.php");
include_once("store/pages/ProductListPage.php");

$page = new ProductListPage();
$page->setSellableProducts(new SellableProducts());

$page->initialize();

$page->processInput();

$page->startRender();

$page->renderContents();

$page->finishRender();
?>
