<?php
include_once("session.php");
include_once("class/beans/SellableProducts.php");
include_once("class/components/renderers/items/ProductListItem.php");

header( "Content-Type: text/csv" );
header( "Content-Disposition: attachment;filename=catalog.csv");
$fp = fopen("php://output", "w");

$keys = array("id", "content_id", "title", "description", "availability", "condition", "link", "image_link", "brand", "product_type", "price");

fputcsv($fp, $keys);

$bean = new SellableProducts();

$query = $bean->queryFull();
$query->select->group_by = " prodID, color ";
$query->select->order_by = " update_date DESC ";

$query->exec();

$item = new ProductListItem();

while ($result = $query->nextResult()) {
    $prodID = $result->get("prodID");
    $piID = $result->get("piID");

    $data = $result->getAll();
    $item->setData($data);

    $export_row = array();
    $export_row["id"] = $prodID.".".$piID;
    $export_row["content_id"] = $prodID.".".$piID;
    $export_row["title"] = $result->get("product_name");
    $export_row["description"] = $result->get("product_name");
    $export_row["availability"] = "in stock";
    $export_row["condition"] = "new";

    $link = $item->getDetailsURL()->toString();
    $export_row["link"] = fullURL($link);

    $image_link = $item->getPhoto()->hrefImage(640,-1);
    $export_row["image_link"] = fullURL($image_link);
    $export_row["brand"] = $result->get("brand_name");
    $export_row["product_type"] = $result->get("category_name");


    $export_row["price"] = $result->get("sell_price");

    fputcsv($fp, $export_row);

}



?>
