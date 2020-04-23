<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/OrdersBean.php");
include_once("class/handlers/OrderStatusRequestHandler.php");
include_once("lib/handlers/DeleteItemRequestHandler.php");
include_once("class/utils/OrdersQuery.php");

$page = new AdminPage();
$page->checkAccess(ROLE_ORDERS_MENU);

$bean = new OrdersBean();

<<<<<<< HEAD
$h_send = new ConfirmSendRequestHandler();
=======
$h_send = new OrderStatusRequestHandler($bean);
>>>>>>> origin/master
RequestController::addRequestHandler($h_send);

// $h_delete = new DeleteItemRequestHandler($bean);
// RequestController::addRequestHandler($h_delete);


$sel = new OrdersQuery();

$sel->where = " o.status='" . OrdersBean::STATUS_SENT . "' ";


include_once("list.php");

$act = $view->getColumn("actions")->getCellRenderer();
$act->addAction(
  new Action(
	"Потвърди завършване", "?cmd=order_status", 
	array(
	  new ActionParameter("orderID", "orderID"),
	  new ActionParameter("status", OrdersBean::STATUS_COMPLETED,true),
	)
  )
  
); 

$menu = array();

$page->startRender($menu);
$page->renderPageCaption();

$scomp->render();

$view->render();

$page->finishRender();
?>
