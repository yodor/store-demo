<?php
include_once ("lib/forms/InputForm.php");

class SectionInputForm extends InputForm
{


    public function __construct()
    {
	  $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "section_title", "Section", 1);
	  $this->addField($field);

    }

}
?>
