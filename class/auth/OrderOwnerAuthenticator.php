<?php
include_once("lib/auth/UserAuthenticator.php");
include_once("class/beans/OrdersBean.php");

class OrderOwnerAuthenticator extends UserAuthenticator
{
    public function validate(bool $skip_cookie_check = false, array $user_data = NULL)
    {
        $is_owner = false;


        $context_data = parent::data($skip_cookie_check);

        if ($context_data) {

            $logged_userID = $context_data[Authenticator::CONTEXT_ID];

            $orders = new OrdersBean();
            $orderID = (int)$user_data[$orders->key()];

            $order_userID = (int)$orders->fieldValue($orderID, "userID");

            if ($logged_userID == $order_userID) $is_owner = true;

        }
        return $is_owner;

    }
}

?>
