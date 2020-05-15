<?php
include_once("components/renderers/cells/TableCellRenderer.php");

include_once("class/utils/Cart.php");

class OrderDeliveryCellRenderer extends TableCellRenderer
{

    protected $data = NULL;

    public function setData(array &$row)
    {
        parent::setData($row);
        $this->data = $row;
    }

    protected function renderImpl()
    {

        $orderID = $this->data["orderID"];
        $userID = $this->data["userID"];

        echo "<div class='group address_data'>";

        if (strcmp($this->data["delivery_type"], Cart::DELIVERY_EKONTOFFICE) == 0) {
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

    }
}

?>
