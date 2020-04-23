<?php
include_once("lib/forms/InputForm.php");
include_once("lib/input/DataInputFactory.php");

class ContactRequestForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();

        $field = DataInputFactory::Create(DataInputFactory::TEXTFIELD, "fullname", "Име", 1);
        $this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::EMAIL, "email", "Email", 1);
        $this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXTAREA, "query", "Запитване", 1);
        $this->addField($field);


    }


}

?>
