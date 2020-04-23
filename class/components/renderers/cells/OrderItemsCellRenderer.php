<?php
include_once("lib/components/Component.php");
include_once("lib/components/renderers/ICellRenderer.php");
include_once("lib/components/TableColumn.php");
include_once("class/beans/OrderItemsBean.php");

class OrderItemsCellRenderer extends TableCellRenderer implements ICellRenderer
{

    public function renderCell($row, TableColumn $tc)
    {

        $this->processAttributes($row, $tc);

        $this->startRender();

        $orderID = $row["orderID"];


        global $order_items;

        $order_items->startIterator("WHERE orderID='$orderID' ORDER BY position ASC");

        echo "<div class='group order_items'>";

        while ($order_items->fetchNext($item)) {

            $piID = $item["piID"];
            $prodID = $item["prodID"];

            $itemID = $item["itemID"];


            echo "<div class='item qty'>";
            echo "<label>" . tr("Позиция") . "</label>";
            echo "<span>" . $item["position"] . "</span>";
            echo "</div>";

            echo "<div class='item photo'>";
            echo "<label>";
            echo $order_items->getThumb($itemID, 100);
            echo "</label>";

            echo "</div>";


            $details = explode("//", $item["product"]);
            foreach ($details as $index => $data) {
                $label_value = explode("||", $data);

                echo "<div class='item'>";
                echo "<label>" . tr($label_value[0]) . "</label>";
                echo "<span>" . $label_value[1] . "</span>";
                echo "</div>";
            }


            echo "<div class='item qty'>";
            echo "<label>" . tr("Количество") . "</label>";
            echo "<span>" . $item["qty"] . "</span>";
            echo "</div>";

            echo "<div class='item price'>";
            echo "<label>" . tr("Цена") . "</label>";
            echo "<span>" . sprintf("%0.2f лв.", $item["price"]) . "</span>";
            echo "</div>";

            echo "<div class='item price'>";
            echo "<label>" . tr("Цена общо") . "</label>";
            echo "<span>" . sprintf("%0.2f лв.", ($item["qty"] * $item["price"])) . "</span>";
            echo "</div>";


            if ($prodID > 0) {
                echo "<a class='ActionRenderer' href='" . SITE_ROOT . "admin/store/products/inventory/list.php?prodID=$prodID'>" . tr("Виж инвентар") . "</a>";
            }
        }

        echo "</div>";

        $this->finishRender();
    }

}

?>
