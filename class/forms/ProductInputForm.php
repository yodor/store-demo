<?php
include_once("forms/InputForm.php");
include_once("input/DataInputFactory.php");

include_once("class/beans/BrandsBean.php");
include_once("class/beans/SectionsBean.php");

include_once("class/beans/ProductClassesBean.php");
include_once("class/beans/ProductCategoriesBean.php");
include_once("class/beans/ProductFeaturesBean.php");
include_once("class/beans/ProductPhotosBean.php");

include_once("class/beans/ClassAttributeValuesBean.php");
include_once("class/input/renderers/ClassAttributeField.php");
include_once("input/transactors/CustomFieldTransactor.php");

class ProductInputForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();

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

        $field1 = new ArrayDataInput("feature", "Характеристики", 0);
        $field1->allow_dynamic_addition = TRUE;
        $field1->source_label_visible = TRUE;

        $features_source = new ProductFeaturesBean();
        $field1->setSource($features_source);

        $renderer = new TextField($field1);
        $renderer->setIterator($features_source->query());

        $field1->setValidator(new EmptyValueValidator());
        $field1->setProcessor(new BeanPostProcessor());

        new ArrayField($renderer);

        $this->addInput($field1);


    }

    public function loadBeanData($editID, DBTableBean $bean)
    {

        parent::loadBeanData($editID, $bean);

        //       $renderer = $this->getInput("value")->getRenderer();
        //       $renderer->setCategoryID($this->getInput("catID")->getValue());
        //       $renderer->setProductID($editID);

    }

    public function loadPostData(array $arr): void
    {
        parent::loadPostData($arr);

        //       $renderer = $this->getInput("value")->getRenderer();
        //       $renderer->setCategoryID($arr["catID"]);

    }
}

?>
