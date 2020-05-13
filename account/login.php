<?php
include_once("session.php");
include_once("auth/UserAuthenticator.php");
include_once("handlers/AuthenticatorRequestHandler.php");
include_once("forms/LoginForm.php");
include_once("forms/renderers/LoginFormRenderer.php");

include_once("class/pages/AccountPage.php");
include_once("class/forms/RegisterClientInputForm.php");
include_once("class/forms/processors/RegisterClientFormProcessor.php");

$page = new AccountPage(FALSE);

$auth = new UserAuthenticator();

$req = new AuthenticatorRequestHandler($auth, "doLogin");
$req->setCancelUrl(LOCAL . "account/login.php");
$req->setSuccessUrl(LOCAL . "account/index.php");

RequestController::addRequestHandler($req);

$af = new LoginForm();

$afr = new LoginFormRenderer($af, $req);

$afr->setAttribute("name", "auth");


$form = new RegisterClientInputForm();
$form->setName("RegisterClient");

$frender = new FormRenderer($form);
$frender->setLayout(FormRenderer::FIELD_VBOX);

$frender->getSubmitButton()->setText("Регистрация");

$proc = new RegisterClientFormProcessor();


$proc->process($form);

if ($proc->getStatus() == IFormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}
else if ($proc->getStatus() == IFormProcessor::STATUS_OK) {

    header("Location: delivery.php");
    exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Expires: 0");

$page->startRender();
$page->setPreferredTitle(tr("Вход"));

echo "<div class='caption'>" . tr("Регистрирани клиенти") . "</div>";

echo "<div class='panel'>";
echo "<div align=center>";
echo "<div class='login_component'>";
echo "<span class='inner'>";
$afr->render();
echo "</span>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "<div class='caption'>" . tr("Нови клиенти") . "</div>";

echo "<div class='panel'>";
echo "<div align=center>";
echo "<div class='register_component'>";
$frender->render();
echo "</div>";
echo "</div>";
echo "</div>";

$page->finishRender();
?>
