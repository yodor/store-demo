<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");

include_once("lib/handlers/AuthenticatorRequestHandler.php");

include_once("lib/forms/AuthForm.php");
include_once("lib/forms/renderers/AuthFormRenderer.php");

include_once("lib/auth/UserAuthenticator.php");
include_once("class/forms/RegisterClientInputForm.php");
include_once("class/forms/processors/RegisterClientFormProcessor.php");

$page = new AccountPage(false);


$auth = new UserAuthenticator();

$req = new AuthenticatorRequestHandler($auth, "doLogin");
$req->setCancelUrl(SITE_ROOT . "account/login.php");
$req->setSuccessUrl(SITE_ROOT . "account/index.php");

RequestController::addRequestHandler($req);


$af = new AuthForm();

$afr = new AuthFormRenderer();


$afr->setAttribute("name", "auth");
$afr->setForm($af);
$afr->setAuthContext($auth->name());


$form = new RegisterClientInputForm();
$frender = new FormRenderer(FormRenderer::FIELD_VBOX);
$frender->setAttribute("name", "RegisterClient");
$frender->setForm($form);
$frender->getSubmitButton()->setText("Регистрация");
$form->setRenderer($frender);

$form->setProcessor(new RegisterClientFormProcessor());

$form->getProcessor()->processForm($form, "RegisterClient");

if ($form->getProcessor()->getStatus() == IFormProcessor::STATUS_ERROR) {
    Session::Set("alert", $form->getProcessor()->getMessage());
}
else if ($form->getProcessor()->getStatus() == IFormProcessor::STATUS_OK) {

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
$afr->renderForm($af);
echo "</span>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "<div class='caption'>" . tr("Нови клиенти") . "</div>";

echo "<div class='panel'>";
echo "<div align=center>";
echo "<div class='register_component'>";
$frender->renderForm($form);
//         $frender->startRender();
//         $frender->renderImpl();
//         echo "<input type='hidden' name='RegisterClient' value='submit'>";
//         $frender->finishRender();   
echo "</div>";
echo "</div>";
echo "</div>";

$page->finishRender();
?>
