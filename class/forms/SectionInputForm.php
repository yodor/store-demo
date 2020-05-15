<?php
include_once("forms/InputForm.php");

class SectionInputForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "section_title", "Секция", 1);
        $this->addInput($field);

    }

}

?>
