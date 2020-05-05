<?php
include_once("lib/forms/InputForm.php");
include_once("lib/input/DataInputFactory.php");


class StoreColorInputForm extends InputForm
{

    public function __construct()
    {

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "color", "Име на цвят", 1);
        $this->addInput($field);
        $field->enableTranslator(true);

        $field = DataInputFactory::Create(DataInputFactory::COLOR_CODE, "color_code", "Цветови код", 0);

        $this->addInput($field);

    }

}

?>
