<?php
include_once("session.php");
include_once("class/pages/DemoPage.php");
include_once("beans/DynamicPagePhotosBean.php");
// include_once("beans/DynamicPagesBean.php");
include_once("beans/MenuItemsBean.php");

$page = new DemoPage();

if (!isset($_GET["page_id"]) || !isset($_GET["page_class"])) {

    exit;
}

$page_class = DBConnections::Get()->escape($_GET["page_class"]);
$page_id = (int)$_GET["page_id"];

try {
    @include_once("class/beans/$page_class.php");
    @include_once("beans/$page_class.php");
    $b = new $page_class;

    $prkey = $b->key();
    $rrow = array();
    $qry = $b->queryField($prkey, $page_id, 1);
    $qry->select->fields()->set(" item_title, content, visible ");
    $num = $qry->exec();
    if ($num < 1) throw new Exception("This page is not available.");
    $rrow = $qry->next();
    if (!$rrow["visible"]) throw new Exception("This page is currently unavailable.");

}
catch (Exception $e) {
    Session::SetAlert($e->getMessage());
    header("Location: " . LOCAL . "/home.php");
    exit;
}

$menu1 = new MainMenu();
$menu1->setMenuBeanClass("MenuItemsBean");
// $parentID=0, MenuItem $parent_item = NULL, $key="menuID", $title="menu_title"
$menu1->constructMenuItems(0, NULL, "menuID", "menu_title");

$menu_bar1 = new MenuBarComponent($menu1);
$menu_bar1->setName("MenuItemsBean");

$page->startRender();

echo "<div class='$page_class'>";

echo "<div class='MenuBarWrapper'>";
$menu_bar1->render();
echo "</div>";

echo "<div class='photo'>";
$photo_href = StorageItem::Image($page_id, $page_class);

echo "<img src='$photo_href'>";

echo "</div>";

echo "<div class='item_title'>" . $rrow["item_title"] . "</div>";

if (isset($rrow["subtitle"])) {
    echo "<div class='subtitle'>" . $rrow["subtitle"] . "</div>";
}

echo "<div class='content'>" . $rrow["content"] . "</div>";

echo "<div class='PagePhotos'>";

$dpp = new DynamicPagePhotosBean();
$qry = $dpp->queryField("dpID", $page_id, 1);
$qry->select->fields()->set("ppID", "caption");
$num_photos = $qry->exec();

$image_popup = new ImagePopup();
$image_popup->setBean($bean);
$image_popup->setAttribute("rel", "DynamicPagePhotosBean");

if ($num_photos && $dpprow = $qry->next()) {
    $photo_id = $dpprow["ppID"];

    echo "<div class='photo_item' id='$photo_id' class='DynamicPagePhotosBean'>";

    $image_popup->setID($photo_id);
    $image_popup->render();

    echo "<div class='caption'>";
    echo $dpprow["caption"];
    echo "</div>";

    echo "</div>";

}

echo "</div>";

echo "</div>"; //$page_class

$page->finishRender();
?>