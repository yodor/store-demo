<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/SectionsBean.php");
include_once("lib/components/TableView.php");

$menu=array(

);

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_add = new Action("", "add.php", array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Section");
$page->addAction($action_add);




$bean = new SectionsBean();

$h_position = new ChangePositionRequestHandler($bean);
RequestController::addRequestHandler($h_position);

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);


$view = new TableView(new BeanResultIterator($bean));
$view->setCaption("Sections List");
$view->setDefaultOrder(" position ASC ");

$view->addColumn(new TableColumn($bean->getPrKey(),"ID"));
$view->addColumn(new TableColumn("section_title","Section"));
$view->addColumn(new TableColumn("position","Position"));


$view->addColumn(new TableColumn("actions","Actions"));


$act = new ActionsTableCellRenderer();
$act->addAction(
  new Action("Edit", "add.php", array(new ActionParameter("editID",$bean->getPrKey()))  )
); 
$act->addAction(  new PipeSeparatorAction() );
$act->addAction( $h_delete->createAction() );
$act->addAction(  new RowSeparatorAction() );

$repos_param = array(
    new ActionParameter("item_id", $bean->getPrKey()), 
);

$act->addAction(new Action("First", "?cmd=reposition&type=first", $repos_param) ); 
$act->addAction(new PipeSeparatorAction());
$act->addAction(new Action("Last", "?cmd=reposition&type=last", $repos_param) ); 
$act->addAction(new RowSeparatorAction());
$act->addAction(new Action("Previous", "?cmd=reposition&type=previous", $repos_param) );
$act->addAction(new PipeSeparatorAction());
$act->addAction(new Action("Next", "?cmd=reposition&type=next", $repos_param) );

$act->addAction(  new RowSeparatorAction() );

$act->addAction(
  new Action("Banners Gallery", "banners/list.php", array(new ActionParameter("secID",$bean->getPrKey()))  )
);

$view->getColumn("actions")->setCellRenderer($act);



Session::set("sections.list", $page->getPageURL());

$page->beginPage($menu);
$page->renderPageCaption();

$view->render();



$page->finishPage();

?>
