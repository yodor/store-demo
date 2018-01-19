<?php
include_once ("lib/forms/InputForm.php");


class AttributeInputForm extends InputForm
{

    public function __construct()
    {
	$field = new InputField("name", "Име на атрибут", 1);
	$field->setRenderer(new TextField());
	$this->addField($field);

	$field = new InputField("unit", "Мярна единица", 0);
	$field->setRenderer(new TextField());
	$this->addField($field);
    }

}
?>
