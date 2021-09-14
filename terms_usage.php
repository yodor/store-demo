<?php
include_once("session.php");
include_once("class/pages/StorePage.php");
include_once("beans/DynamicPagesBean.php");

$page = new StorePage();

$dpID = -1;
if (isset($_GET["dpID"])) {
    $dpID = (int)$_GET["dpID"];
}
$page->startRender();

echo "<div class='columns'>";

$dpages = new DynamicPagesBean();
$qry = $dpages->query("item_title", "keywords", "item_date",$dpages->key());
$qry->select->where()->add("keywords", "'%terms%'", " LIKE ", " OR ");
$qry->select->where()->add("keywords", "'terms%'", " LIKE ");

$qry->select->order_by = " position ASC ";

$num = $qry->exec();

$pageids = array();

echo "<div class='column menu'>";
echo "<div class='terms_menu'>";
while ($prow = $qry->next()) {
    echo "<a class='item' href='?dpID=".$prow[$dpages->key()]."'>";
    echo $prow["item_title"];
    echo "</a>";
    $pageids[] = $prow[$dpages->key()];
}
echo "</div>";
echo "</div>";

echo "<div class='column page_data'>";
if (!in_array($dpID, $pageids)) {
    foreach ($pageids as $key=>$pageid) {
        $dpID = $pageid;
        break;
    }
}

if ($dpID>0) {
    $content = $dpages->getValue($dpID, "content");
    $item_title = $dpages->getValue($dpID, "item_title");

    $page->setTitle($item_title);
    echo "<h1 class='Caption'>" . $item_title . "</h1>";

    echo $content;

    echo "</div>";
}
else {
    Session::SetAlert("Page not found");
}
echo "</div>";

$page->finishRender();
?>
