<?php
include_once ("store/pages/StorePageBase.php");

class StorePage extends StorePageBase
{
    public function __construct()
    {
        parent::__construct();
        $this->head()->addCSS(LOCAL."/css/store.css");
    }

}
?>
