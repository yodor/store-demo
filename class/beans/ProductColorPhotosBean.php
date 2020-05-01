<?php
include_once("lib/beans/OrderedDataBean.php");

class ProductColorPhotosBean extends OrderedDataBean
{
    protected $createString = "CREATE TABLE `product_color_photos` (
 `pclrpID` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `photo` longblob NOT NULL,
 `pclrID` int(11) unsigned NOT NULL,
 `date_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 `position` int(11) NOT NULL DEFAULT '0',
 `caption` varchar(255) DEFAULT NULL,
 PRIMARY KEY (`pclrpID`),
 KEY `pclrID` (`pclrID`),
 CONSTRAINT `product_color_photos_ibfk_1` FOREIGN KEY (`pclrID`) REFERENCES `product_colors` (`pclrID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    public function __construct()
    {
        parent::__construct("product_color_photos");
    }

    //return pclrpID
    public function getFirstPhotoID($pclrID)
    {

        $pclrpID = -1;

        $this->startIterator(" WHERE pclrID='$pclrID' ORDER BY position ASC LIMIT 1 ", $this->key());
        $photo_row = array();
        if ($this->fetchNext($photo_row)) {
            $pclrpID = $photo_row[$this->key()];

        }

        return $pclrpID;

    }


}

?>
