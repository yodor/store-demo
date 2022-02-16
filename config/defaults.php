<?php

global $defines;
global $site_domain;

$defines->set("SITE_TITLE", "Store Demo");

$defines->set("TRANSLATOR_ENABLED", TRUE);
$defines->set("DB_ENABLED", TRUE);

$defines->set("DEFAULT_LANGUAGE", "bulgarian");
$defines->set("DEFAULT_LANGUAGE_ISO3", "bgn");

$defines->set("DEFAULT_CURRENCY", "BGN");

$defines->set("IMAGE_UPLOAD_DEFAULT_WIDTH", 1920);
$defines->set("IMAGE_UPLOAD_DEFAULT_HEIGHT", 1080);
$defines->set("IMAGE_UPLOAD_UPSCALE_ENABLED", FALSE);

//generic contacts email (not used)
$defines->set("DEFAULT_EMAIL_NAME", $site_domain . " Administration");
$defines->set("DEFAULT_EMAIL_ADDRESS", "info@" . $site_domain);

//default sender of the Mailer class (sender of service emails)
$defines->set("DEFAULT_SERVICE_NAME", "Sparkbox Store");
$defines->set("DEFAULT_SERVICE_EMAIL", "info@" . $site_domain);

$defines->set("DEFAULT_STOCK_AMOUNT", 10);

//errors from orders goes to this email (rcpt)
$defines->set("ORDER_ERROR_EMAIL", $defines->get("DEFAULT_SERVICE_EMAIL"));
//admin is notified for received order (rcpt)
$defines->set("ORDER_ADMIN_EMAIL", $defines->get("DEFAULT_SERVICE_EMAIL"));

?>
