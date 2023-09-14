<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");

include_once("store/beans/VariantOptionsBean.php");
include_once("store/beans/ProductsBean.php");


$menu = array(
//    new MenuItem("Inventory", "inventory/list.php", "list"),
);

$cmp = new BeanListPage();

$bean = new VariantOptionsBean();

$req = new BeanKeyCondition($bean, "list.php", array("option_name"));
$bean->select()->where()->add("parentID" , $req->getID());

$cmp->getPage()->setName(tr("Parameters for option") . ": " . $req->getData("option_name"));


$cmp->setBean($bean);


$cmp->setListFields(array("voID"=>"ID", "position"=>"Position", "option_value"=>"Parameter"));
$query = $bean->queryFull();

$cmp->setIterator($query);


$view = $cmp->initView();
//$cmp->getView()->setDefaultOrder(" color ASC ");
//$cmp->getView()->getColumn("color_code")->setCellRenderer(new ColorCodeCellRenderer());

//$cmp->getPage()->navigation()->clear();


$act = $cmp->viewItemActions();

$act->append(new RowSeparator());

//$act->append(new Action("Parameters", "parameters.php", array(new DataParameter("voID"))));


$closure = function(ClosureComponent $cmp) {
    echo "<div class='help summary'>";
    echo "Тук добавяте избираеми параметри за съответната опция.<BR>";
    echo "Параметрите се използват за избор от клиента преди поръчка на продукт.<BR>";
    echo "</div>";
};
$cmp->insert(new ClosureComponent($closure), 0);
$cmp->render();

?>
