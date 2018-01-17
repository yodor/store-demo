<?php
include_once ("lib/forms/InputForm.php");
include_once ("lib/input/InputFactory.php");


class RegisterClientInputForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();
        
        $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "fullname", "Full Name", 1);
        $this->addField($field);

        $field = InputFactory::CreateField(InputFactory::EMAIL, "email", "Email", 1);
        $this->addField($field);

        $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "phone", "Phone", 1);
        $this->addField($field);

    }

}
?>
