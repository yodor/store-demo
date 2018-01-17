<?php
include_once("session.php");
include_once("lib/auth/UserAuthenticator.php");

UserAuthenticator::logout();
header("Location: ".SITE_ROOT);
exit;

?>
