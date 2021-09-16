<?php
$info = pathinfo($_SERVER['SCRIPT_FILENAME']);
$info = pathinfo($info["dirname"]);
//echo $info["dirname"]."/session.php";
include_once($info["dirname"]."/session.php");
?>