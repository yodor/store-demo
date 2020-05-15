<?php
define("SKIP_LANGUAGE", 1);
define("PERSISTENT_DB", 1);

include_once("session.php");
Session::Close();

$GLOBALS["DEBUG_OUTPUT"] = 0;

include_once("storage/BeanDataRequest.php");
$storage = new BeanDataRequest();
?>