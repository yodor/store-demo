<?php
include_once("session.php");
include_once("class/pages/StorePage.php");

$page = new StorePage();
$page->beginPage();
$page->setPreferredTitle("Контакти");
echo "<div class='caption'>".tr($page->getPreferredTitle())."</div>";
$page->finishPage();
?>
