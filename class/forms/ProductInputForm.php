<?php
include_once("lib/forms/InputForm.php");
include_once("lib/input/DataInputFactory.php");

include_once("class/beans/BrandsBean.php");
include_once("class/beans/SectionsBean.php");

include_once("class/beans/ProductClassesBean.php");
include_once("class/beans/ProductCategoriesBean.php");
include_once("class/beans/ProductFeaturesBean.php");
include_once("class/beans/ProductPhotosBean.php");

include_once("class/beans/ClassAttributeValuesBean.php");
include_once("class/input/renderers/ClassAttributeField.php");
include_once("lib/input/transactors/CustomFieldTransactor.php");


class ProductInputForm extends InputForm
{

    public function __construct()
    {

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "section", "Секция", 1);
        $rend = $field->getRenderer();
        $sb = new SectionsBean();
        $rend->setIterator($sb->query());
        $rend->list_key = "section_title";
        $rend->list_label = "section_title";
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::NESTED_SELECT, "catID", "Категория", 1);
        $bean1 = new ProductCategoriesBean();
        $rend = $field->getRenderer();
        $rend->setIterator($bean1->query());
        $rend->list_key = "catID";
        $rend->list_label = "category_name";

        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "brand_name", "Марка", 1);
        $rend = $field->getRenderer();
        $brands = new BrandsBean();
        $rend->setIterator($brands->query());
        $rend->list_key = "brand_name";
        $rend->list_label = "brand_name";
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "class_name", "Продуктов клас", 0);
        $rend = $field->getRenderer();
        $pcb = new ProductClassesBean();
        $rend->setIterator($pcb->query());
        $rend->list_key = "class_name";
        $rend->list_label = "class_name";
        $this->addInput($field);


        $field = DataInputFactory::Create(DataInputFactory::TEXT, "product_name", "Име на продукта", 1);
        $this->addInput($field);

        //         $field = DataInputFactory::CreateField(DataInputFactory::TEXTFIELD, "product_code", "Product Code", 1);
        // 	$this->addField($field);


        // 	$field = DataInputFactory::CreateField(DataInputFactory::TEXTFIELD, "price", "Price", 0);
        // 	$this->addField($field);
        //
        // 	$field = DataInputFactory::CreateField(DataInputFactory::TEXTFIELD, "buy_price", "Buy Price", 0);
        // 	$this->addField($field);
        //
        // 	$field = DataInputFactory::CreateField(DataInputFactory::TEXTFIELD, "old_price", "Old Price", 0);
        // 	$this->addField($field);
        //
        // 	$field = DataInputFactory::CreateField(DataInputFactory::TEXTFIELD, "weight", "Weight", 0);
        // 	$this->addField($field);


        // 	$field = DataInputFactory::CreateField(DataInputFactory::TEXTFIELD, "stock_amount", "Stock Amount", 1);
        // 	$this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::CHECKBOX, "visible", "Видим (в продажба)", 0);
        $this->addInput($field);

        // 	$field = DataInputFactory::CreateField(DataInputFactory::CHECKBOX, "promotion", "Promotion", 0);
        // 	$this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::MCE_TEXTAREA, "product_summary", "Описание", 0);
        $this->addInput($field);


        // 	$field = DataInputFactory::CreateField(DataInputFactory::MCE_TEXTAREA, "product_description", "Product Description", 0);
        // 	$this->addField($field);


        $field = DataInputFactory::Create(DataInputFactory::TEXTAREA, "keywords", "Ключови думи", 0);
        $this->addInput($field);


        // 	$input = DataInputFactory::CreateField(DataInputFactory::SESSION_IMAGE, "photo","Photo", 0);
        // 	$input->setSource(new ProductPhotosBean());
        // 	$input->transact_mode = InputField::TRANSACT_OBJECT;
        // 	$input->getValueTransactor()->max_slots = 4;
        // 	$this->addField($input);

        $field1 = new ArrayDataInput("feature", "Характеристики", 0);
        $field1->allow_dynamic_addition = true;
        $field1->source_label_visible = true;

        $features_source = new ProductFeaturesBean();
        $field1->setSource($features_source);

        $renderer = new TextField();
        $renderer->setIterator($features_source->query());
        $field1->setRenderer($renderer);

        $field1->setValidator(new EmptyValueValidator());
        $field1->setProcessor(new BeanPostProcessor());

        $this->addInput($field1);


        // 	$field = new ArrayInputField("value", "Optional Attributes", 0);
        // 	$field->allow_dynamic_addition = false;
        // 	$field->source_label_visible = true;
        // 	$field->getValueTransactor()->process_datasource_foreign_keys = true;
        //
        // 	$bean1 = new ClassAttributeValuesBean();
        // 	$field->setSource($bean1);
        //
        // 	$rend = new ClassAttributeField();
        // 	$field->setRenderer($rend);
        //
        // 	$this->addField($field);


    }

    public function loadBeanData($editID, DBTableBean $bean)
    {

        parent::loadBeanData($editID, $bean);

        //       $renderer = $this->getField("value")->getRenderer();
        //       $renderer->setCategoryID($this->getField("catID")->getValue());
        //       $renderer->setProductID($editID);

    }

    public function loadPostData(array $arr) : void
    {
        parent::loadPostData($arr);

        //       $renderer = $this->getField("value")->getRenderer();
        //       $renderer->setCategoryID($arr["catID"]);

    }
}

?>
