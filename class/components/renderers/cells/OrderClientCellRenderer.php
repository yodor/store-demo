<?php
include_once("lib/components/Component.php");
include_once("lib/components/renderers/ICellRenderer.php");
include_once("lib/components/TableColumn.php");

class OrderClientCellRenderer extends TableCellRenderer implements ICellRenderer
{

    public function renderCell(array &$row, TableColumn $tc)
    {

        $this->processAttributes($row, $tc);

        $this->startRender();


        $userID = (int)$row["userID"];

        global $clients;

        $client = $clients->getByID($row["userID"], false, "fullname , email, phone");

        echo "<div class='group client_data'>";
        echo "<div class='item fullname'>";
        echo "<label>" . tr("Име") . "</label>";
        echo "<span>" . $client["fullname"] . "</span>";
        echo "</div>";
        echo "<div class='item email'>";
        echo "<label>" . tr("E-Mail") . "</label>";
        echo "<span>" . $client["email"] . "</span>";
        echo "</div>";
        echo "<div class='item phone'>";
        echo "<label>" . tr("Телефон") . "</label>";
        echo "<span>" . $client["phone"] . "</span>";
        echo "</div>";
        echo "</div>";

        $this->finishRender();
    }

}

?>
