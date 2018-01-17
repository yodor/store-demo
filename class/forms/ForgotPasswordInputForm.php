<?php
include_once ("lib/forms/InputForm.php");
include_once ("lib/input/InputFactory.php");


class ForgotPasswordInputForm extends InputForm
{

	public function __construct()
	{
	    parent::__construct();
	    $field = InputFactory::CreateField(InputFactory::TEXTFIELD,"email","Email",1);
	    $this->addField($field);

	}

}
?>