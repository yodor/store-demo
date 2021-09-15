<?php
include_once("session.php");

include_once("store/pages/AccountPage.php");
include_once("store/forms/RegisterClientInputForm.php");
include_once("store/forms/processors/RegisterClientFormProcessor.php");

$page = new AccountPage(FALSE);

$page->addJS(STORE_LOCAL."/js/RegisterForm.js");
$page->addJS(SPARK_LOCAL."/js/md5.js");

$form = new RegisterClientInputForm();


$frender = new FormRenderer($form);
$frender->setAttribute("autocomplete", "off");

$frender->setLayout(FormRenderer::FIELD_VBOX);

$frender->getSubmitButton()->setContents("Регистрация");

$proc = new RegisterClientFormProcessor();

$proc->process($form);

if ($proc->getStatus() == IFormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}
else if ($proc->getStatus() == IFormProcessor::STATUS_OK) {

    header("Location: register_complete.php");
    exit;
}

$page->startRender();
$page->setTitle(tr("Нова регистрация"));

echo "<div class='column register'>"; //register

    echo "<h1 class='Caption'>" . tr("Нова регистрация") . "</h1>";

    echo "<div class='panel'>";
        echo "<div align=center>";
            echo "<div class='register_component'>";
            $frender->render();
            echo "</div>";//register_component
        echo "</div>"; //align=center
    echo "</div>"; //panel

echo "</div>"; //column

?>
<script type='text/javascript'>
            onPageLoad(function () {
                var register_form = new RegisterForm();
                register_form.setName("<?php echo $form->getName();?>");
                register_form.initialize();
            });
</script>
<?php

$page->finishRender();
?>
