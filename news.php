<?php
include_once("session.php");

include_once("class/pages/StorePage.php");

include_once("store/beans/NewsItemsBean.php");
include_once("components/PublicationsComponent.php");

$page = new StorePage();

$page->head()->addCSS(LOCAL . "/css/news.css");

$nb = new NewsItemsBean();
$prkey = $nb->key();

$itemID = -1;

if (isset($_GET[$prkey])) {
    $itemID = (int)$_GET[$prkey];
}

$qry = $nb->query();
$qry->select->where()->add($prkey, $itemID);
$qry->select->limit = " 1 ";
$qry->select->order_by = " item_date DESC ";

$pac = new PublicationsComponent(new NewsItemsBean(), LOCAL . "/news.php");

$selected_year = $pac->getSelectedYear();
$selected_month = $pac->getSelectedMonth();

$num = -1;

if ($pac->isProcessed()) {

    $qry->where = " YEAR(item_date)='$selected_year' AND MONTHNAME(item_date)='$selected_month' ";
    $qry->limit = "";
    $num = $qry->exec();
}

if ($num < 1) {

    $qry->where = " YEAR(item_date)='$selected_year' AND MONTHNAME(item_date)='$selected_month' ";
    $num = $qry->exec();
}

$page->startRender();

echo "<div class='news_view'>";

echo "<div class='column main'>";
$item_row = array();
while ($nb->fetchNext($item_row)) {
    $itemID = $item_row[$nb->key()];
    trbean($itemID, "item_title", $item_row, $nb->getTableName());
    trbean($itemID, "content", $item_row, $nb->getTableName());

    echo "<div class='item_view' itemID='$itemID'>";
    echo "<div class='title'>";
    echo $item_row["item_title"];
    echo "</div>";

    echo "<div class='date'>";
    echo dateFormat($item_row["item_date"], FALSE);
    echo "</div>";

    echo "<div class='content'>";
    $item = new StorageItem();
    $item->id = $itemID;
    $item->className = "NewsItemsBean";
    $img_href = $item->hrefCrop(-1, 256);
    echo "<img src='$img_href'>";
    echo $item_row["content"];
    echo "</div>";

    echo "</div>";

    echo "<hr>";
}
echo "</div>"; //column_main

echo "<div class='column other'>";
echo "<div class='latest'>";
echo "<div class='Caption'>";
echo tr("Latest News");
echo "</div>";
drawLatestNews(3);

echo "</div>";

echo "<div class='archive'>";
echo "<div class='Caption'>";
echo tr("News Archive");
echo "</div>";
$pac->render();
echo "</div>";

echo "</div>"; //column_other

echo "</div>";//news_view

function drawLatestNews($num, $selected_year = FALSE, $selected_month = FALSE)
{

    $nb = new NewsItemsBean();
    $qry = $nb->query();
    $qry->select->order_by = " item_date DESC ";
    $qry->select->limit = "3";
    $qry->select->fields()->set( $nb->key(), "item_title", "item_date");
    $qry->exec();

    while ($item_row = $qry->next()) {
        $itemID = $item_row[$nb->key()];
        echo "<a class='item' newsID='$itemID' href='" . LOCAL . "/news.php?newsID=$itemID'>";

        echo "<div class='cell image'>";
        $img_href = StorageItem::Image($itemID, $nb, 48, 48);
        echo "<div class='panel'><img src='$img_href'></div>";
        echo "</div>";

        echo "<div class='cell details'>";
        echo "<span class='title'>" . $item_row["item_title"] . "</span>";
        echo "<span class='date'>" . dateFormat($item_row["item_date"], FALSE) . "</span>";
        echo "</div>";

        echo "</a>";
    }

}

$page->finishRender();
?>
