<?php
include_once ("lib/forms/InputForm.php");
include_once ("lib/input/InputFactory.php");
include_once ("lib/selectors/DBEnumSelector.php");

class FAQItemInputForm extends InputForm
{

  public function __construct() 
  {
	
      $field = InputFactory::CreateField(InputFactory::SELECT, "section", "Секция", 1);

      $enm = new DBEnumSelector("faq_items", "section");
      $rend = $field->getRenderer();
      $rend->setSource($enm);
      $rend->list_key="section";
      $rend->list_label="section";

      $this->addField($field);

      $field = InputFactory::CreateField(InputFactory::TEXTFIELD, "question", "Въпрос", 1);
      $this->addField($field);

      $field = InputFactory::CreateField(InputFactory::TEXTAREA, "answer", "Отговор", 1);
      $this->addField($field);
	
  }

}
?>
