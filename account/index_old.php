<?php
include_once("session.php");
include_once("class/pages/AccountPage.php");
include_once("class/forms/UpdateAddressInputForm.php");
include_once("class/beans/UserDetailsBean.php");

include_once("lib/auth/UserAuthenticator.php");
include_once("class/beans/OrdersBean.php");

class UpdateAddressProcessor extends FormProcessor
{

    protected function processImpl(InputForm $form)
    {
        parent::processImpl($form);

        global $ud, $ub, $userID, $ud_row;

        $userID = $_SESSION[CONTEXT_USER]["id"];

        if ($this->status != IFormProcessor::STATUS_OK) return;

        $form_row = $form->getFieldValues();

        $form_row["userID"] = $userID;

        $db = DBDriver::Get();
        try {
            $db->transaction();

            if (!$ud->update($ud_row[$ud->key()], $form_row)) throw new Exception("Грешка при промяна на адреса: " . $db->getError());
            $db->commit();

            $ud_row = $ud->getByRef("userID", $userID);
            $this->setMessage(tr("Адресът за доставка беше променен успешно"));
        }
        catch (Exception $e) {
            $db->rollback();
            throw $e;

        }
    }
}


$page = new AccountPage();

/*
$is_auth = UserAuthenticator::checkAuthState();
if (!$is_auth) {
//   Session::set("alert", tr("This page is password protected."));
  header("Location: login.php");
  exit;
}*/


$userID = $_SESSION[CONTEXT_USER]["id"];

$ud = new UserDetailsBean();
$ub = new UsersBean();


$ud_row = $ud->getByRef("userID", $userID);
$udID = $ud_row[$ud->key()];
$username = $ub->email($userID);


$form = new UpdateAddressInputForm();
$form->star_required = true;

$frender = new FormRenderer();
$frender->setName("UserDetails");
$frender->getSubmitButton()->setText("Update");

$proc = new UpdateAddressProcessor();

$form->setRenderer($frender);
$form->setProcessor($proc);


$proc->processForm($form);

if ($proc->getStatus() != IFormProcessor::STATUS_NOT_PROCESSED) {
    Session::Set("alert", $proc->getMessage());
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit;
}

$page->startRender();

$page->setPreferredTitle(tr("Профил на клиента"));


echo "<div class='caption'>";
//   echo tr("Профил на клиента");

echo "<div class='section welcome'>" . tr("Welcome back") . ", ";
echo $ud_row["fullname"];
echo "</div>";

echo "<div class='section buttons'>";
StyledButton::DefaultButton()->renderButton("Logout", "logout.php");
echo "</div>";

echo "</div>";


if (isset($_GET["return"])) {
    echo "<div class='panel back_to_order'>";
    $return = DBDriver::Get()->escapeString($_GET["return"]);
    StyledButton::DefaultButton()->renderButton("Обратно към поръчката", SITE_ROOT . "$return");
    echo "</div>";
}


echo "<div class='clear'></div>";

// echo "<div class='caption'>";
// echo tr("Регистриран Адрес за Доставка");
// echo "</div>";

echo "<div class='panel registered_address'>";

echo tr("Може да промените Вашият адрес за доставка от формата по-долу.");
echo "<BR>";
echo tr("Въведете промените и натиснете бутон 'Обнови' за да потвърдите.");
echo "<BR>";


$form->loadBeanData($udID, $ud);

$frender->renderForm($form);

echo "</div>"; //registered_address

echo "<div class='clear'></div>";

echo "<div class='caption'>";
echo tr("История на поръчките");
echo "</div>";

echo "<div class=' order_history'>";

echo "<div class='list'>";
echo "<div class='item head'>";
echo "<div class='cell orderID'>#</div>";
echo "<div class='cell cart_data'>Поръчка</div>";
echo "<div class='cell order_total'>Общо</div>";
echo "<div class='cell order_date'>Дата</div>";
echo "<div class='cell completion_date'>Изпълнена</div>";
echo "</div>";
$orders = new OrdersBean();
$orders->startIterator("WHERE userID='$userID'");
while ($orders->fetchNext($order_row)) {
    echo "<div class='item'>";

    echo "<div class='cell oneline orderID'>";
    echo "OrderID:" . $order_row["orderID"];
    echo "</div>";

    echo "<div class='cell cart_data'>";
    $all_data = str_replace("\r\n", "<BR>", $order_row["cart_data"]);
    echo $all_data;
    // 	  $all_products = explode("\r\n", $order_row["cart_data"]);
    // 	  foreach ($all_products as $idx=>$data) {
    // 		$data = explode("|", $data);
    // 		list($pos, $product_code, $category, $brand, $product_name, $qty, $line_total) = $data;
    // 		echo "<div class='cart_item'>";
    //
    // 		echo "</div>";
    // 	  }
    echo "</div>";

    echo "<div class='cell oneline order_total'>";
    echo printPrice($order_row["order_total"]);
    echo "</div>";

    echo "<div class='cell oneline order_date'>";
    echo dateFormat($order_row["order_date"], false);
    echo "</div>";

    echo "<div class='cell oneline completion_date'>";
    if ($order_row["is_complete"]) {
        echo dateFormat($order_row["completion_date"], false);
    }
    else {
        echo "n/a";
    }
    echo "</div>";

    echo "</div>"; //item
}
echo "</div>";

echo "</div>";

echo "</div>";

$page->finishRender();
?>
