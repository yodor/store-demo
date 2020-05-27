<?php
include_once("session.php");
include_once("class/pages/ProductDetailsPage.php");

$page = new ProductDetailsPage();

$prodID = -1;
if (isset($_GET["prodID"])) {
    $prodID = (int)$_GET["prodID"];
}
$piID = -1;
if (isset($_GET["piID"])) {
    $piID = (int)$_GET["piID"];
}

$sellable = array();

$landing_sellable = array();

$db = DBConnections::Get();
$res = NULL;
try {

    $relation = $page->derived;

    $relation->where()->set("pi.prodID", $prodID);
    $relation->order_by = " pi.size_value ASC ";
    $relation->group_by = " pi.piID ";

    //    echo $relation->getSQL();

    $res = $db->query($relation->getSQL());
    if (!$res) throw new Exception("Product Not Found: " . $db->getError());

    $found_piID = FALSE;
    while ($item = $db->fetch($res)) {
        $sellable[] = $item;
        //
        if ((int)$piID == (int)$item["piID"]) {
            $found_piID = TRUE;
            $landing_sellable = $item;
        }

    }
    $db->free($res);

    if (count($sellable) < 1) {
        throw new Exception("Product Not Found.");
    }

    if (!$found_piID) {
        $piID = $sellable[0]["piID"];
        $landing_sellable = $sellable[0];
    }

}
catch (Exception $e) {

    Session::set("alert", "Този продукт е недостъпен. Грешка: " . $e->getMessage());
    header("Location: products.php");
    exit;
}

//update view counter
try {
    $db->transaction();
    $res = $db->query("UPDATE product_inventory pi SET pi.view_counter=pi.view_counter+1 WHERE pi.prodID='$prodID' AND pi.pclrID='{$landing_sellable["pclrID"]}'");
    if (!$res) throw new Exception($db->getError());
    $db->commit();
}
catch (Exception $e) {
    $db->rollback();
    debug("Unable to update inventory view counter: " . $db->getError());
}

//per pclrID items used for color button images
$color_chips = array();

//per pclrID color names
$color_names = array();

//per pclrID items
$galleries = array();

$prices = array();

//per inventory ID
$attributes = array();

$pos = 0;

//product_colors => product color scheme
//product_color_photos => product color scheme photos
foreach ($sellable as $pos => $row) {

    // 	echo $pos++;

    //NULL color links all sizes goes to key 0
    $pclrID = (int)$row["pclrID"];

    $attr_list = explode("|", $row["inventory_attributes"]);
    $attr_all = array();
    foreach ($attr_list as $idx => $pair) {
        $name_value = explode(":", $pair);
        $attr_all[] = array("name" => $name_value[0], "value" => $name_value[1]);
    }

    $attributes[$row["piID"]] = $attr_all;

    //colorID/size_value/piID
    $prices[$pclrID][$row["size_value"]][$row["piID"]] = array("sell_price"   => $row["sell_price"],
                                                               "stock_amount" => $row["stock_amount"]);

    $color_names[$pclrID] = $row["color"];

    $color_codes[$pclrID] = $row["color_code"];

    if (!isset($galleries[$pclrID])) {
        $galleries[$pclrID] = array();
        $use_photos = FALSE;

        if ($pclrID > 0) {
            $res = $db->query("SELECT pclrpID FROM product_color_photos WHERE pclrID=$pclrID ORDER BY position ASC");
            if (!$res) throw new Exception("Unable to query color gallery: " . $db->getError());
            if ($db->numRows($res) < 1) $use_photos = TRUE;
            while ($grow = $db->fetch($res)) {
                $item = array("id" => $grow["pclrpID"], "class" => "ProductColorPhotosBean");
                $galleries[$pclrID][] = $item;
            }
            $db->free($res);

        }
        if ($use_photos || $pclrID < 1) {
            //attach default photo as signle color gallery
            $res = $db->query("SELECT ppID FROM product_photos WHERE prodID=$prodID ORDER BY position ASC");
            if (!$res) throw new Exception("Unable to query product gallery: " . $db->getError());
            while ($grow = $db->fetch($res)) {
                $item = array("id" => $grow["ppID"], "class" => "ProductPhotosBean");
                $galleries[$pclrID][] = $item;
            }
            $db->free($res);
        }
    }

    //use the color chip from product color scheme
    if ((int)$row["have_chip"] > 0) {
        $item = array("id" => $pclrID, "class" => "ProductColorsBean&field=color_photo");
        $color_chips[$pclrID] = $item;
    }
    else {
        //no chip assigned - use first image from the gallery if there is atleast one coloring scheme setup
        if (isset($galleries[$pclrID][0])) {
            $color_chips[$pclrID] = $galleries[$pclrID][0];
        }
        else {
            //use the color code as color button
            $item = array("id" => $pclrID);
            $color_chips[$pclrID] = $item;
        }
    }

}

$sellable_variation = $sellable[0];
// echo $sel->getSQL();

$page->setSellableItem($sellable_variation);

// $page->selectMenuForSection();

$page->startRender();

// var_dump($attributes);
// print_r($galleries);

$page->renderCategoryPath();

echo "<div class='column details'>";

echo "<div class='images'>";

//main image
$gallery_href = STORAGE_LOCAL . "?cmd=image&width=500&height=500";
$big_href = STORAGE_LOCAL . "?cmd=image";

