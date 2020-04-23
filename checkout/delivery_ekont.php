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

    public function setEditID($editID)
    {
        $this->editID = (int)$editID;
    }

    public function processImpl(InputForm $form)
    {
        parent::processImpl($form);

        if ($this->getStatus() != FormProcessor::STATUS_OK) return;

        $page = HTMLPage::Instance();

        $dbt = new DBTransactor();
        $dbt->appendValue("userID", $page->getUserID());

        $dbt->transactValues($form);

        $dbt->processBean($this->bean, $this->editID);

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

$frend = new FormRenderer();
$frend->setName("EkontOffice");

$frend->setForm($form);

$proc->processForm($form, "office");


$page->startRender();


$page->setPreferredTitle(tr("Избор на Еконт офис"));

$page->drawCartItems();


echo "<div class='item ekont_office $empty'>";

echo "<div class='caption'>" . tr("Избран офис на Еконт") . "</div>";

$frend->startRender();
echo "<div class='selected_office'>";
$frend->renderField($form->getField("office"));
echo "</div>";
$frend->finishRender();


echo "<a class='DefaultButton' href='javascript:changeEkontOffice();'>" . tr("Изберете друг офис") . "</a>";

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


echo "<div class='navigation'>";

echo "<div class='slot left'>";
echo "<a href='confirm.php'>";
echo "<img src='" . SITE_ROOT . "images/cart_edit.png'>";
echo "<div class='DefaultButton checkout_button' >" . tr("Назад") . "</div>";
echo "</a>";
echo "</div>";

echo "<div class='slot center'>";
echo "</div>";

echo "<div class='slot right'>";
echo "<a href='javascript:document.forms.EkontOffice.submit();'>";
echo "<img src='" . SITE_ROOT . "images/cart_checkout.png'>";
echo "<div class='DefaultButton checkout_button'  >" . tr("Продължи") . "</div>";
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

        $(".item.ekont_office .TextArea[name='office']").val(text);

        showAlert("Избрахте офис на 'Еконт'<br>" + text);

        $(".item.ekont_office").removeClass("empty");

//     $(".selected_office").css("display", "block");
    }

    function changeEkontOffice() {
        $(".item.ekont_office").addClass("empty");
        //$(".selected_office").css("display", "none");
    }
</script>


<?php
$page->finishRender();
?>
