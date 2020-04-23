<?php
include_once("lib/forms/processors/FormProcessor.php");
include_once("lib/forms/InputForm.php");
include_once("lib/beans/UsersBean.php");
include_once("lib/auth/Authenticator.php");
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

        $email = $form->getField("email")->getValue();
        $users = new UsersBean();

        if ($this->editID < 1) {

            $email_exists = $users->emailExists($email);

            if ($email_exists) {
                $form->getField("email")->setError("Този имейл адрес е вече регистриран.");
                throw new Exception(tr("Вие избрахте регистрирация, но имейлът е вече регистриран при нас. Ако сте регистриран клиент изберете вход за регистриран потребител."));
            }

            $urow = array();
            $urow["fullname"] = $form->getField("fullname")->getValue();
            $urow["email"] = strtolower(trim($form->getField("email")->getValue()));
            $urow["phone"] = $form->getField("phone")->getValue();

            $password = Authenticator::RandomToken(8);

            $urow["password"] = md5($password);
            $urow["is_confirmed"] = 1;
            $urow["date_signup"] = DBDriver::Factory()->dateTime();


            $userID = $users->insert($urow);
            if ($userID < 1) {
                throw new Exception("Грешка в системата за регистрация. Моля опитайте по късно. " . $users->getDB()->getError());
            }

            $mailer = new RegisterCustomerPasswordMailer($userID, $password, $urow["fullname"], $urow["email"]);
            $mailer->send();

            $context_data = array(UserAuthenticator::DATA_EMAIL => $urow["email"], UserAuthenticator::DATA_FULLNAME => $urow["fullname"]);
            $auth = new UserAuthenticator();
            $auth->store($userID, $context_data);
        }
        else {

            //current client data
            $existing_data = $users->getByID($this->editID);
            $existing_email = $existing_data["email"];

            $email = strtolower(trim($form->getField("email")->getValue()));

            if (strcmp($email, $existing_email) != 0) {
                //check if email exists and is for different ID
                $existingID = $users->email2id($email);
                if ((int)$existingID != (int)$this->editID) {
                    throw new Exception("Този email адрес е вече регистриран");
                }
            }


            $urow = array();
            $urow["fullname"] = $form->getField("fullname")->getValue();
            $urow["email"] = $email;
            $urow["phone"] = $form->getField("phone")->getValue();

            if (!$users->update($this->editID, $urow)) throw new Exception("Грешка при обновяване на профила: " . $users->getDB()->getError());


        }
    }
}

?>
