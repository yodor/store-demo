<?php
include_once("session.php");
include_once("class/pages/StorePage.php");
include_once("class/beans/ProductCategoriesBean.php");
include_once("class/beans/ProductCategoryPhotosBean.php");

include_once("class/beans/SectionsBean.php");
include_once("class/beans/SectionBannersBean.php");
include_once("class/utils/ProductsSQL.php");
include_once("class/components/ProductsTape.php");
include_once("class/beans/SellableProducts.php");

$page = new StorePage();
$page->setTitle("Начало");

$page->addCSS(LOCAL."/css/ProductListItem.css");

$page->startRender();


$sections = new SectionsBean();

$qry = $sections->query("secID", "section_title");
$qry->select->order_by = " position ASC ";

$qry->exec();

$sellable = new SellableProducts();
$query_tape = $sellable->query(...$sellable->columnNames());
$query_tape->select->order_by = " order_counter DESC, view_counter DESC ";
$query_tape->select->group_by = SellableProducts::DefaultGrouping();

$query_tape->select->limit = " 4 ";

$tape = new ProductsTape();

//TODO list only sections with products
while ($section = $qry->nextResult()) {

    $sectionName = $section->get("section_title");
    $secID = $section->get("secID");

    $query_tape->select->where()->clear();
    $query_tape->select->where()->add("section", "'{$sectionName}'");

    echo "<div class='section $sectionName'>";

        $secion_url = LOCAL . "/products/list.php?section=$sectionName";
        echo "<a href='$secion_url' title='$sectionName'><h1 class='Caption'>$sectionName</h1></a>";

        $tape->setIterator($query_tape);
        $tape->render();

    echo "</div>";
}

Session::Set("shopping.list", $page->getPageURL());

$page->finishRender();
?>
