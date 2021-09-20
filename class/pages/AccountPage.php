<?php
include_once ("store/pages/AccountPageBase.php");

class AccountPage extends AccountPageBase
{
    public function __construct($authorized_access = TRUE)
    {
        parent::__construct($authorized_access);

    }

}
?>