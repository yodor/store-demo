<?php
include_once("session.php");
include_once("store/pages/ProductListPage.php");

$page = new ProductListPage();

$bean = new SellableProducts();

$clause = new SQLClause();
$clause->setExpression("(discount_percent > 0 OR promo_price > 0)", "", "");
$bean->select()->where()->addClause($clause);
$page->setSellableProducts($bean);

$page->initialize();

$page->processInput();

$page->startRender();

$page->renderContents();

$page->finishRender();
?>
