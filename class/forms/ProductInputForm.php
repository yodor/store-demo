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

class ProductInputForm extends InputForm
{

    public function __construct()
    {
        parent::__construct();

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "section", "Section", 1);
        $rend = $field->getRenderer();
        $sb = new SectionsBean();

        $rend->setIterator($sb->query($sb->key(),"section_title"));
        $rend->getItemRenderer()->setValueKey("section_title");
        $rend->getItemRenderer()->setLabelKey("section_title");
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::NESTED_SELECT, "catID", "Category", 1);
        $bean1 = new ProductCategoriesBean();
        $rend = $field->getRenderer();

        $rend->setIterator(new SQLQuery($bean1->selectTree(array("category_name")), $bean1->key(), $bean1->getTableName()));
        $rend->getItemRenderer()->setValueKey("catID");
        $rend->getItemRenderer()->setLabelKey("category_name");

        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "brand_name", "Brand", 1);
        $rend = $field->getRenderer();
        $brands = new BrandsBean();

        $rend->setIterator($brands->query($brands->key(), "brand_name"));
        $rend->getItemRenderer()->setValueKey("brand_name");
        $rend->getItemRenderer()->setLabelKey("brand_name");
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "class_name", "Product Class", 0);
        $rend = $field->getRenderer();
        $pcb = new ProductClassesBean();
        $rend->setIterator($pcb->query($pcb->key(), "class_name"));
        $rend->getItemRenderer()->setValueKey("class_name");
        $rend->getItemRenderer()->setLabelKey("class_name");
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXT, "product_name", "Product Name", 1);
        $this->addInput($field);

        $field = DataInputFactory::Create(DataInputFactory::CHECKBOX, "visible", "Visible (on-sale)", 0);
        $this->addInput($field);

        // 	$field = DataInputFactory::CreateField(DataInputFactory::CHECKBOX, "promotion", "Promotion", 0);
        // 	$this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::MCE_TEXTAREA, "product_summary", "Description", 0);
        $this->addInput($field);

        // 	$field = DataInputFactory::CreateField(DataInputFactory::MCE_TEXTAREA, "product_description", "Product Description", 0);
        // 	$this->addField($field);

        $field = DataInputFactory::Create(DataInputFactory::TEXTAREA, "keywords", "Keywords", 0);
        $this->addInput($field);


        $field1 = new ArrayDataInput("feature", "Features", 0);
        $field1->source_label_visible = TRUE;

        $field1->setValidator(new EmptyValueValidator());
        $proc = new InputProcessor($field1);

        $renderer = new TextField($field1);
        new ArrayField($renderer);

        $features = new ProductFeaturesBean();
        $field1->getProcessor()->setTransactBean($features);

        $renderer->setIterator($features->queryFull());

        $this->addInput($field1);

    }

    public function loadBeanData($editID, DBTableBean $bean)
    {

        parent::loadBeanData($editID, $bean);

        //       $renderer = $this->getInput("value")->getRenderer();
        //       $renderer->setCategoryID($this->getInput("catID")->getValue());
        //       $renderer->setProductID($editID);

    }

    public function loadPostData(array $arr)
    {
        parent::loadPostData($arr);

        //       $renderer = $this->getInput("value")->getRenderer();
        //       $renderer->setCategoryID($arr["catID"]);

    }
}

?>
