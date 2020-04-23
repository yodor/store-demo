<?php
include_once("lib/components/Component.php");
include_once("lib/components/renderers/ICellRenderer.php");
include_once("lib/components/TableColumn.php");

include_once("class/utils/Cart.php");

class OrderDeliveryCellRenderer extends TableCellRenderer implements ICellRenderer
{

    public function renderCell($row, TableColumn $tc)
    {
        $this->processAttributes($row, $tc);

        $this->startRender();

        $orderID = $row["orderID"];
        $userID = $row["userID"];

        echo "<div class='group address_data'>";

        if (strcmp($row["delivery_type"], Cart::DELIVERY_EKONTOFFICE) == 0) {
            global $ekont_addresses;
            $row = $ekont_addresses->getByRef("userID", $userID);
            echo "<div class='caption'>" . tr("Офис на еконт") . "</div>";

            echo "<div class='item office'>";
            echo "<label>" . tr("Адрес") . "</label>";
            echo "<span>" . str_replace("\r\n", "<BR>", $row["office"]) . "</span>";
            echo "</div>";

        }
        else {
            global $client_addresses;
            $row = $client_addresses->getByRef("userID", $userID);

            echo "<div class='caption'>" . tr("Регистриран адрес на клиента") . "</div>";

            echo "<div class='item city'>";
            echo "<label>" . tr("Град") . "</label>";
            echo "<span>" . $row["city"] . "</span>";
            echo "</div>";

            echo "<div class='item postcode'>";
            echo "<label>" . tr("Пощенски код") . "</label>";
            echo "<span>" . $row["postcode"] . "</span>";
            echo "</div>";

            echo "<div class='item address1'>";
            echo "<label>" . tr("Адрес 1") . "</label>";
            echo "<span>" . $row["address1"] . "</span>";
            echo "</div>";

            echo "<div class='item address2'>";
            echo "<label>" . tr("Адрес 2") . "</label>";
            echo "<span>" . $row["address2"] . "</span>";
            echo "</div>";

        }


        echo "</div>";

        $this->finishRender();
    }
}

?>
