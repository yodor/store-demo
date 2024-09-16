<?php
include_once("sql/SQLSelect.php");

class ColorPhotosSQL extends SQLSelect
{
    public function __construct()
    {
        parent::__construct();

        $this->fields()->set("pclr.pclrID", "pclr.color", "pclr.color_photo", "pclr.prodID",
                             "pcp.pclrpID", "pcp.photo", "pcp.date_upload", "pcp.position", "pcp.caption");


        $this->from = " product_colors pclr JOIN product_color_photos pcp ON pclr.pclrID = pcp.pclrID ";


    }
    public function createView(string $view_name="color_photos")
    {

        $sql = "CREATE VIEW IF NOT EXISTS $view_name AS ({$this->getSQL()})";
        $db = DBConnections::Open();
        $res = $db->query($sql);
        $db->free($res);

    }
}

?>