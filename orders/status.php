<?php
include_once("session.php");
include_once("class/pages/ProductsPage.php");

include_once("class/beans/OrdersBean.php");

include_once("lib/forms/processors/FormProcessor.php");
include_once("lib/forms/renderers/FormRenderer.php");


include_once("class/forms/OrderStatusInputForm.php");
include_once("class/forms/OrderAddressInputForm.php");

class OrderStatusProcessor extends FormProcessor 
{
    public $order = NULL;
    public $orderID = -1;
    public $confirm_ticket = "";


    
    protected function processImpl(InputForm $form)
    {

	parent::processImpl($form);

	if ($this->status != IFormProcessor::STATUS_OK) return;
	
	$orders = new OrdersBean();

	$ticket = $form->getField("ticket")->getValue();
	$email = $form->getField("email")->getValue();

	$num = $orders->startIterator("WHERE order_identifier='$ticket' AND client_identifier='$email' LIMIT 1");

	if ($num>0 && $orders->fetchNext($order_row)) {

	  $this->order = $order_row;
	  $this->orderID = $order_row[$orders->getPrKey()];
	  $this->confirm_ticket = $ticket;

	}	
	else {
	  throw new Exception(tr("Поръчката с този код не е намерена. Моля, уверете се, че сте въвели правилно вашият код на поръчка и email."));
	}
    }
}


$page = new ProductsPage();

$form = new OrderStatusInputForm();


$tfr = new FormRenderer();
$tfr->setName("OrderStatus");

$form->setRenderer($tfr);

$proc = new OrderStatusProcessor();

$form->setProcessor($proc);


$proc->processForm($form);


if ($proc->getStatus() == IFormProcessor::STATUS_ERROR) {

  Session::set("alert", tr($form->getProcessor()->getMessage()));

}

$page->beginPage();

echo "<div class='column left'>";
$page->renderCategoryTree();
$page->renderNewsItems();

echo "</div>";
	
echo "<div class='column right orders'>";


if ($proc->getStatus() == IFormProcessor::STATUS_OK) {

  echo "<div class='caption'>";
  echo tr("Състояние на поръчка");
  echo "</div>";
  
  $page->renderOrderDetails($proc->orderID, $proc->order, $proc->confirm_ticket);

  
  $page->renderOrderDeliveryAddress($proc->order);
  
 

}
else {

  echo "<div class='caption'>";
  echo tr("Състояние на поръчка");
  echo "</div>";


  echo "<div class='panel'>";
  
  echo tr("Тук може да проверите състоянието на вашата поръчка");
  echo "<BR>";
  echo tr("Въведете Вашият email и код за потвърждение за да продължите.");
  echo "<BR>";
  
  $tfr->renderForm($form);
  
  echo "</div>";
}

echo "</div>";

$page->finishPage();
?>