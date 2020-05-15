<?php
include_once("beans/DBTableBean.php");

class OrdersBean extends DBTableBean
{
    const STATUS_PROCESSING = "Processing";
    const STATUS_SENT = "Sent";
    const STATUS_COMPLETED = "Completed";
    const STATUS_CANCELED = "Canceled";

    protected $createString = "CREATE TABLE `orders` (
 `orderID` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `total` decimal(10,2) NOT NULL,
 `delivery_price` decimal(10,2) NOT NULL,
 `delivery_type` enum('UserAddress','EkontOffice','Other') NOT NULL DEFAULT 'UserAddress',
 `delivery_address` varchar(512) NOT NULL,
 `note` varchar(512) NOT NULL,
 `require_invoice` tinyint(1) NOT NULL DEFAULT 0,
 `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
 `completion_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 `status` enum('Processing','Sent','Completed','Canceled') NOT NULL DEFAULT 'Processing',
 `userID` int(11) unsigned NOT NULL,
 PRIMARY KEY (`orderID`),
 KEY `userID` (`userID`),
 CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8
";

    public function __construct()
    {
        parent::__construct("orders");
    }

}

?>
