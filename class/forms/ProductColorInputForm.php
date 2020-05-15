<?php
include_once("forms/InputForm.php");
include_once("input/DataInputFactory.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/StoreColorsBean.php");


class ProductColorInputForm extends InputForm
{

    public function __construct($prodID)
    {

        parent::__construct();

        $field = DataInputFactory::Create(DataInputFactory::SELECT, "color", "Цветови код", 1);

        $rend = $field->getRenderer();
        $scb = new StoreColorsBean();
        $rend->setIterator($scb->query());
        $rend->getItemRenderer()->setValueKey("color");
        $rend->getItemRenderer()->setLabelKey("color");
        $rend->addon_content = "<a class='Action' action='new' href='../../colors/add.php'>" . tr("Нов цветови код") . "</a>";

        $opt = $rend->getItemRenderer();
        $opt->addDataAttribute("color_code");

        $this->addInput($field);
        // 	$field->enableTranslator(true);

        $input = DataInputFactory::Create(DataInputFactory::SESSION_IMAGE, "color_photo", "Чип за цвета", 0);
        // 	$input->setSource(new ProductPhotosBean());
        // 	$input->transact_mode = InputField::TRANSACT_OBJECT;
        // 	$input->getValueTransactor()->max_slots = 10;

        $input->getProcessor()->transact_mode = InputProcessor::TRANSACT_OBJECT;
        $input->getProcessor()->max_slots = 1;
        $this->addInput($input);

        $input = DataInputFactory::Create(DataInputFactory::SESSION_IMAGE, "photo", "Снимки", 0);
        $bean = new ProductColorPhotosBean();

        $input->getProcessor()->setTransactBean($bean);

        $input->getProcessor()->transact_mode = InputProcessor::TRANSACT_OBJECT;
        $input->getProcessor()->max_slots = 10;

        $this->addInput($input);

    }

    public function loadBeanData($editID, DBTableBean $bean)
    {

        $item_row = parent::loadBeanData($editID, $bean);
        //       $pclrID = (int)$item_row["pclrID"];
        //       $this->getInput("photo")->getSource()->setFilter(" pclrID ='$pclrID' ");

    }

    public function loadPostData(array $arr)
    {
        parent::loadPostData($arr);
        //       $pclrID = -1;
        //       $this->getInput("photo")->getSource()->setFilter(" pclrID ='$pclrID' ");

    }
}

?>