echo "<div class='image_big' source='$gallery_href' >";
echo "<a class='ImagePopup' data-href='' source='$big_href'><img src='$big_href'></a>";
echo "</div>";
//TODO:Images
//photo galleries per color
echo "<div class='image_gallery'>";
foreach ($galleries as $pclrID => $gallery) {
    echo "<div class='list' pclrID='$pclrID'>";
    foreach ($gallery as $key => $item) {
        $href_source = STORAGE_LOCAL . "?cmd=image&width=50&height=50";

        $href = $href_source . "&class=" . $item["class"] . "&id=" . $item["id"];

        echo "<div class='item' bean='{$item["class"]}' itemID='{$item["id"]}' source='$href_source' onClick='javascript:changeImage(this)' style='background-image:url($href)'>";
        // 			echo "<img src='$href' >";
        echo "</div>";

    }
    echo "</div>";//list
}
echo "</div>";//image_gallery

echo "</div>"; // images

echo "<div class='side_pane'>";

echo "<div class='product_name'>" . $sellable_variation["product_name"] . "</div>";

echo "<div class='sell_price'>";
echo "<span class='value' piID='$piID'>" . sprintf("%0.2f", $sellable_variation["sell_price"]) . "</span>";
echo "&nbsp;<span class='currency'>лв.</span>";
echo "</div>";

echo "<div class='stock_amount'>";
echo "<span>" . tr("Наличност") . "&nbsp;-&nbsp;</span>";
echo "<span class='value'></span>";
echo "<span> бр.</span>";
echo "</div>";

echo "<div class='group sizing_colors'>";

//hide color chooser for single color or color schemeless products
// 	if ($pclrID == 0 || count($color_names)==1) {
//             //no colors setup
//             $chooser_visibility = "style='display:none'";
// 	}

echo "<div class='item current_color'>";
echo "<label>" . tr("Цвят") . "</label>";
echo "<span class='value'></span>";
echo "</div>";

echo "<div class='item color_chooser'>";

echo "<span class='value'>";

foreach ($color_chips as $pclrID => $item) {
    $pclrID = (int)$pclrID;

    if (isset($item["class"])) {
        $href = StorageItem::Image($item["id"], $item["class"], 100, 100);
    }

    $chip_colorName = $color_names[$pclrID];

    $sizes = $prices[$pclrID];

    $pids = array();

    $sell_prices = array();
    $stock_amounts = array();

    foreach ($sizes as $size_value => $arr) {
        foreach ($arr as $cpiID => $sell_data) {
            $pids[] = $cpiID;
            $sell_prices[] = $sell_data["sell_price"];
            $stock_amounts[] = $sell_data["stock_amount"];
        }
    }

    $size_values = implode("|", array_keys($sizes));
    $cpiID = $pids[0];
    $pids = implode("|", $pids);
    $sell_prices = implode("|", $sell_prices);
    $stock_amounts = implode("|", $stock_amounts);

    //sizing pids = $pid_values
    echo "<span class='color_button' pclrID='$pclrID' piID='$cpiID' size_values='$size_values' sell_prices='$sell_prices' stock_amounts='$stock_amounts' pids='$pids' color_name='$chip_colorName' onClick='javascript:changeColor($pclrID)' title='$chip_colorName'>";

    if (isset($item["class"])) {
        echo "<img src='$href' >";
    }
    else {
        echo "<span class='simple_color' style='background-color:{$color_codes[$pclrID]};'></span>";
    }

    echo "</span>";
}

echo "</span>";//value
echo "</div>";//color_chooser

// 	echo "<div class='item size_chooser' model='select_box>";
// 	  echo "<label>".tr("Размер")."</label>";
// 	  echo "<span class='value'>";
//             echo "<select class='product_size' onChange='javascript:updatePrice()'>";
//             echo "</select>";
// 	  echo "</span>";
// 	echo "</div>";
echo "<div class='item size_chooser' model='size_button'>";
echo "<label>" . tr("Размер") . "</label>";
echo "<span class='value'>";
//listed from javascript
echo "</span>";
echo "</div>";

echo "</div>"; //sizing_colors

echo "<div class='group attributes'>";
echo "</div>";

echo "<div class='cart_link'>";
echo "<a class='cart_add' href='javascript:addToCart()'>" . tr("Добави в кошница") . "</a>";
echo "</div>";

echo "<div class='summary'>";
echo "<label>" . tr("Описание") . "</label>";
echo "<span class='value'>" . $sellable_variation["product_summary"] . "</span>";
echo "</div>";

echo "</div>"; //side_pane

echo "<div class='clear'></div>";

echo "<div class='category_products product_group'>";
$page->renderSameCategoryProducts();
echo "</div>";

echo "<div class='ordered_products product_group'>";
$page->renderMostOrderedProducts();
echo "</div>";

echo "</div>"; //column details

?>

<script type='text/javascript'>
    var piID = <?php echo $piID;?>;

    var attributes = <?php echo json_encode($attributes);?>

        onPageLoad(function () {

//   var first_color = $(".color_chooser .color_button").first();
//   changeColor(first_color.attr("pclrID"));

            //find the color button of this piID by looking the pids attribute of .color_button
            var selected_pclrID = -1;

            $(".color_chooser .color_button").each(function (index) {
                var pids = $(this).attr("pids").split("|");
                var pclrID = $(this).attr("pclrID");
                var have_pid = pids.indexOf("" + piID);
                console.log("pclrID=" + pclrID + " PIDS: " + pids + " | IndexOf(" + piID + ") = " + have_pid);
                if (have_pid >= 0) {
                    selected_pclrID = pclrID;
                    return;
                }
            });
            changeColor(selected_pclrID);

//     var firstColorButton = $(".color_chooser .color_button[piID='"+piID+"']");
//     if (firstColorButton) {
//         var pclrID = firstColorButton.attr("pclrID");
//         changeColor(pclrID);
//         //   console.log(piID+"=>"+pclrID);
//     }


        });

</script>

<?php
$page->finishRender();
?>
