<?php
include_once("session.php");
include_once("class/pages/ProductListPage.php");


$bean = new SellableProducts();

$clause = new SQLClause();
$clause->setExpression("(discount_percent > 0 OR promo_price > 0)", "", "");
$bean->select()->where()->addClause($clause);

$page = new ProductListPage($bean);

$page->processInput();

$page->startRender();

$page->renderContents();

$page->finishRender();
?>
