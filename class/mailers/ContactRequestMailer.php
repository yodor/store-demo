<?php
include_once("mailers/Mailer.php");

class ContactRequestMailer extends Mailer
{

    public function __construct($crID)
    {

        $config = ConfigBean::Factory();
        $config->setSection("global");

        $this->to = $config->getValue("admin_email");

        $subject = "Contact Request - crID: $crID";

        $this->subject = $subject;

        $message = "crID: $crID\r\n";
        $message .= "<a href='" . SITE_URL . LOCAL . "admin/contact_requests/list.php'>List Contact Requests</a>";

        $this->body = $this->templateMessage($message);

    }

}

?>