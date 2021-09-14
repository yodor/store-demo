<?php
include_once("session.php");
include_once("class/pages/StorePage.php");
include_once("class/beans/SectionBannersBean.php");
include_once("beans/DynamicPagesBean.php");

$page = new StorePage();


$page->startRender();

$banners = new SectionBannersBean();

$dyn_pages = new DynamicPagesBean();
$qry_pages = $dyn_pages->query("content", "item_title");
$qry_pages->select->where()->add("item_title", "'За нас'", " LIKE ");
$num_pages = $qry_pages->exec();


echo "<div class='banners'>";


    $qry1 = $banners->query("sbID", "caption", "link", "position");
    $qry1->select->order_by = " RAND() ";

    $num = $qry1->exec();
    if ($num) {

        echo "<div class='banner' >";

        while ($banner = $qry1->next()) {

            $img_href = StorageItem::Image($banner["sbID"], $banners);

            echo "<img src='{$img_href}'>";

        }

        echo "</div>";
    }


echo "</div>";



    echo "<div class='wrap'>";

    if ($num_pages>0) {
        $page_row = $qry_pages->next();
        echo stripAttributes($page_row["content"]);
    }

    echo "</div>";
?>
<script type="text/javascript">
    function fadeBanners()
    {

        let sections = $(".section .banner");

        for (var a=0;a<sections.size();a++) {
            let section = sections[a];

            $(section).children().first().appendTo($(section));
        }
        //let section = $(".section.Жени .banner");
        //

        slideBanners();

    }
    function slideBanners()
    {
        setTimeout(fadeBanners,3000);
    }
    onPageLoad(slideBanners);
</script>

<?php
$page->finishRender();
?>
