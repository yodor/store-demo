<?php
include_once ("lib/auth/UserAuthenticator.php");
include_once ("class/beans/OrdersBean.php");

class OrderOwnerAuthenticator extends UserAuthenticator
{
    public function checkAuthState($skip_cookie_check, $user_data)
    {
        $is_owner = false;
        
        if (parent::checkAuthState($skip_cookie_check)) {
            $logged_userID = $_SESSION[CONTEXT_USER]["id"];
            
            $orders = new OrdersBean();
            $orderID = (int)$user_data["orderID"];
            
            $order_userID = (int)$orders->fieldValue($orderID, "userID");
            
            if ($logged_userID == $order_userID) $is_owner = true;
            
            
        }
        return $is_owner;
        
    }
}

?>
