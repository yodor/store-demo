<?php
include_once("lib/forms/InputForm.php");

class DeliveryAddressForm extends InputForm 
{

    public function __construct()
    {
        
        $aw1 = new ArraySelector(array(Cart::DELIVERY_USERADDRESS=>"Моят регистриран адрес", Cart::DELIVERY_EKONTOFFICE=>"До офис на Еконт"),"item_id", "item_value");
        $f12 = new InputField("delivery_type", "Изберете адрес", 1);
        $r12 = new RadioField();
        $r12->setSource($aw1);
        $r12->list_key = "item_id";
        $r12->list_label = "item_value";
        $f12->setRenderer($r12);
        $f12->setValidator(new EmptyValueValidator());
        $f12->setProcessor(new BeanPostProcessor());

        $this->addField($f12);

    }

   
  
}
?>
