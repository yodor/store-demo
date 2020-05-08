<?php
include_once("forms/InputForm.php");
include_once("class/beans/ProductCategoriesBean.php");
include_once("class/beans/AttributesBean.php");
include_once("class/beans/ClassAttributesBean.php");
include_once("input/ArrayDataInput.php");

class ProductCategoryInputForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();

        $field = new DataInput("category_name", "Име на категория", 1);
        new TextField($field);
        $this->addInput($field);

        $field = new DataInput("parentID", "Родителска категория", 1);

        $pcats = new ProductCategoriesBean();

        $rend = new NestedSelectField($field);

        $rend->setIterator(new SQLQuery($pcats->listTreeSelect(), $pcats->key(), $pcats->getTableName()));
        $rend->list_key = "catID";
        $rend->list_label = "category_name";
        $rend->na_str = '--- TOP ---';
        $rend->na_val = "0";

        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::SESSION_IMAGE, "photo", "Снимка", 0);
        $this->addInput($field);

        $this->getInput("category_name")->enableTranslator(TRUE);
    }

}

?>
