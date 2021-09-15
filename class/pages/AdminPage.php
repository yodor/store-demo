<?php
include_once("pages/SparkAdminPage.php");
include_once("components/MenuBarComponent.php");

include_once("components/renderers/cells/BeanFieldCellRenderer.php");
include_once("components/renderers/cells/CallbackCellRenderer.php");
include_once("components/renderers/cells/BooleanCellRenderer.php");

class AdminPage extends SparkAdminPage
{

    public function __construct()
    {
        parent::__construct();
        MenuItem::$icon_path = STORE_LOCAL . "/images/admin/spark_icons/";

        $this->addMeta("viewport", "width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0");
        $this->addCSS(STORE_LOCAL . "/css/AdminPage.css");
    }

}

?>
