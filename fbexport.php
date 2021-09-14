<?php
include_once("session.php");
include_once("class/beans/GalleryPhotosBean.php");
include_once("sql/SQLSelect.php");
include_once("iterators/SQLQuery.php");
include_once("class/utils/ProductsSQL.php");

header( "Content-Type: text/csv" );
header( "Content-Disposition: attachment;filename=catalog.csv");
$fp = fopen("php://output", "w");

$keys = array("id", "content_id", "title", "description", "availability", "condition", "price", "link", "image_link", "brand", "product_type");

fputcsv($fp, $keys);

$sel = new ProductsSQL();
$sel->group_by = " pi.prodID ";
$sel->order_by = " p.insert_date DESC ";


$qry = new SQLQuery($sel, "prodID");
$qry->exec();

while ($row = $qry->next()) {
    $id = $row["prodID"];
    $photoID = $row["ppID"];

    $export_row = array();
    $export_row["id"] = $row["prodID"];
    $export_row["content_id"] = $row["prodID"];
    $export_row["title"] = $row["product_name"];
    $export_row["description"] = $row["product_name"];
    $export_row["availability"] = "in stock";
    $export_row["condition"] = "new";
    $export_row["price"] = $row["sell_price"];
    $export_row["link"] = "https://viki-max.com/products/details.php?prodID=$id";
    $export_row["image_link"] = "https://viki-max.com/storage.php?cmd=image&id=$photoID&class=ProductPhotosBean";
    $export_row["brand"] = "viki-max";
    $export_row["product_type"] = $row["category_name"];
    fputcsv($fp, $export_row);
}



?>

