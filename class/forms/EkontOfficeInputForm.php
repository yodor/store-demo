<?php
include_once("lib/forms/InputForm.php");

class EkontOfficeInputForm extends InputForm
{

    public function __construct()
    {

        $field = DataInputFactory::Create(DataInputFactory::TEXTAREA, "office", "Избран офис", 0);
        $field->getRenderer()->setFieldAttribute("readonly", "1");
        $this->addField($field);


    }

    public function renderPlain()
    {
        echo "<div class='InvoiceDetailsList'>";

        foreach ($this->getFields() as $index => $field) {
            echo "<div class='address_item'>";
            echo "<label>" . tr($field->getLabel()) . ": </label>";
            $value = strip_tags(stripslashes($field->getValue()));
            $value = str_replace("\r\n", "<BR>", $value);
            echo "<span>" . $value . "</span>";
            echo "</div>";
        }

        echo "</div>";
    }

}

?>
