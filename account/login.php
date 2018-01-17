<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");

include_once("lib/handlers/AuthenticatorRequestHandler.php");

include_once("lib/forms/AuthForm.php");
include_once("lib/forms/renderers/AuthFormRenderer.php");

include_once("lib/auth/UserAuthenticator.php");

$page = new AccountPage(false);



$auth = new UserAuthenticator();

$req = new AuthenticatorRequestHandler($auth, "doLogin");
$req->setCancelUrl(SITE_ROOT."account/login.php");
$req->setSuccessUrl(SITE_ROOT."account/index.php");

RequestController::addRequestHandler($req);


$af = new AuthForm();

$afr = new AuthFormRenderer();


$afr->setAttribute("name", "auth");
$afr->setForm($af);
$afr->setAuthContext($auth->getAuthContext());


header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Expires: 0");


$page->beginPage();
$page->setPreferredTitle(tr("Вход за регистрирани клиенти"));
echo "<div class='caption'>".$page->getPreferredTitle()."</div>";


echo "<div class='panel'>";

  echo "<div align=center>";

  echo "<div class='login_component'>";

    echo "<span class='inner'>";
    $afr->renderForm($af);
    echo "</span>";


  echo "</div>";
  echo "</div>";
  
echo "</div>";

$page->finishPage();
?>
