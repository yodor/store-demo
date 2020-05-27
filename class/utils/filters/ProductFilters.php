<?php
include_once("sql/SQLSelect.php");

// class GenderFilter implements IQueryFilter
// {
//   public function getQueryFilter($view=NULL, $value = NULL)
//   {
// 	$sel = NULL;
// 
// 	if ($value) {
// 	  $sel = new SelectQuery();
// 	  $sel->fields = "";
// 	  $sel->from = "";
// 	  if (strcmp($value, "N/A")==0 || strcmp($value, "NULL")==0) {
// 		$sel->where = " relation.gender IS NULL ";
// 	  }
// 	  else {
// 		$sel->where = " LOWER(relation.gender) LIKE LOWER('$value') ";
// 	  }
// 	}
// 	
// 	return $sel;
//   }
// }

class ColorFilter implements IQueryFilter
{
    public function filterSelect($view = NULL, $value = NULL)
    {
        $sel = NULL;

        if ($value) {
            $sel = new SQLSelect();

            if (strcmp($value, "N/A") == 0 || strcmp($value, "NULL") == 0) {
                $sel->where()->add("relation.color","NULL", "IS");
            }
            else {
                $sel->where()->add("relation.color", "'$value'");
            }
        }

        return $sel;
    }
}

class SizingFilter implements IQueryFilter
{

    public function filterSelect($view = NULL, $value = NULL)
    {
        $sel = NULL;

        if ($value) {
            $sel = new SQLSelect();
            $sel->from = "";
            if (strcmp($value, "N/A") == 0 || strcmp($value, "NULL") == 0) {
                $sel->where()->add("relation.size_value", "NULL",  "IS");
            }
            else {
                $sel->where()->add("(relation.size_values LIKE '%$value|%' OR relation.size_values LIKE '%|$value%' OR relation.size_values='$value')", "", "");
            }
        }

        return $sel;
    }
}

class PricingFilter implements IQueryFilter
{
    public function filterSelect($view = NULL, $value = NULL)
    {
        $sel = NULL;

        if ($value) {
            $sel = new SQLSelect();
            $sel->from = "";

            $price_range = explode("|", $value);
            if (count($price_range) == 2) {
                $price_min = (float)$price_range[0];
                $price_max = (float)$price_range[1];

                $sel->where()->add( " (  
		  (relation.price_min >= $price_min AND relation.price_min <= $price_max) 
		  OR 
		  (relation.price_max >= $price_min AND relation.price_max <=  $price_max) 
		  )", "", "");
            }

        }
        return $sel;
    }
}

class InventoryAttributeFilter implements IQueryFilter
{

    public function filterSelect($view = NULL, $value = NULL)
    {
        $sel = NULL;

        if ($value) {

            $sel = new SQLSelect();

            $sel->from = "";

            //?ia=Материал:Пух|Години:10
            $all_filters = explode("|", $value);
            // 	  var_dump($all_filters);

            foreach ($all_filters as $idx => $filter) {
                if (strlen($filter) < 1) continue;

                $name_value = explode(":", $filter);
                if (!is_array($name_value) || count($name_value) != 2) continue;

                $sel_current = new SQLSelect();
                $sel_current->from = "";

                //TODO: handle multiple values inside $filter_value - comma separated
                $ia_name = DBConnections::Get()->escape($name_value[0]);
                $ia_value = DBConnections::Get()->escape($name_value[1]);

                $sel_current->where()->add(" (relation.inventory_attributes LIKE '$ia_name:$ia_value|%' OR relation.inventory_attributes LIKE '%|$ia_name:$ia_value|%' OR relation.inventory_attributes LIKE '%|$ia_name:$ia_value' OR relation.inventory_attributes LIKE '$ia_name:$ia_value') ", "", "");

                $sel = $sel->combineWith($sel_current);
            }

        }
        // 	echo $sel->getSQL();
        return $sel;
    }
}

?>
