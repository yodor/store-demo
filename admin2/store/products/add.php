<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("store/forms/ProductInputForm.php");
include_once("store/beans/ProductsBean.php");
include_once("store/beans/ProductInventoryBean.php");

//DBTableBean $bean, DBDriver $db, int $lastID, array $values
function DBTransactor_onAfterCommit(BeanTransactor $transactor, DBDriver $db)
{
    $values = $transactor->getValues();
    $lastID = $transactor->getLastID();

    $pibean = new ProductInventoryBean();

    //auto insert inventory
    if ($transactor->getEditID()<1) {
        $invrow = array("prodID"=> $lastID,
                        "stock_amount" => 1,
        );
        $pibean->insert($invrow);
    }
    else {
        //populate prices from product
        $update = new SQLUpdate($pibean->select());
        $update->set("price", $values["price"]);
        $update->set("buy_price", $values["buy_price"]);
        $update->set("promo_price", $values["promo_price"]);
        $update->where()->add("prodID", $lastID);


        $db->transaction();
        $db->query($update->getSQL());
        $db->commit();

    }

}

$cmp = new BeanEditorPage();
$cmp->setBean(new ProductsBean());
$cmp->setForm(new ProductInputForm());

if (isset($_GET["editID"])) {
    $cmp->getPage()->setName(tr("Редактиране на продукт"));
}


$cmp->initView();

$cmp->getEditor()->getTransactor()->assignInsertValue("insert_date", DBConnections::Get()->dateTime());
$cmp->render();
?>
