<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");
include_once("class/forms/EkontOfficeInputForm.php");
include_once("class/beans/EkontAddressesBean.php");

class EkontOfficeFormProcessor extends FormProcessor
{
    protected $bean = NULL;
    protected $editID = -1;

    public function setBean(DBTableBean $bean)
    {
        $this->bean = $bean;
    }

    public function setEditID(int $editID)
    {
        $this->editID = (int)$editID;
    }

    public function processImpl(InputForm $form)
    {
        parent::processImpl($form);

        if ($this->getStatus() != FormProcessor::STATUS_OK) return;

        $page = StorePage::Instance();

        $dbt = new BeanTransactor($this->bean, $this->editID);
        $dbt->appendValue("userID", $page->getUserID());

        $dbt->processForm($form);

        //will do insert or update
        $dbt->processBean();

        header("Location: confirm.php");
        exit;
    }
}

$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$bean = new EkontAddressesBean();
$proc = new EkontOfficeFormProcessor();
$proc->setBean($bean);

$form = new EkontOfficeInputForm();
$form->setName("EkontOffice");

$empty = "";
$eorow = $bean->findFieldValue("userID", $page->getUserID());
if (!$eorow) {
    $empty = "empty";
}
else {
    $editID = (int)$eorow[$bean->key()];
    $proc->setEditID($editID);
    $form->loadBeanData($editID, $bean);
}

$frend = new FormRenderer($form);

$proc->process($form);

$page->startRender();

$page->setPreferredTitle(tr("Избор на Еконт офис"));

$page->drawCartItems();

echo "<div class='item ekont_office $empty'>";

echo "<div class='caption'>" . tr("Избран офис на Еконт") . "</div>";

echo "<div class='selected_office'>";
echo str_replace("\r", "<br>", $form->getInput("office")->getValue());
echo "</div>";

$frend->startRender();
$frend->renderInputs();//($form->getInput("office"));
$frend->renderSubmitValue();
$frend->finishRender();

echo "<a class='ColorButton' href='javascript:changeEkontOffice();'>" . tr("Изберете друг офис") . "</a>";

echo "</div>";//ekont_office

echo "<div class='item ekont_locator'>";

echo "<div class='caption'>";
echo tr("Изберете офис на Еконт за доставка");
echo "</div>";
?>
<iframe id="ekont_frame" scrolling="no" frameborder="0" style="border: medium none; width: 800px; height: 450px;"
        allowtransparency="true" src="http://www.bgmaps.com/templates/econt?office_type=all&shop_url=<?php
echo SITE_URL; ?>"></iframe>
<?php
echo "</div>"; //ekont_locator

// $back_url = Session::get("checkout.navigation.back", $page->getPageURL());

echo "<div class='navigation'>";

echo "<div class='slot left'>";
echo "<a href='delivery.php'>";
echo "<img src='" . LOCAL . "images/cart_edit.png'>";
echo "<div class='ColorButton checkout_button' >" . tr("Назад") . "</div>";
echo "</a>";
echo "</div>";

echo "<div class='slot center'>";
echo "</div>";

echo "<div class='slot right'>";
echo "<a href='javascript:document.forms.EkontOffice.submit();'>";
echo "<img src='" . LOCAL . "images/cart_checkout.png'>";
echo "<div class='ColorButton checkout_button'  >" . tr("Продължи") . "</div>";
echo "</a>";
echo "</div>";

echo "</div>";

?>
<script type='text/javascript'>

    window.addEventListener("message", receiveMessage, false);

    function receiveMessage(event) {

        if ((event.origin === "http://www.bgmaps.com") || (event.origin === "https://www.bgmaps.com")) {

        } else {

            console.log("event.origin missmatch");
            console.log(event.origin);

            return;
        }

        // ...
        console.log(event.origin);
        console.log(event.data);

        var office = event.data.split("||");
        var text = office[0] + " " + office[1] + "\r\n";
        text += office[3] + "\r\n";
        text += office[2] + "\r\n";

        $('.item.ekont_office .selected_office').html(text.replace("\r\n", "<BR>"));

        $('.item.ekont_office .TextArea TEXTAREA[name="office"]').html(text);

        showAlert("Избрахте офис на 'Еконт'<br>" + text);

        $(".item.ekont_office").removeClass("empty");
        $(".item.ekont_locator").css("display", "none");

//     $(".selected_office").css("display", "block");
    }

    function changeEkontOffice() {
        $(".item.ekont_office").addClass("empty");
        //$(".selected_office").css("display", "none");
        $(".item.ekont_locator").css("display", "block");
    }

    onPageLoad(function(){
        $(".item.ekont_locator").css("display", "none");

    });
</script>


<?php
$page->finishRender();
?>
