<?php
include_once("lib/forms/InputForm.php");
include_once("class/utils/Cart.php");
include_once("lib/input/validators/NumericValidator.php");

class DeliveryConfigForm extends InputForm
{

    public function __construct()
    {

        $field = DataInputFactory::Create(DataInputFactory::TEXTFIELD, Cart::DELIVERY_USERADDRESS, Cart::getDeliveryTypeText(Cart::DELIVERY_USERADDRESS), 1);
        $field->setValidator(new NumericValidator());
        // 	    $field->getRenderer()->addon_content = "лв.";
        $this->addField($field);


        $field = DataInputFactory::Create(DataInputFactory::TEXTFIELD, Cart::DELIVERY_EKONTOFFICE, Cart::getDeliveryTypeText(Cart::DELIVERY_EKONTOFFICE), 1);
        $field->setValidator(new NumericValidator());
        // 	    $field->getRenderer()->addon_content = "лв.";
        $this->addField($field);

    }


}

?>
