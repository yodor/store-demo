<?php
include_once("forms/InputForm.php");
include_once("input/DataInputFactory.php");
include_once("input/renderers/ArrayField.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ClassAttributesBean.php");
include_once("class/beans/ProductColorsBean.php");
include_once("class/beans/StoreSizesBean.php");
include_once("class/beans/InventoryAttributeValuesBean.php");

include_once("class/input/renderers/ClassAttributeField.php");
include_once("class/input/renderers/SourceRelatedField.php");
include_once("class/beans/ProductCategoriesBean.php");

class ProductInventoryInputForm extends InputForm
{

    protected $prodID = -1;
    protected $product = array();

    public function __construct()
    {

        parent::__construct();

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "pclrID", "Цветова схема", 0);
        $colors = new ProductColorsBean();
        $field->getRenderer()->setIterator($colors->query());

        $field->getRenderer()->list_key = "pclrID";
        $field->getRenderer()->list_label = "color";

        $field->getValueTransactor()->renderer_source_copy_fields = array("color");

        $this->addInput($field);


        $field = DataInputFactory::Create(DataInputFactory::SELECT, "size_value", "Оразмеряване", 0);
        $sizes = new StoreSizesBean();
        $field->getRenderer()->setIterator($sizes->query());
        $field->getRenderer()->list_key = "size_value";
        $field->getRenderer()->list_label = "size_value";

        $field->getRenderer()->addon_content = "<a class='ActionRenderer' action='inline-new' href='../../sizes/add.php'>" . tr("Нов код за оразмеряване") . "</a>";

        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "stock_amount", "Стокова наличност", 1);
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "price", "Продажна цена", 0);
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "buy_price", "Покупна цена", 0);
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "old_price", "Стара цена", 0);
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "weight", "Тегло", 0);
        $this->addInput($field);

        //1. input is taking array of values (ArrayDataInput)
        //2. renderer is drawing single element with many items (DataSourceField)
        $field = new ArrayDataInput("value", "Атрибути на класа", 0);
        $field->allow_dynamic_addition = false;

        $field->source_label_visible = true;

        $field->getValueTransactor()->process_datasource_foreign_keys = true;

        $bean1 = new InventoryAttributeValuesBean();
        $field->setSource($bean1);

        $rend = new SourceRelatedField($field);

        $bean = new ClassAttributesBean();
        $rend->setIterator($bean->query());

        $rend->list_key = "value";
        $rend->list_label = "attribute_name";

        $this->addInput($field);
    }

    public function setProductID(int $prodID)
    {
        $this->prodID = (int)$prodID;

        $this->getInput("pclrID")->getRenderer()->getIterator()->select->where = " prodID='{$this->prodID}' ";

        $this->getInput("pclrID")->getRenderer()->addon_content = "<a class='ActionRenderer' action='inline-new' href='../color_gallery/add.php?prodID={$this->prodID}'>" . tr("Нова цветова схема") . "</a>";

        // 	  $this->getField("size_value")->getRenderer()->setFilter(" WHERE prodID='{$this->prodID}' ");

        $prods = new ProductsBean();
        $this->product = $prods->getByID($this->prodID);

        $rend = $this->getInput("value")->getRenderer();

        $rend->setCaption(tr("Продуктов клас") . ": " . $this->product["class_name"]);

        $sel = $rend->getIterator()->select;
        $sel->where = " ca.class_name='{$this->product["class_name"]}' ";
        $sel->from = $rend->getIterator()->name()." ca LEFT JOIN attributes attr ON attr.name = ca.attribute_name ";
        $sel->fields = " ca.*, attr.unit as attribute_unit, attr.type attribute_type ";

        //debug($sel->getSQL());

        $this->getInput("price")->setValue($this->product["price"]);
        $this->getInput("buy_price")->setValue($this->product["buy_price"]);
        $this->getInput("old_price")->setValue($this->product["old_price"]);
        $this->getInput("weight")->setValue($this->product["weight"]);

    }

    public function loadBeanData(int $editID, DBTableBean $bean)
    {

        $item_row = parent::loadBeanData($editID, $bean);

        $rend = $this->getInput("value")->getRenderer();

        $sel = $rend->getIterator()->select;

        $sel->from = $rend->getIterator()->name()." ca LEFT 
        JOIN inventory_attribute_values iav ON iav.caID = ca.caID AND iav.piID='$editID' LEFT 
        JOIN attributes attr ON attr.name = ca.attribute_name ";

        $sel->where = " ca.class_name='{$this->product["class_name"]}'";

        $sel->fields = " ca.*, iav.value, attr.unit as attribute_unit, attr.type attribute_type ";

    }

    public function loadPostData(array $arr) : void
    {
        parent::loadPostData($arr);

    }
}

?>
