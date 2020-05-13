<?php
include_once("forms/InputForm.php");
include_once("iterators/ArrayDataIterator.php");

class DeliveryAddressForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();

        $data = new ArrayDataIterator(array(Cart::DELIVERY_USERADDRESS => "Моят регистриран адрес",
                                            Cart::DELIVERY_EKONTOFFICE => "До офис на Еконт"));

        $field = new DataInput("delivery_type", "Изберете адрес", 1);

        $radio = new RadioField($field);
        $radio->setItemIterator($data);
        $radio->getItemRenderer()->setValueKey(ArrayDataIterator::KEY_ID);
        $radio->getItemRenderer()->setLabelKey(ArrayDataIterator::KEY_VALUE);

        $field->setValidator(new EmptyValueValidator());
        new InputProcessor($field);

        $this->addInput($field);

    }

}

?>
