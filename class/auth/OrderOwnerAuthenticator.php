<?php
include_once ("lib/auth/UserAuthenticator.php");
include_once ("class/beans/OrdersBean.php");

class OrderOwnerAuthenticator extends UserAuthenticator
{
    public static function checkAuthState($skip_cookie_check, $user_data)
    {
        debug("OrderOwnerAuthenticator::checkAuthState()");
        $is_owner = false;
        
        if (parent::checkAuthState($skip_cookie_check)) {
            $logged_userID = $_SESSION[CONTEXT_USER]["id"];
            
            $orders = new OrdersBean();
            $orderID = (int)$user_data["orderID"];
            
            $order_userID = (int)$orders->fieldValue($orderID, "userID");
            
            if ($logged_userID == $order_userID) $is_owner = true;
            
            debug("OrderOwnerAuthenticator::checkAuthState() Authenticated as owner");
            
        }
        return $is_owner;
        
    }
}

?>
