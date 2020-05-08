<?php
include_once("session.php");
include_once("class/pages/StorePage.php");
include_once("class/beans/SectionsBean.php");
include_once("class/beans/SectionBannersBean.php");
include_once("class/utils/ProductsSQL.php");
include_once("class/components/renderers/items/ProductListItem.php");

$page = new StorePage();

$item = new ProductListItem();

$page->startRender();

$section_banners = new SectionBannersBean();

$qry = $page->sections->query();
$qry->select->order_by = " position ASC ";

$sel = new ProductsSQL();
$sel->order_by = " pi.order_counter DESC, pi.view_counter DESC ";
$sel->group_by = " pi.prodID, pi.color ";
$sel->limit = "4";

$prodQry = new SQLQuery($sel, "p.prodID");

$qry->exec();

//TODO list only sections with products
while ($section_row = $qry->next()) {

    $section = $section_row["section_title"];
    $secID = $section_row["secID"];
    echo "<div class='section $section'>";

    echo "<a class='caption' href='" . LOCAL . "products.php?section=$section'>$section</a>";

    $qry1 = $section_banners->queryField("secID", $secID, 1);
    $qry1->select->order_by = " RAND() ";
    $qry1->select->fields = " sbID, caption, link, position ";
    $num = $qry1->exec();

    if ($banner_row = $qry1->next()) {
        echo "<a class='banner' href='" . LOCAL . "{$banner_row["link"]}'>";
        $img_href = StorageItem::Image($banner_row["sbID"], $section_banners);
        echo "<img width='100%' src='$img_href'>";
        echo "</a>";
    }

    echo "<div class='products'>";

    $prodQry->select->where = " p.section='$section' ";
    $prodQry->select->limit = "4";
    $prodQry->exec();

    while ($row = $prodQry->next()) {
        $item->setItem($row);
        $item->render();
    }

    echo "</div>";

    echo "</div>";
}

Session::Set("shopping.list", $page->getPageURL());

$page->finishRender();

?>
