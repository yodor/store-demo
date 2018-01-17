<?php
include_once ("lib/forms/InputForm.php");
include_once ("lib/input/InputFactory.php");

class ContactRequestForm extends InputForm
{

	public function __construct()
	{
	    parent::__construct();
	    
	    $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "fullname", "Full Name", 1);
	    $this->addField($field);
	    
	    $field = InputFactory::CreateField(InputFactory::EMAIL, "email", "Email", 1);
	    $this->addField($field);
	    
	    $field = InputFactory::CreateField(InputFactory::TEXTAREA, "query", "Query", 1);
	    $this->addField($field);
	    
	    
	}

	
 }
?>