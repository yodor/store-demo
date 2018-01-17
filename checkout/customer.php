<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");

include_once("class/forms/RegisterClientInputForm.php");
include_once("class/forms/processors/RegisterClientFormProcessor.php");

include_once("lib/auth/UserAuthenticator.php");
include_once("lib/handlers/AuthenticatorRequestHandler.php");
include_once("lib/forms/AuthForm.php");
include_once("lib/forms/renderers/AuthFormRenderer.php");


$page = new CheckoutPage();

if ($page->getUserID()>0) {
    header("Location:delivery.php");
    exit;
}

$auth = new UserAuthenticator();

$req = new AuthenticatorRequestHandler($auth, "doLogin");
$req->setCancelUrl(SITE_ROOT."checkout/customer.php");
$req->setSuccessUrl(SITE_ROOT."checkout/delivery.php");

RequestController::addRequestHandler($req);


if ($auth->checkAuthState()) {
  header("Location: delivery.php");
  exit;
}


$af = new AuthForm();
$afr = new AuthFormRenderer();
$afr->setAttribute("name", "auth");
$afr->setForm($af);
$afr->setAuthContext($auth->getAuthContext());
$afr->forgot_password_url = SITE_ROOT."account/forgot_password.php";

$form = new RegisterClientInputForm();

$frender = new FormRenderer();
$frender->setAttribute("name", "RegisterClient");
$frender->setForm($form);

$form->setRenderer($frender);

$form->setProcessor(new RegisterClientFormProcessor());

$form->getProcessor()->processForm($form, "RegisterClient");

if ($form->getProcessor()->getStatus() == IFormProcessor::STATUS_ERROR) {
  Session::set("alert", $form->getProcessor()->getMessage());
}
else if ($form->getProcessor()->getStatus() == IFormProcessor::STATUS_OK) {

  header("Location: delivery.php");
  exit;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: 0");

$page->beginPage();
$page->setPreferredTitle(tr("Клиенти"));




echo "<div class='item login'>";

  echo "<div class='caption'>".tr("Регистрирани Клиенти")."</div>";

  echo "<div class='login_component'>";
  echo "<div class='inner'>";
  $afr->renderForm($af);
  echo "</div>";
  echo "</div>";

echo "</div>";

//
echo "<div class='item register'>";

  echo "<div class='caption'>".tr("Нови Клиенти")."</div>";

  echo "<div class='panel'>";
  $frender->startRender();
  $frender->renderImpl();
  echo "<input type='hidden' name='RegisterClient' value='submit'>";
  $frender->finishRender();
  echo "</div>";
  
echo "</div>";



echo "<div class='navigation'>";

  
  echo "<div class='slot left'>";
    echo "<a href='cart.php'>";
    echo "<img src='".SITE_ROOT."images/cart_edit.png'>";
    echo "<div class='checkout_button' >".tr("Назад")."</div>";
    echo "</a>";
  echo "</div>";

  echo "<div class='slot center'>";
//     echo "<div class='note'>";
//         echo "<i>".tr("Натискайки бутона 'Продължи' Вие се съгласявате с нашите")."&nbsp;"."<a  href='".SITE_ROOT."terms.php'>".tr("Условия за ползване")."</a></i>";
//     echo "</div>";
  echo "</div>";
  
  echo "<div class='slot right'>";
    echo "<a href='javascript:document.forms.RegisterClient.submit();'>";
    echo "<img src='".SITE_ROOT."images/cart_checkout.png'>";
    echo "<div class='checkout_button'>".tr("Продължи")."</div>";
    echo "</a>";
  echo "</div>";
  // 
  // 

echo "</div>";



$page->finishPage();
?>
