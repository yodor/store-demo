<?php
$GLOBALS["DEBUG_OUTPUT"] = 1;

$install_path = __DIR__;

$includes = array();
$includes[] = realpath($install_path. "/../sparkbox/lib" ); //sparkbox
$includes[] = realpath($install_path); //local classes
$includes[] = "."; //current www folder
ini_set("include_path", implode(PATH_SEPARATOR, $includes));
//echo ini_get("include_path");

include_once("sparkbox.php");



function formatPrice($price)
{
    return sprintf("%0.2f лв", $price);
}
?>
