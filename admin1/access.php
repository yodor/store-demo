<?php
include_once("session.php");
include_once("store/pages/AdminPage.php");

$menu = array();

$content = new AdminPage();

$content->startRender($menu);

echo "Access to this resource is not allowed for your account.";

$content->finishRender();

?>
