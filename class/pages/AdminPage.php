<?php
include_once("store/pages/AdminPageBase.php");

class AdminPage extends AdminPageBase
{

    public function __construct()
    {
        parent::__construct();

        $this->addCSS(STORE_LOCAL . "/css/AdminPage.css");
    }

}

?>