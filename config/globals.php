<?php

function formatPrice($price, string $currency_symbol="лв", bool $symbol_front=false)
{
    $format = "%0.2f";

    if ($symbol_front) {
        $format = $currency_symbol." ".$format;
    }
    else {
        $format = $format." ".$currency_symbol;
    }

    return sprintf($format, $price);
}

?>