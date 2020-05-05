<?php
include_once("lib/forms/InputForm.php");
include_once("class/beans/BrandsBean.php");
include_once("lib/input/validators/URLValidator.php");

class BrandInputForm extends InputForm
{


    public function __construct()
    {
        $field = DataInputFactory::Create(DataInputFactory::TEXT, "brand_name", "Име на марка", 1);
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "url", "URL", 0);
        $field->setValidator(new URLValidator());
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::MCE_TEXTAREA, "summary", "Описание", 0);
        $this->addInput($field);


        $field = DataInputFactory::Create(DataInputFactory::SESSION_IMAGE, "photo", "Снимка", 0);
        $this->addInput($field);

    }

}

?>
