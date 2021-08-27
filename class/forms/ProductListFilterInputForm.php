<?php

class ProductListFilterInputForm extends InputForm {

    protected $search_expressions = NULL;
    protected $compare_operators = NULL;

    public function __construct()
    {
        parent::__construct();

        $filter_color = DataInputFactory::Create(DataInputFactory::SELECT, "color", "Color",0);
        $this->addInput($filter_color);


        $filter_size = DataInputFactory::Create(DataInputFactory::SELECT, "size_value", "Size",0);
        $this->addInput($filter_size);

        //$this->updateIterators($filters_select);
    }

    public function updateIterators(SQLSelect $filters_select)
    {

        $color_select = clone $filters_select;

        $color_select->fields()->set("pi.color");
        $color_select->order_by = " pi.color ASC ";
        $color_select->group_by = " pi.color ";

        $color_renderer = $this->getInput("color")->getRenderer();
        $color_renderer->setIterator(new SQLQuery($color_select, "color"));

        $color_renderer->getItemRenderer()->setValueKey("color");
        $color_renderer->getItemRenderer()->setLabelKey("color");
        $color_renderer->na_label = "--- Всички ---";
        $color_renderer->setInputAttribute("onChange", "javascript:applyFilter(this)");


        $size_select = clone $filters_select;

        $size_select->fields()->set("pi.size_value");
        $size_select->order_by = " ssz2.position ASC ";
        $size_select->group_by = " pi.size_value ";

        $size_renderer = $this->getInput("size_value")->getRenderer();
        $size_renderer->setIterator(new SQLQuery($size_select, "size_value"));

        $size_renderer->getItemRenderer()->setValueKey("size_value");
        $size_renderer->getItemRenderer()->setLabelKey("size_value");
        $size_renderer->na_label = "--- Всички ---";
        $size_renderer->setInputAttribute("onChange", "javascript:applyFilter(this)");



    }

    protected function clauseValue(string $key, string $val): SQLClause
    {
        $clause = new SQLClause();

        $val = DBConnections::Get()->escape($val);
        //if (strcmp($key, "keyword") == 0) {
        $clause->setExpression("pi.".$key, "'".$val."'");

        return $clause;

    }

}
?>