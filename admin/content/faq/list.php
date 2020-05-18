<?php
include_once("session.php");
include_once("templates/admin/BeanListPage.php");
include_once("beans/FAQItemsBean.php");

$bean = new FAQItemsBean();

$cmp = new BeanListPage();

$menu = array(new MenuItem("Sections", "sections.php"));

$cmp->getPage()->setPageMenu($menu);

$qry = $bean->query();
$qry->select->from.= " fi LEFT JOIN faq_sections fs ON fs.fqsID = fi.fqsID ";
$qry->select->fields = " fi.fID, fs.section_name, fi.question, fi.answer ";
$cmp->setBean($bean);
$cmp->setIterator($qry);

$cmp->setListFields(array("section_name"=>"Section", "question"=>"Question", "answer"=>"Answer"));

$cmp->getPage()->navigation()->clear();

$cmp->render();
?>