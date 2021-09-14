<?php
include_once("forms/processors/FormProcessor.php");
include_once("forms/InputForm.php");
include_once("beans/UsersBean.php");
include_once("auth/Authenticator.php");
include_once("class/mailers/RegisterCustomerActivationMailer.php");

class RegisterClientFormProcessor extends FormProcessor
{

    protected $editID = -1;

    public function setEditID(int $editID)
    {
        $this->editID = (int)$editID;
    }

    /**
     * @param InputForm $form
     * @throws Exception
     */
    protected function processImpl(InputForm $form)
    {
        parent::processImpl($form);
        if ($this->status != IFormProcessor::STATUS_OK) return;

        $email = $form->getInput("email")->getValue();
        $users = new UsersBean();

        if ($this->editID < 1) {

            $email_exists = $users->emailExists($email);

            if ($email_exists) {

                $form->getInput("email")->setError("Този имейл адрес е вече регистриран.");
                throw new Exception(tr("Вие избрахте регистрирация, но имейлът е вече регистриран при нас. Ако сте регистриран клиент изберете вход за регистриран потребител."));
            }

            $urow = array();
            $urow["fullname"] = $form->getInput("fullname")->getValue();
            $urow["email"] = strtolower(trim($form->getInput("email")->getValue()));
            $urow["phone"] = $form->getInput("phone")->getValue();

            $urow["password"] = $form->getInput("pass")->getValue();


            //automatic registration without activation email
//            $password = Authenticator::RandomToken(8);
//
//            $urow["password"] = md5($password);
//            $urow["confirmed"] = 1;
//            $urow["date_signup"] = DBConnections::Get()->dateTime();
//
//            $auth = new UserAuthenticator();
//
//            $context = $auth->register($urow);
//
//            $mailer = new RegisterCustomerPasswordMailer($context->getID(), $password, $urow["fullname"], $urow["email"]);
//            $mailer->send();
            //

            //registration requires activation email
            $confirm_code = Authenticator::RandomToken(32);
            $urow["confirmed"] = 0;
            $urow["date_signup"] = DBConnections::Get()->dateTime();
            $urow["confirm_code"] = $confirm_code;

            $userID = $users->insert($urow);
            if ($userID<1) throw new Exception(tr("Неуспешна регистрация. Моля опитайте по късно или се свържете с нас."));

            $mailer = new RegisterCustomerActivationMailer($urow["fullname"], $urow["email"], $confirm_code);
            $mailer->send();

        }
        else {

            //current client data
            $existing_data = $users->getByID($this->editID);
            $existing_email = $existing_data["email"];

            $email = strtolower(trim($form->getInput("email")->getValue()));

            if (strcmp($email, $existing_email) != 0) {
                //check if email exists and is for different ID
                $existingID = $users->email2id($email);
                if ((int)$existingID != (int)$this->editID) {
                    throw new Exception("Този e-mail адрес е вече регистриран");
                }
            }

            $urow = array();
            $urow["fullname"] = $form->getInput("fullname")->getValue();
            $urow["email"] = $email;
            $urow["phone"] = $form->getInput("phone")->getValue();

            if (!$users->update($this->editID, $urow)) throw new Exception("Грешка при обновяване на профила: " . $users->getDB()->getError());

        }
    }
}

?>
