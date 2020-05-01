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


$page->sections->startIterator("WHERE 1 ORDER BY position ASC");


$sel = new ProductsSQL();
$sel->order_by = " pi.order_counter DESC, pi.view_counter DESC ";
$sel->group_by = " pi.prodID, pi.color ";
$sel->limit = "4";


$db = DBDriver::Get();
$section_row = array();
while ($page->sections->fetchNext($section_row)) {
    $section = $section_row["section_title"];
    $secID = $section_row["secID"];
    echo "<div class='section $section'>";

        
        echo "<a class='caption' href='products.php?section=$section'>$section</a>";
        
        
        $num = $section_banners->startIterator("WHERE secID='$secID' ORDER BY RAND() LIMIT 1", " sbID, caption, link, position ");
        $banner_row = array();
        if ($section_banners->fetchNext($banner_row)) {
            echo "<a class='banner' href='{$banner_row["link"]}'>";
            $img_href = StorageItem::Image($banner_row["sbID"], $section_banners);
            echo "<img width='100%' src='$img_href'>";
            echo "</a>";
        }
        
        
        echo "<div class='products'>";
            $sel->where = " p.section='$section' ";
            $sel->limit = "4";
//             echo $sel->getSQL();
            $res = $db->query($sel->getSQL());
            if (!$res) throw new Exception("Unable to query products from section '$section'. Error: ".$db->getError());
            
            while ($row = $db->fetch($res)) {
                $item->setItem($row);
                $item->render();
            }
            $db->free($res);
            
        echo "</div>";
        

    echo "</div>";
}

Session::Set("shopping.list", $page->getPageURL());

$page->finishRender();

?>
