<?php
include_once("responders/RequestResponder.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductFeaturesBean.php");
include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ClassAttributeValuesBean.php");

class CopyProductRequestResponder extends RequestResponder
{

    protected $item_id = -1;
    protected $bean = NULL;

    public function __construct()
    {
        parent::__construct("copy_product");

        $this->bean = new ProductsBean();

        $this->need_confirm = TRUE;
    }

    public function getItemID()
    {
        return $this->item_id;
    }

    protected function parseParams()
    {
        if (!isset($_GET["item_id"])) throw new Exception("Item ID not passed");
        $this->item_id = (int)$_GET["item_id"];
        $arr = $_GET;
        unset($arr["cmd"]);
        unset($arr["item_id"]);
        $this->cancel_url = queryString($arr);
        $this->cancel_url = $_SERVER['PHP_SELF'] . $this->cancel_url;

    }

    public function createAction($title = "Copy", $href_add = "", $check_code = NULL, $parameters_array = array())
    {
        $parameters = array(new DataParameter("item_id", $this->bean->key()));
        return new Action($title, "?cmd=copy_product$href_add", array_merge($parameters, $parameters_array), $check_code);
    }

    protected function processConfirmation()
    {
        $this->drawConfirmDialog("Потвърдете копиране", "Потвърдете копиране на този продукт включително атрибути и снимки?");
    }

    protected function process()
    {

        $db = DBConnections::Factory();

        try {
            $cbrow = $this->bean->getByID($this->item_id);

            //copy the product
            unset($cbrow["prodID"]);
            $lastID = $this->bean->insert($cbrow, $db);
            if ($lastID < 1) throw new Exception("Unable to copy the product: " . $db->getError());

            //copy attributes
            $pa = new ProductFeaturesBean();
            $qry = $pa->queryField("prodID", $this->item_id);
            $qry->exec();
            while ($parow = $qry->next()) {
                unset($parow[$pa->key()]);
                $parow["prodID"] = $lastID;
                if (!$pa->insert($parow, $db)) throw new Exception("Unable to copy features: " . $db->getError());
            }
            //copy photos
            $pp = new ProductPhotosBean();
            $qry = $pp->queryField("prodID", $this->item_id);
            $qry->exec();
            while ($pprow = $qry->next()) {
                unset($pprow[$pp->key()]);
                $pprow["prodID"] = $lastID;
                $pprow["photo"] = $db->escape($pprow["photo"]);
                // var_dump($pprow);
                $lastppID = $pp->insert($pprow, $db);
                if ($lastppID < 1) throw new Exception("Unable to copy photo: " . $db->getError());
            }

            $ca = new ClassAttributeValuesBean();
            $qry = $ca->queryField("prodID", $this->item_id);
            $qry->exec();
            while ($carow = $qry->next()) {
                unset($carow[$ca->key()]);
                $carow["prodID"] = $lastID;

                $lastcaID = $ca->insert($carow, $db);
                if ($lastcaID < 1) throw new Exception("Unable to copy class attibutes: " . $db->getError());
            }

            $db->commit();
            $success = TRUE;
            Session::SetAlert(tr("Продуктът е копиран успешно.") . tr("Кликнете") . " <a href='add.php?editID=$lastID&catID={$cbrow["catID"]}'>" . tr("тук") . "</a> " . tr("за редактиране"));

            header("Location: {$this->cancel_url}");
            exit;
        }
        catch (Exception $e) {

            $db->rollback();
            throw $e;
        }

    }

}

?>
