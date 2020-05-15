<?php
include_once("session.php");
include_once("pages/AdminLoginPage.php");
include_once("auth/AdminAuthenticator.php");
include_once("handlers/AuthenticatorRequestHandler.php");

include_once("forms/LoginForm.php");
include_once("forms/renderers/LoginFormRenderer.php");

$page = new AdminLoginPage();

$auth = new AdminAuthenticator();

$req = new AuthenticatorRequestHandler($auth);
$req->setCancelUrl(LOCAL . "admin/login.php");
$req->setSuccessUrl(LOCAL . "admin/index.php");

RequestController::addRequestHandler($req);

$af = new LoginForm();

$afr = new LoginFormRenderer($af, $req);

//$afr->getSubmitButton()->setClassName("admin_button orange");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Expires: 0");

$page->startRender();

$page->setPreferredTitle(tr("Administration"));

$afr->setCaption(SITE_TITLE . "<BR><small>" . tr("Administration") . "</small>");

$afr->render();

$page->finishRender();
?>