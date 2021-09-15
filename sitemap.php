<?php
include_once("session.php");
include_once("store/beans/SellableProducts.php");

$bean = new SellableProducts();


echo "<?xml version='1.0' encoding='UTF-8'?>";
echo "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";
renderItem(fullURL(LOCAL."/home.php"));
renderItem(fullURL(LOCAL."/products/list.php"));
renderItem(fullURL(LOCAL."/products/promo.php"));
renderItem(fullURL(LOCAL."/contacts.php"));

$bean->select()->group_by = " prodID ";

$qry = $bean->query("prodID", "update_date");
$num = $qry->exec();
while ($result = $qry->nextResult()) {
    $prodID = $result->get("prodID");

    $update_date = new DateTime($result->get("update_date"));

    renderItem(fullURL(LOCAL."/products/details.php?prodID=$prodID"), $update_date->format('Y-m-d'));
}

echo "</urlset>";

function renderItem(string $loc, string $lastmod="")
{
    //2018-06-04

    echo "<url>";
    echo "<loc>$loc</loc>";
    if ($lastmod) {
        echo "<lastmod>$lastmod</lastmod>";
    }
    echo "</url>";

}
?>
