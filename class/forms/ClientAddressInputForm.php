<?php
include_once ("lib/forms/InputForm.php");
include_once ("lib/input/InputFactory.php");


class ClientAddressInputForm extends InputForm
{

	public function __construct()
	{
	    parent::__construct();
	    
	    $field = InputFactory::CreateField(InputFactory::TEXTFIELD,"city", "Град",1);
	    $this->addField($field);
	    $field = InputFactory::CreateField(InputFactory::TEXTFIELD,"postcode","Пощенски код",1);
	    $this->addField($field);
	    $field = InputFactory::CreateField(InputFactory::TEXTFIELD,"address1", "Адрес (ред 1)", 1);
	    $this->addField($field);
	    $field = InputFactory::CreateField(InputFactory::TEXTFIELD,"address2", "Адрес (ред 2)", 0);
	    $this->addField($field);

	}
	
        public function renderPlain()
	{
            echo "<div class='ClientAddressList'>";

            foreach ($this->getFields() as $index=>$field) {
                echo "<div class='address_item'>";
                echo "<label>".tr($field->getLabel()).": </label>";
                echo "<span>".strip_tags(stripslashes($field->getValue()))."</span>";
                echo "</div>";
            }
            
            echo "</div>";
	}
}
?>
