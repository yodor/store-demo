<?php
include_once("lib/auth/UserAuthenticator.php");
include_once("class/beans/OrdersBean.php");

class OrderOwnerAuthenticator extends UserAuthenticator
{
<<<<<<< HEAD
    public function authorize(array $user_data = NULL)
=======
    public static function checkAuthState($skip_cookie_check, $user_data)
>>>>>>> origin/master
    {
        debug("OrderOwnerAuthenticator::checkAuthState()");
        $is_owner = false;

        $context = parent::authorize($user_data);

        if ($context != NULL) {

            $logged_userID = $context->getID();

            $orders = new OrdersBean();
            $orderID = (int)$user_data[$orders->key()];

            $order_userID = (int)$orders->fieldValue($orderID, "userID");

            if ($logged_userID == $order_userID) $is_owner = true;
<<<<<<< HEAD

=======
            
            debug("OrderOwnerAuthenticator::checkAuthState() Authenticated as owner");
            
>>>>>>> origin/master
        }
        return $is_owner;

    }
}

?>
