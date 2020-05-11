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

        $rend->setItemIterator(new SQLQuery($pcats->selectTree(array("category_name")), $pcats->key(), $pcats->getTableName()));
        $rend->getItemRenderer()->setValueKey("catID");
        $rend->getItemRenderer()->setLabelKey( "category_name");
        $rend->na_label = '--- TOP ---';
        $rend->na_value = "0";

        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::SESSION_IMAGE, "photo", "Снимка", 0);
        $this->addInput($field);

        $this->getInput("category_name")->enableTranslator(TRUE);
    }

}

?>
