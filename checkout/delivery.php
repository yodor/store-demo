<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");
include_once("class/forms/DeliveryAddressForm.php");
include_once("class/beans/ClientAddressesBean.php");


class DeliveryAddressProcessor extends FormProcessor
{
       
	public function processImpl(InputForm $form)
	{
		
		
            parent::processImpl($form);
            
            if ($this->getStatus() != FormProcessor::STATUS_OK) return;
            
            $page = SitePage::getInstance();
            $cart = $page->getCart();
            
            $delivery_type = $form->getField("delivery_type")->getValue();
            
            $cart->setDeliveryType($delivery_type[0]);
		

	}
}



$page = new CheckoutPage();
$page->ensureCartItems();
$page->ensureClient();

// $page->getCart()->setDeliveryType(NULL);

$form = new DeliveryAddressForm();


$proc = new DeliveryAddressProcessor();
$frend = new FormRenderer();
$frend->setName("DeliveryAddress");

$frend->setForm($form);
$form->setRenderer($frend);
$form->setProcessor($proc);

$bean = new ClientAddressesBean();



$proc->processForm($form, "Delivery");

if ($proc->getStatus() == FormProcessor::STATUS_NOT_PROCESSED) {
    $delivery_type = $page->getCart()->getDeliveryType();
    $form->getField("delivery_type")->setValue($delivery_type);
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::set("alert", $proc->getMessage());
}
else if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    // 		echo $delivery_type[0]; exit;
    $delivery_type = $page->getCart()->getDeliveryType();
    if (strcmp($delivery_type, Cart::DELIVERY_USERADDRESS)==0) {
        
        $cabrow = $bean->findFieldValue("userID", $page->getUserID());
        if (!$cabrow) {
            header("Location: delivery_address.php");
            exit;
        }
        else {
            header("Location: confirm.php");
            exit;
        }
    
    }
    else if (strcmp($delivery_type, Cart::DELIVERY_EKONTOFFICE)==0) {
        header("Location: delivery_ekont.php");
        exit;
    }
}

$page->beginPage();
$page->setPreferredTitle(tr("Начин на доставка"));

// echo "UserID: ".$page->getUserID();


$page->drawCartItems();

// $page->showShippingInfo();


echo "<div class='delivery_address'>";

  echo "<div class='caption'>".$page->getPreferredTitle()."</div>";
  
  $frend->startRender();
  $frend->renderImpl();
  echo "<input type='hidden' name='Delivery' value='submit'>";
  $frend->finishRender();
  
echo "</div>"; //delivery_details






$back_url = Session::get("checkout.navigation.back", "cart.php");

echo "<div class='navigation'>";

  
  echo "<div class='slot left'>";
    echo "<a href='$back_url'>";
    echo "<img src='".SITE_ROOT."images/cart_edit.png'>";
    echo "<div class='DefaultButton checkout_button' >".tr("Назад")."</div>";
    echo "</a>";
  echo "</div>";

  echo "<div class='slot center'>";
//     echo "<div class='note'>";
//         echo "<i>".tr("Натискайки бутона 'Продължи' Вие се съгласявате с нашите")."&nbsp;"."<a  href='".SITE_ROOT."terms.php'>".tr("Условия за ползване")."</a></i>";
//     echo "</div>";
  echo "</div>";
  
  echo "<div class='slot right'>";
    echo "<a href='javascript:document.forms.DeliveryAddress.submit();'>";
    echo "<img src='".SITE_ROOT."images/cart_checkout.png'>";
    echo "<div class='DefaultButton checkout_button'>".tr("Продължи")."</div>";
    echo "</a>";
  echo "</div>";
  // 
  // 

echo "</div>";

// Session::set("checkout.navigation.back", $page->getPageURL());

$page->finishPage();
?>
