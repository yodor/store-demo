<?php
include_once("lib/forms/InputForm.php");

class SectionInputForm extends InputForm
{


    public function __construct()
    {
        $field = DataInputFactory::Create(DataInputFactory::TEXTFIELD, "section_title", "Секция", 1);
        $this->addField($field);

    }

}

?>
