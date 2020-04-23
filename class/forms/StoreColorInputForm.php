<?php
include_once("lib/forms/InputForm.php");
include_once("lib/input/DataInputFactory.php");


class StoreColorInputForm extends InputForm
{

    public function __construct()
    {

        $field = DataInputFactory::Create(DataInputFactory::TEXTFIELD, "color", "Име на цвят", 1);
        $this->addField($field);
        $field->enableTranslator(true);

        $field = DataInputFactory::Create(DataInputFactory::COLORCODE, "color_code", "Цветови код", 0);

        $this->addField($field);

    }

}

?>
