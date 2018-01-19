<?php
include_once ("lib/forms/InputForm.php");
include_once ("class/beans/BrandsBean.php");
include_once ("lib/input/validators/URLValidator.php");

class BrandInputForm extends InputForm
{


    public function __construct()
    {
	  $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "brand_name", "Име на марка", 1);
	  $this->addField($field);

  	  $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "url", "URL", 0);
  	  $field->setValidator(new URLValidator());
	  $this->addField($field);

  	  $field = InputFactory::CreateField(InputFactory::MCE_TEXTAREA, "summary", "Описание", 0);
	  $this->addField($field);

	  
	  $field = InputFactory::CreateField(InputFactory::SESSION_IMAGE, "photo", "Снимка", 0);
	  $this->addField($field);

    }

}
?>
