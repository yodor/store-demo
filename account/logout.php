<?php
include_once("session.php");
include_once("lib/auth/UserAuthenticator.php");

$auth = new UserAuthenticator();
$auth->logout();

header("Location: " . SITE_ROOT);
exit;

?>
