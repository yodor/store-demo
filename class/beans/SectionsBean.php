<?php
include_once ("lib/beans/OrderedDataBean.php");


class SectionsBean extends OrderedDataBean
{

    protected $createString = "CREATE TABLE `genders` (
 `secID` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `section_title` varchar(32) NOT NULL,
 PRIMARY KEY (`secID`),
 UNIQUE KEY `section_title` (`section_title`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8";
    
    public function __construct() 
    {
	parent::__construct("sections");
    }

}

?>
