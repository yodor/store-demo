<?php
include_once("forms/InputForm.php");
include_once("input/DataInputFactory.php");
include_once("class/beans/AttributesBean.php");
include_once("class/beans/ClassAttributesBean.php");
include_once("input/ArrayDataInput.php");

class ProductClassInputForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "class_name", "Име на класа", 1);
        $this->addInput($field);

        $field->enableTranslator(FALSE);

        $field1 = new ArrayDataInput("attribute_name", "Атрибут", 0);
        $field1->allow_dynamic_addition = TRUE;
        $field1->getProcessor()->setTransactBean(new ClassAttributesBean());
        // 	  $field1->getValueTransactor()->process_datasource_foreign_keys = true;
        $field1->getProcessor()->bean_copy_fields = array("class_name");

        $attribs = new AttributesBean();

        $rend = new SelectField($field1);
        $rend->setItemIterator($attribs->query());
        $rend->getItemRenderer()->setValueKey("name");
        $rend->getItemRenderer()->setLabelKey("name");

        $field1->setValidator(new EmptyValueValidator());

        $arend = new ArrayField($rend);

        $act_rend = new ActionRenderer(new Action("New attribute", "../attributes/add.php", array()));
        $act_rend->setName("Нов атрибут");
        $act_rend->setAttribute("action", "inline-new");
        $arend->addControl($act_rend);

        $this->addInput($field1);

    }

}

?>
