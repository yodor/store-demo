<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("class/forms/ProductInventoryInputForm.php");
include_once("class/beans/ProductInventoryBean.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductColorsBean.php");

class MultiAddInventoryProcessor extends FormProcessor {

    public function __construct()
    {
        parent::__construct();
    }
    protected function processImpl(InputForm $form)
    {
        parent::processImpl($form);

        global $prodID, $rc;

        $bean = new ProductInventoryBean();
        $colors_bean = new ProductColorsBean();

        $colorids = $form->getInput("pclrID")->getValue();
        $sizes = $form->getInput("size_value")->getValue();


        $num_items = 0;

        foreach ($colorids as $colorKey=>$colorID) {
            debug("Processing colorID: $colorID");
            foreach ($sizes as $sizeKey=>$size_value) {
                debug("Processing size_value: $size_value");

                $qry = $bean->query("prodID", "pclrID", "size_value");
                $qry->select->where()->add("prodID", $prodID)->add("pclrID", $colorID)->add("size_value", "'".$size_value."'");
                $num = $qry->exec();
                debug($qry->select->getSQL());
                if ($num>0) {
                    debug ("This combination already exists ($colorID/$size_value)");
                    continue;
                }

                $color_name = $colors_bean->getValue($colorID, "color");

                $row = array("prodID"=>$prodID,
                             "pclrID"=>$colorID,
                             "color"=>$color_name,
                             "size_value"=>$size_value,
                             "price"=>$rc->getData("price"),
                             "promo_price"=>$rc->getData("promo_price"),
                             "stock_amount"=>DEFAULT_STOCK_AMOUNT
                );

                $lastID = $bean->insert($row);
                $num_items ++;
            }
        }

        Session::SetAlert("Бяха добавени $num_items елемента в инвентара на продукта");

    }
}

$rc = new BeanKeyCondition(new ProductsBean(), "../list.php", array("product_name", "class_name", "brand_name",
                                                                    "section", "catID", "price", "promo_price"));
$prodID = (int)$rc->getID();

$page = new AdminPage();

//$cmp = new BeanEditorPage();
//$cmp->setRequestCondition($rc);
$cats = new ProductCategoriesBean();
$category_name = $cats->getValue($rc->getData("catID"), "category_name");


$pageName = tr("Multi-Add").": ".$rc->getData("section") . " / " . $category_name. " / " . $rc->getData("product_name");

$page->setName($pageName);

$form = new ProductInventoryInputForm(true);
$form->getInput("pclrID")->setRequired(true);
$form->getInput("pclrID")->setValidator(new EmptyValueValidator());
$form->getInput("size_value")->setRequired(true);


$form->setProductID($prodID);
$frend = new FormRenderer($form);



$proc = new MultiAddInventoryProcessor();
$proc->process($form);

if ($proc->getStatus() === IFormProcessor::STATUS_OK) {
//    header("Location: list.php?prodID=$prodID");
//    exit;
}
else if ($proc->getStatus() === IFormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}

$page->startRender();

echo "<div class='note'>";
echo tr("Тук може да добавите комбинация от цветове и размери от избрания продукт.");
echo "</div>";

$frend->render();

$page->finishRender();

?>
