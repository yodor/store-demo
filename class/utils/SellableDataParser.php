<?php
include_once("class/utils/PriceInfo.php");
include_once("class/utils/SellableItem.php");
include_once("class/utils/SellableDataParser.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductPhotosBean.php");

class SellableDataParser
{
    protected $product_photos = null;
    protected $product_color_photos = null;

    public function __construct()
    {

        $this->product_color_photos = new ProductColorPhotosBean();
        $this->product_photos = new ProductPhotosBean();

    }

    /**
     * Populate sellable item properties using data from db result record
     * @param SellableItem $item
     * @param array $result
     * @throws Exception
     */
    public function parse(SellableItem $item, array &$result)
    {
        //main photo
        if (is_null($item->getMainPhoto())) {

            $photos_query = $this->product_photos->queryField("prodID", $item->getProductID(), 0, "ppID");
            $photos_query->select->order_by = "position ASC";
            $photos_query->select->limit = " 1 ";
            $num = $photos_query->exec();
            if ($photo_row = $photos_query->next()) {

                $main_photo = new StorageItem($photo_row["ppID"], "ProductPhotosBean");
                $item->setMainPhoto($main_photo);
                // $this->gallery_href = STORAGE_LOCAL . "?cmd=image&height=648&width=648";

                // $this->big_href = STORAGE_LOCAL . "?cmd=image&class=ProductPhotosBean&id=$this->photoID&height=648&width=648";
                //$big_href = STORAGE_LOCAL . "?cmd=image";

            }
        }

        $piID = $result["piID"];
        if ($item->getActiveInventoryID()<1) $item->setInventoryID($piID);

        $item->setData($piID, $result);


        if (isset($result["product_name"])) {
            $item->setTitle($result["product_name"]);
        }
        if (isset($result["product_description"])) {
            $item->setCaption($result["product_description"]);
        }
        if (isset($result["long_description"])) {
            $item->setDescription($result["long_description"]);
        }
        if (isset($result["keywords"])) {
            $item->setKeywords($result["keywords"]);
        }

        //
        $attr_list = explode("|", $result["inventory_attributes"]);
        $attr_all = array();
        if (count($attr_list)>0) {
            foreach ($attr_list as $idx => $pair) {
                $name_value = explode(":", $pair);
                if(count($name_value)>1) {
                    //var_dump($name_value);
                    $attr_all[] = array("name" => $name_value[0], "value" => $name_value[1]);
                }
            }
        }
        $item->setAttributes($piID, $attr_all);

        $pclrID = (int)$result["pclrID"];
        $piID = (int)$result["piID"];

        $size_value = "".$result["size_value"];

        var_dump($result);

        $priceInfo = new PriceInfo((float)$result["sell_price"], (float)$result["old_price"], (int)$result["stock_amount"]);

        $item->setSizeValue($piID, $size_value);

        $item->setPriceInfo($piID, $priceInfo);

        $item->setColorID($piID, $pclrID);


        if (isset($result["color"])) {
            $item->setColorName($pclrID, $result["color"]);
        }

        if (isset($result["color_code"])) {
            $item->setColorCode($pclrID, $result["color_code"]);
        }

        if (!$item->haveGalleryItems($pclrID)) {

            //use product photos instead of product color photos
            $use_photos = FALSE;

            if ($pclrID > 0) {
                $qry = $this->product_color_photos->query("pclrpID");
                $qry->select->where()->add("pclrID", $pclrID);
                $qry->select->order_by = " position ASC ";
                $num = $qry->exec();
                if ($num < 1) $use_photos = TRUE;
                while ($result = $qry->next()) {
                    $sitem = new StorageItem($result["pclrpID"], get_class($this->product_color_photos));
                    $item->addGalleryItem($pclrID, $sitem);
                }

            }
            if ($use_photos || $pclrID < 1) {
                //attach default photo as single color gallery
                $qry = $this->product_photos->query("ppID");
                $qry->select->where()->add("prodID", $item->getProductID());
                $qry->select->order_by = " position ASC ";
                $num = $qry->exec();

                while ($result = $qry->next()) {
                    $sitem = new StorageItem($result["ppID"], get_class($this->product_photos));
                    $item->addGalleryItem($pclrID, $sitem);
                }
            }
        }

        //use the color chip from product color scheme
        if (isset($result["have_chip"]) && $result["have_chip"] > 0) {
            $sitem = new StorageItem($pclrID,  "ProductColorsBean", "color_photo");
            $item->setColorChip($pclrID, $sitem);
        }
        else {
            //no chip assigned - use first image from the gallery if there is at least one coloring scheme setup
            $gallery_items = $item->galleryItems($pclrID);
            if (isset($gallery_items[0])) {
                $item->setColorChip($pclrID, $gallery_items[0]);
            }
            else {
                //use the color code as color button
                $item->setColorChip($pclrID, null);
            }
        }
    }
}
?>