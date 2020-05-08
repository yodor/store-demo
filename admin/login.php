<?php
include_once("session.php");
include_once("pages/AdminLoginPage.php");
include_once("auth/AdminAuthenticator.php");
include_once("handlers/AuthenticatorRequestHandler.php");

include_once("forms/AuthForm.php");
include_once("forms/renderers/AuthFormRenderer.php");


$page = new AdminLoginPage();

$auth = new AdminAuthenticator();

$req = new AuthenticatorRequestHandler($auth, "doLogin");
$req->setCancelUrl(LOCAL . "admin/login.php");
$req->setSuccessUrl(LOCAL . "admin/index.php");

RequestController::addRequestHandler($req);


$af = new AuthForm();

$afr = new AuthFormRenderer();


$afr->setAttribute("name", "auth");
$afr->setForm($af);

$afr->getSubmitButton()->setClassName("admin_button orange");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Expires: 0");


$page->startRender();

//set the token after RequestController processHandlers is done
$af->getInput("rand")->setValue($auth->createLoginToken());


$page->setPreferredTitle("Login");

echo "<div class='login_component'>";

//   echo "<div style='float:left'>";
//   echo "<img src='".LOCAL."admin/pics/admin_logo.png'>";
//   echo "</div>";

echo "<span class='inner'>";

echo "<span class='caption'>Administration</span>";
$afr->renderForm($af);
echo "</span>";


echo "</div>";

$page->finishRender();
?>