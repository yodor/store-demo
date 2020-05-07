<?php
include_once("auth/UserAuthenticator.php");
include_once("class/beans/OrdersBean.php");

class OrderOwnerAuthenticator extends UserAuthenticator
{

    public function authorize(array $user_data = NULL)
    {

        debug("authorize");
        $is_owner = false;

        $context = parent::authorize($user_data);

        if ($context != NULL) {

            $logged_userID = $context->getID();

            $orders = new OrdersBean();
            $orderID = (int)$user_data[$orders->key()];

            $order_userID = (int)$orders->fieldValue($orderID, "userID");

            if ($logged_userID == $order_userID) $is_owner = true;

            debug("Authenticated as owner");
            

        }
        return $is_owner;

    }
}

?>
