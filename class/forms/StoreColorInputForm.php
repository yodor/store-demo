<?php
include_once ("lib/forms/InputForm.php");
include_once ("lib/input/InputFactory.php");


class StoreColorInputForm extends InputForm
{

    public function __construct()
    {

	  $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "color", "Име на цвят", 1);
	  $this->addField($field);
	  $field->enableTranslator(true);

	  $field = InputFactory::CreateField(InputFactory::COLORCODE, "color_code", "Цветови код", 0);
	  
	  $this->addField($field);

  }

}
?>
