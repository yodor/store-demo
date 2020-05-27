<?php
include_once("sql/SQLSelect.php");

class ProductsSQL extends SQLSelect
{
    public function __construct()
    {
        parent::__construct();

        $this->fields()->set("iav.value AS ia_value", "ca.attribute_name AS ia_name", "pc.catID", "pc.category_name", "pp.ppID", "pi.piID", "pi.size_value", "pi.color", "pi.pclrID", "pi.prodID", "pi.stock_amount", "p.product_name", "p.brand_name", "p.product_summary", "p.keywords", "p.promotion", "p.visible", "p.class_name", "p.section", "p.old_price", "p.insert_date", "p.update_date", "sc.color_code");

        $this->fields()->setExpression("(pclrs.color_photo IS NOT NULL)", "have_chip");
        $this->fields()->setExpression("(SELECT 
        GROUP_CONCAT(DISTINCT(pi1.size_value) SEPARATOR '|') 
        FROM product_inventory pi1 
        WHERE pi1.prodID=pi.prodID AND (pi1.pclrID = pi.pclrID OR pi.pclrID IS NULL) 
        GROUP BY pi.pclrID )", "size_values");

        $this->fields()->setExpression("(SELECT 
        GROUP_CONCAT(DISTINCT(pi2.color) SEPARATOR '|') 
        FROM product_inventory pi2 
        WHERE pi2.prodID=pi.prodID 
        ORDER BY pclrID ASC )", "colors");

        $this->fields()->setExpression("(SELECT 
        GROUP_CONCAT(DISTINCT(pi3.pclrID) SEPARATOR '|') 
        FROM product_inventory pi3 
        WHERE pi3.prodID=pi.prodID 
        ORDER BY pclrID ASC )", "color_ids");

        $this->fields()->setExpression("(SELECT 
        GROUP_CONCAT(DISTINCT(CONCAT(ca.attribute_name,':', cast(iav.value as char))) SEPARATOR '|') 
        FROM inventory_attribute_values iav JOIN class_attributes ca ON ca.caID = iav.caID 
        WHERE iav.piID = pi.piID)", "inventory_attributes");

        $this->fields()->setExpression("(SELECT 
        pclrpID 
        FROM product_color_photos pcp 
        WHERE pcp.pclrID=pi.pclrID 
        ORDER BY position ASC LIMIT 1)", "pclrpID");

        $this->fields()->setExpression("pi.price - (pi.price * (coalesce(sp.discount_percent,0)) / 100.0)", "sell_price");
        $this->fields()->setExpression("(SELECT min(pi4.price - (pi4.price * (coalesce(sp.discount_percent,0)) / 100.0)) FROM product_inventory pi4 WHERE pi4.prodID=pi.prodID )", "price_min");
        $this->fields()->setExpression("(SELECT max(pi5.price - (pi5.price * (coalesce(sp.discount_percent,0)) / 100.0)) FROM product_inventory pi5 WHERE pi5.prodID=pi.prodID )", "price_max");

        $this->fields()->setExpression("coalesce(sp.discount_percent,0)", "discount_percent");

        $this->from = " product_inventory pi 

JOIN products p ON (p.prodID = pi.prodID AND p.visible=1) 
JOIN product_categories pc ON pc.catID=p.catID 

LEFT JOIN store_promos sp ON (sp.targetID = p.catID AND sp.target='Category' AND sp.start_date <= NOW() AND sp.end_date >= NOW()) 
LEFT JOIN product_colors pclrs ON pclrs.pclrID = pi.pclrID
LEFT JOIN inventory_attribute_values iav ON iav.piID=pi.piID 
LEFT JOIN class_attributes ca ON ca.caID=iav.caID 

LEFT JOIN store_colors sc ON sc.color=pi.color
LEFT JOIN product_photos pp ON pp.prodID=pi.prodID

";

    }
}

?>
