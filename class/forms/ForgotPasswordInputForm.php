<?php
include_once("lib/forms/InputForm.php");
include_once("lib/input/DataInputFactory.php");


class ForgotPasswordInputForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();
        $field = DataInputFactory::Create(DataInputFactory::TEXTFIELD, "email", "Email", 1);
        $this->addField($field);

    }

}

?>