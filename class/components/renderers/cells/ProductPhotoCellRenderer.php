<?php

include_once("components/renderers/cells/TableImageCellRenderer.php");

class ProductPhotoCellRenderer extends TableImageCellRenderer
{

    protected function constructItems()
    {
        $this->items = array();

        $item = NULL;

        if (isset($row["pclrpID"]) && $row["pclrpID"] > 0) {
            $item = new ImageItem();
            $item->item_id = (int)$row["pclrpID"];
            $item->item_class = "ProductColorPhotosBean";
        }
        else if (isset($row["ppID"]) && $row["ppID"] > 0) {
            $item = new ImageItem();
            $item->item_id = (int)$row["ppID"];
            $item->item_class = "ProductPhotosBean";
        }

        $this->items[] = $item;
    }
}

?>
