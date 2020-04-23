<?php
include_once("lib/auth/UserAuthenticator.php");
include_once("class/beans/OrdersBean.php");

class OrderOwnerAuthenticator extends UserAuthenticator
{
    protected function validate(AuthContext $context, string $name, array $user_data = NULL)
    {
        $is_owner = false;

        $context_data = parent::validate($context, $name, $user_data);

        if ($context_data != NULL) {

            $logged_userID = $context->getID();

            $orders = new OrdersBean();
            $orderID = (int)$user_data[$orders->key()];

            $order_userID = (int)$orders->fieldValue($orderID, "userID");

            if ($logged_userID == $order_userID) $is_owner = true;

        }
        return $is_owner;

    }
}

?>
