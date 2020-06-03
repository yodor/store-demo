<?php
include_once("components/renderers/items/DataIteratorItem.php");
include_once("storage/StorageItem.php");
include_once("class/beans/ProductColorPhotosBean.php");


class ProductListItem extends DataIteratorItem implements IHeadContents, IPhotoRenderer
{

    //same product all inventory color data
    protected $colorSeries = array();

    /**
     * To render the main inventory photo
     * @var StorageItem
     */
    protected $photo;

    /**
     * To render the color chip
     * @var StorageItem
     */
    protected $chip;

    /**
     * Details page of this inventory
     * @var URLBuilder
     */
    protected $detailsURL;

    protected $width = 275;
    protected $height = 275;

    protected $chipSize = 48;

    public function __construct()
    {
        parent::__construct();

        $this->photo = new StorageItem();

        $this->chip = new StorageItem();
        $this->chip->className = "ProductColorPhotosBean";

        $this->detailsURL = new URLBuilder();
        $this->detailsURL->setScriptName(LOCAL . "/details.php");
        $this->detailsURL->add(new DataParameter("prodID"));
        $this->detailsURL->add(new DataParameter("piID"));
    }

    public function getDetailsURL(): URLBuilder
    {
        return $this->detailsURL;
    }

    public function requiredStyle(): array
    {
        $arr = parent::requiredStyle();
        $arr[] = LOCAL . "/css/ProductListItem.css";
        return $arr;
    }

    public function setPhotoSize(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function getPhotoWidth(): int
    {
        return $this->width;
    }

    public function getPhotoHeight(): int
    {
        return $this->height;
    }

    public function setChipSize(int $chipSize)
    {
        $this->chipSize = $chipSize;
    }

    public function setData(array &$item)
    {
        parent::setData($item);
        $this->setAttribute("prodID", $this->data["prodID"]);
        $this->setAttribute("piID", $this->data["piID"]);

        if ($this->data["color_ids"]) {

            $this->colorSeries["id"] = explode("|", $this->data["color_ids"]);
            $this->colorSeries["photo"] = explode("|", $this->data["color_photo_ids"]);
            $this->colorSeries["name"] =  explode("|", $this->data["color_names"]);
            $this->colorSeries["code"] = explode("|", $this->data["color_codes"]);
            $this->colorSeries["inventories"] = explode("|", $this->data["inventory_ids"]);

        }
        else {
            $this->colorSeries = array();
        }


        if (isset($item["pclrpID"]) && $item["pclrpID"] > 0) {

            $this->photo->id = (int)$item["pclrpID"];
            $this->photo->className = "ProductColorPhotosBean";//ProductColorPhotosBean::class;
        }
        else if (isset($item["ppID"]) && $item["ppID"] > 0) {

            $this->photo->id = (int)$item["ppID"];
            $this->photo->className = "ProductPhotosBean";//ProductPhotosBean::class;
        }

        $this->detailsURL->setData($item);

    }

    protected function renderImpl()
    {

        echo "<div class='wrap'>";

        //echo $this->sel->getSQL();

        echo "<a href='{$this->detailsURL->url()}' class='product_link'>";
        $img_href = $this->photo->hrefThumb($this->width, $this->height);
        echo "<img src='$img_href'>";
        echo "</a>";

        echo "<div class='product_detail'>";
        $this->renderDetails();
        echo "</div>"; //product_details

        echo "</div>"; //wrap

    }

    protected function renderDetails()
    {

        echo "<div class='colors_container'>";
        $this->renderColorChips();
        echo "</div>"; //colors_container

        echo "<a class='product_name' href='{$this->detailsURL->url()}' >" . $this->data["product_name"] . "</a>";

        //echo "<div class='stock_amount'><label>".tr("Наличност").": </label>".$this->item["stock_amount"]."</div>";

        echo "<div class='sell_price'>";

        echo "<div class='item_price'>" . sprintf("%1.2f", $this->data["sell_price"]) . " " . tr("лв.") . "</div>";

        if ($this->data["price_min"] != $this->data["sell_price"] || $this->data["price_max"] != $this->data["sell_price"]) {
            echo "<div class='series_price'>" . sprintf("%1.2f", $this->data["price_min"]) . " " . tr("лв.") . " - " . sprintf("%1.2f", $this->data["price_max"]) . " " . tr("лв.") . "</div>";
        }

        echo "</div>"; //sell_price

    }

    protected function renderColorChips()
    {
        $haveColorSeries = count($this->colorSeries);

        if ($haveColorSeries < 1) return;

        $numColors = count($this->colorSeries["id"]);

        echo "<div class='colors'>" . $numColors . " " . ($numColors > 1 ? tr("цвята") : tr("цвят")) . "</div>";

        echo "<div class='color_chips'>";

        //
        foreach ($this->colorSeries["id"] as $idx => $id) {

            $code = $this->colorSeries["code"][$idx];
            $name = $this->colorSeries["name"][$idx];
            $photoID = $this->colorSeries["photo"][$idx];
            $inventoryID = $this->colorSeries["inventories"][$idx];

            $data["piID"]=$inventoryID;
            $this->detailsURL->setData($data);

            echo "<a class='chip' style='background-color:$code;' title='$name' piID='$inventoryID' href='{$this->detailsURL->url()}'>";

            if ($photoID>0) {
                $this->chip->id = $photoID;
                $href = $this->chip->hrefThumb($this->chipSize);
                echo "<img src='$href' >";
            }

            echo "</a>";
        }

        echo "</div>"; //color_chips
    }

    public function renderSeparator($idx_curr, $items_total)
    {

    }

}

?>
