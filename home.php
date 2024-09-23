<?php
include_once("session.php");
include_once("class/pages/StorePage.php");
include_once("store/beans/ProductCategoriesBean.php");
include_once("store/beans/ProductCategoryPhotosBean.php");

include_once("store/beans/SectionsBean.php");
include_once("store/beans/SectionBannersBean.php");

include_once("store/components/ProductsTape.php");
include_once("store/beans/SellableProducts.php");

$page = new StorePage();
$page->setTitle("Начало");

$page->head()->addCSS(STORE_LOCAL."/css/ProductListItem.css");


$banners = new SectionBannersBean();

$tape = new ProductsTape();

$item = new ProductListItem();

$secBean = new SectionsBean();

$qry = $secBean->query("secID", "section_title");
$qry->select->order_by = " position ASC ";
$qry->select->where()->add("home_visible", 1);
$qry->exec();
$sections = array();

while ($result = $qry->nextResult()) {
    $sections[] = $result->toArray();
}

$sellables = new SellableProducts();
$sellables->select()->order_by = " sell_price ASC ";
$sellables->select()->group_by = " prodID ";
$sellables->select()->limit = " 4 ";

$qry->exec();

$page->startRender();

//TODO list only sections with products
foreach ($sections as $idx=>$section) {

    $sectionName = $section["section_title"];
    $secID = $section["secID"];

//    if (strcmp($sectionName, "Галерия")==0) {
        $sellables->select()->order_by = " RAND() ";
//    }
//    else {
//        $sellables->select()->order_by = " sell_price ASC ";
//    }

    $sellables->select()->where()->clear();
    $sellables->select()->where()->add("section", "'$sectionName'");

    $query = $sellables->queryFull();
    $numItems = $query->exec();

    //section with no products in it
    if ($numItems < 1) continue;

    echo "<div class='section $sectionName'>";

    $secion_url = LOCAL . "/products/list.php?section=$sectionName";
    echo "<h2 class='Caption'><a href='$secion_url' title='$sectionName'>$sectionName</h2>";

    $qry1 = $banners->queryField("secID", $secID, 0, "sbID", "caption", "link", "position");
    $qry1->select->order_by = " position ASC ";

    $num = $qry1->exec();
    if ($num) {

        echo "<a class='banner' href='$secion_url' title='$sectionName'>";

        while ($banner = $qry1->next()) {

            $img_href = StorageItem::Image($banner["sbID"], $banners, 1200, -1);

            echo "<img src='{$img_href}' alt='' loading='lazy'>";

        }

        echo "</a>";
    }


    echo "<div class='products'>";

    $tape->setIterator($query);
    $tape->render();
    //
    //    while ($row = $query->next()) {
    //        $item->setData($row);
    //        $item->render();
    //    }

    echo "</div>";

    echo "</div>";
}

Session::Set("shopping.list", URL::Current()->toString());
?>
<script type="text/javascript">
    function fadeBanners()
    {

        let sections = $(".section .banner");

        for (var a=0;a<sections.length;a++) {
            let section = sections[a];

            $(section).children().first().appendTo($(section));
        }
        setTimeout(fadeBanners,3000);

    }

    onPageLoad(fadeBanners);
</script>

<?php
$page->finishRender();
?>
