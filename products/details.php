<?php
include_once("session.php");
include_once("class/pages/ProductDetailsPage.php");
include_once("store/components/renderers/items/ProductDetailsItem.php");

$page = new ProductDetailsPage();

$sellable = $page->getSellable();

$cmp = new ProductDetailsItem($sellable);
$cmp->setURL(fullURL($page->getPageURL()));
$cmp->setCategories($page->getCategoryPath());

$page->startRender();

$page->renderCategoryPath();

$cmp->render();


echo "<div class='product_group same_category '>";
$page->renderSameCategoryProducts();
echo "</div>";

//echo "<div class='product_group most_ordered'>";
//$page->renderMostOrderedProducts();
//echo "</div>";

echo "<div class='product_group most_ordered'>";
$page->renderOtherProducts();
echo "</div>";


$page->finishRender();
?>