<?php
include_once("forms/InputForm.php");

class CourierOfficeInputForm extends InputForm
{

    public function __construct()
    {

        parent::__construct();

        $field = DataInputFactory::Create(DataInputFactory::TEXTAREA, "office", "Избран офис", 1);
        //$field->getRenderer()->setInputAttribute("readonly", "1");
        $this->addInput($field);

    }

//    public function renderPlain()
//    {
//        echo "<div class='InvoiceDetailsList'>";
//
//        foreach ($this->getInputs() as $index => $field) {
//            echo "<div class='address_item'>";
//            echo "<label>" . tr($field->getLabel()) . ": </label>";
//            $value = strip_tags(stripslashes($field->getValue()));
//            $value = str_replace("\r\n", "<BR>", $value);
//            echo "<span>" . $value . "</span>";
//            echo "</div>";
//        }
//
//        echo "</div>";
//    }

}

?>
