<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/SectionsBean.php");
include_once("class/beans/SectionBannersBean.php");

include_once("lib/forms/PhotoInputForm.php");



$ref_key="";
$ref_val="";
$qrystr=refkeyPageCheck(new SectionsBean(), "../list.php", $ref_key, $ref_id);

$menu=array(

);

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::get("section.banners.list"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back To Banners");
$page->addAction($action_back);

$photos = new SectionBannersBean();
$photos->setFilter("$ref_key='$ref_id'");

$form = new PhotoInputForm();
$field = new InputField("link", "Link", 1);
$field->setRenderer(new TextField());
// $field->content_after = "<a class='ActionRenderer DynamicPageChooser' href='".ADMIN_ROOT."content/pages/list.php?chooser=1'>".tr("Choose Dynamic Page")."</a>";
$form->addField($field);
$view = new InputFormView($photos, $form);

//current version of dynamic page photos table is set to DBROWS
$view->getForm()->getField("photo")->transact_mode = InputField::TRANSACT_OBJECT;

$view->getTransactor()->appendValue($ref_key, $ref_id);

$view->processInput();

$page->beginPage($menu);

$page->renderPageCaption();

$view->render();

$page->finishPage();


?>
