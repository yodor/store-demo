<?php
include_once ("lib/components/Component.php");
include_once ("lib/components/renderers/ICellRenderer.php");
include_once ("lib/components/TableColumn.php");

class OrderInvoiceCellRenderer extends TableCellRenderer implements ICellRenderer
{

    public function renderCell($row, TableColumn $tc)
    {

        $this->processAttributes($row, $tc);
        
        $this->startRender();

        $userID = (int)$row["userID"];
        $require_invoice = $row["require_invoice"];
        
        if ($require_invoice>0) {

            global $invoices;
            $invoice_details = $invoices->getByRef("userID", $userID);

            echo "<div class='group invoice_data'>";
                echo "<div class='item company_name'>";
                    echo "<label>".tr("Фирма")."</label>";
                    echo "<span>".$invoice_details["company_name"]."</span>";
                echo "</div>";
                echo "<div class='item acc_person'>";
                    echo "<label>".tr("МОЛ")."</label>";
                    echo "<span>".$invoice_details["acc_person"]."</span>";
                echo "</div>";
                echo "<div class='item city'>";
                    echo "<label>".tr("Град")."</label>";
                    echo "<span>".$invoice_details["city"]."</span>";
                echo "</div>";
                echo "<div class='item postcode'>";
                    echo "<label>".tr("Пощенски код")."</label>";
                    echo "<span>".$invoice_details["postcode"]."</span>";
                echo "</div>";
                echo "<div class='item address1'>";
                    echo "<label>".tr("Адрес1")."</label>";
                    echo "<span>".$invoice_details["address1"]."</span>";
                echo "</div>";
                echo "<div class='item address2'>";
                    echo "<label>".tr("Адрес2")."</label>";
                    echo "<span>".$invoice_details["address2"]."</span>";
                echo "</div>";
                echo "<div class='item vat'>";
                    echo "<label>".tr("ЕИК")."</label>";
                    echo "<span>".$invoice_details["vat"]."</span>";
                echo "</div>";
                echo "<div class='item vat_registered'>";
                    echo "<label>".tr("ДДС")."</label>";
                    echo "<span>".($invoice_details["vat_registered"]>0?tr("Да"):tr("Не"))."</span>";
                echo "</div>";
            echo "</div>";

        }
        else {
            echo tr("Без");
        }
        $this->finishRender();
    }

}
?>
