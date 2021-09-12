<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");

include_once("beans/UsersBean.php");
include_once("mailers/ForgotPasswordMailer.php");

include_once("class/forms/ForgotPasswordInputForm.php");

include_once("auth/Authenticator.php");

class ForgotPasswordProcessor extends FormProcessor
{
    protected function processImpl(InputForm $form)
    {
        parent::processImpl($form);

        if ($this->status != IFormProcessor::STATUS_OK) return;

        $email = $form->getInput("email")->getValue();

        global $users;

        if (!$users->emailExists($email)) {
            $form->getInput("email")->setError(tr("Този адрес не е регистриран при нас"));
            throw new Exception(tr("Този адрес не е регистриран при нас"));
        }

        $random_pass = Authenticator::RandomToken(8);

        $db = DBConnections::Factory();
        try {
            $db->transaction();

            $userID = $users->email2id($email);
            $update_row = array();
            $update_row["password"] = md5($random_pass);
            if (!$users->update($userID, $update_row, $db)) throw new Exception("Невъзможна промяна на запис: " . $db->getError());

            $login_url = SITE_URL.LOCAL."/account/";
            $fpm = new ForgotPasswordMailer($email, $random_pass, $login_url);
            $fpm->send();

            $db->commit();
            $this->setMessage(tr("Вашата нова парола беше изпратена на адрес") . ": $email");
        }
        catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}

$page = new AccountPage(FALSE);

$users = new UsersBean();

$form = new ForgotPasswordInputForm();

$frend = new FormRenderer($form);
$frend->setName("ForgotPassword");
$frend->getSubmitButton()->setContents("Изпрати");

$proc = new ForgotPasswordProcessor();
$form->setProcessor($proc);

$proc->process($form);

if ($proc->getStatus() != IFormProcessor::STATUS_NOT_PROCESSED) {
    Session::SetAlert($proc->getMessage());
    header("Location: forgot_password.php");
    exit;
}

$page->startRender();

$page->setTitle("Забравена парола");

echo "<div class='column'>";

    echo "<h1 class='Caption'>";
    echo $page->getTitle();
    echo "</h1>";

    echo "<div class='panel'>";

        echo tr("Въведете Вашият email aдрес от момента на регистрация в сайта и натиснете бутон 'Изпрати'");
        echo "<BR>";
        echo tr("Вашата нова парола ще бъде изпратена на този адрес.");

        echo "<BR><BR>";

        $frend->render();

    echo "</div>";

echo "</div>";

$page->finishRender();
?>
