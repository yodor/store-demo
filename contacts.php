<?php
include_once("session.php");
include_once("class/pages/StorePage.php");
include_once("components/BeanFormEditor.php");
include_once("store/beans/ContactRequestsBean.php");
include_once("store/forms/ContactRequestForm.php");
include_once("store/mailers/ContactRequestMailer.php");
include_once("store/beans/ContactAddressesBean.php");
include_once("store/forms/processors/ContactRequestProcessor.php");

$page = new StorePage();
$page->setTitle(tr("Контакти"));
//$page->startRender();
//$page->setTitle("Контакти");
//



$v = new BeanFormEditor(new ContactRequestsBean(), new ContactRequestForm());
$v->setMessage("Заявката Ви е приета", BeanFormEditor::MESSAGE_ADD);
$v->setProcessor(new ContactRequestProcessor());

$v->setRedirectURL($page->getURL());

$v->processInput();



$proc = $v->getProcessor();

$page->startRender();


echo "<h1 class='Caption'>" . tr($page->getTitle()) . "</h1>";

echo "<div class='columns'>";

    echo "<div class='column map'>";

        echo "<a name='map'></a>";
        echo "<div class='panel map'>";
        echo "<iframe id=google_map src=''  frameborder='0' allowfullscreen='' aria-hidden='false' tabindex='0'></iframe>";
        echo "</div>";

    echo "</div>"; //column

    echo "<div class='column addresses'>";

        $cabean = new ContactAddressesBean();
        $qry = $cabean->queryFull();
        $qry->select->order_by = " position ASC ";
        $num = $qry->exec();
        while ($carow = $qry->next()) {

            echo "<div class='details' pos='{$carow["position"]}' onClick='updateMap(this);' map-url='{$carow["map_url"]}'>";

            echo "<div class='item city' >";
            echo $carow["city"];
            echo "</div>";

            echo "<div class='item address'>";
            echo $carow["address"];
            echo "</div>";

            echo "<div class='item email'>";
            $email = strip_tags($carow["email"]);
            if (strlen($email) > 0) {
                echo "Email: <a href='mailto:$email'>$email</a>";
            }
            echo "</div>";

            echo "<div class='item phone'>";
            $phone = strip_tags($carow["phone"]);
            if (strlen($phone) > 0) {
                echo "Телефон: <a href='tel:$phone'>$phone</a>";
            }
            echo "</div>";

            echo "</div>";//details

        }

    echo "</div>"; //column

echo "</div>";//columns

echo "<h1 class='Caption'>Изпрати запитване</h1>";

echo "<div class='panel contact_form'>";
$v->render();
echo "</div>"; //panel


if ($proc->getStatus() === IFormProcessor::STATUS_ERROR) {
    ?>
    <script type="text/javascript">
        onPageLoad(function(){

            document.forms.ContactRequestForm.scrollIntoView();
        });
    </script>
    <?php
}
?>
<script type="text/javascript">
    function updateMap(elm)
    {
        $("#google_map").attr("src", $(elm).attr("map-url"));
    }
    onPageLoad(function(){
        let elm = $(".details[pos='1']");
        updateMap(elm);
    });
</script>
<?php
$page->finishRender();
?>
