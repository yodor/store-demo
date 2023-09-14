<?php
include_once("session.php");
include_once("templates/admin/BeanEditorPage.php");
include_once("class/forms/ProductInputForm.php");
include_once("store/beans/ProductsBean.php");
include_once("store/utils/CheckStockState.php");


$cmp = new BeanEditorPage();
//
$cmp->setBean(new ProductsBean());
$cmp->setForm(new ProductInputForm());
$cmp->initView();

$transactor = $cmp->getEditor()->getTransactor();
$transactor->assignInsertValue("insert_date", DBConnections::Get()->dateTime());

$old_stock_amount = -1;
$closure_editor = function(BeanFormEditorEvent $event) use (&$old_stock_amount) {
    if ($event->isEvent(BeanFormEditorEvent::FORM_BEAN_LOADED)) {
        $editor = $event->getSource();
        if ($editor instanceof BeanFormEditor) {
            $old_stock_amount = $editor->getForm()->getInput("stock_amount")->getValue();
            debug("Current stock_amount: $old_stock_amount");
        }
    }
};
$cmp->getEditor()->getObserver()->setCallback($closure_editor);

$closure_transactor = function(BeanTransactorEvent $event) use(&$old_stock_amount) {

    if ($event->isEvent(BeanTransactorEvent::AFTER_COMMIT)) {
        $transactor = $event->getSource();
        if (!($transactor instanceof BeanTransactor)) return;
        $prodID = $transactor->getEditID();
        if ($prodID<1) return;

        $stock_amount = $transactor->getValue("stock_amount");
        $proc = new CheckStockState($prodID, $transactor->getValue("product_name"));
        $proc->process($stock_amount, $old_stock_amount);
    }

};
$transactor->getObserver()->setCallback($closure_transactor);

$cmp->render();


?>
