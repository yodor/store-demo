<?php
include_once("forms/processors/FormProcessor.php");
include_once("forms/InputForm.php");
include_once("beans/UsersBean.php");
include_once("auth/Authenticator.php");
include_once("class/mailers/RegisterCustomerPasswordMailer.php");

class RegisterClientFormProcessor extends FormProcessor
{

    protected $editID = -1;

    public function setEditID($editID)
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

            $password = Authenticator::RandomToken(8);

            $urow["password"] = md5($password);
            $urow["is_confirmed"] = 1;
            $urow["date_signup"] = DBDriver::Factory()->dateTime();

            $auth = new UserAuthenticator();

            $context = $auth->register($urow);

            $mailer = new RegisterCustomerPasswordMailer($context->getID(), $password, $urow["fullname"], $urow["email"]);
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
                    throw new Exception("Този email адрес е вече регистриран");
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
