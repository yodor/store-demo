<?php
include_once("forms/InputForm.php");


class AttributeInputForm extends InputForm
{

    public function __construct()
    {
        $field = new DataInput("name", "Име на атрибут", 1);
        $field->setRenderer(new TextField());
        $this->addInput($field);

        $field = new DataInput("unit", "Мярна единица", 0);
        $field->setRenderer(new TextField());
        $this->addInput($field);
    }

}

?>
