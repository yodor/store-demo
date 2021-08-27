<?php

class PriceInfo {
    protected $sell_price = 0.0;
    protected $price = 0.0;
    protected $stock_amount = 0;

    public function __construct(float $sell_price, float $price, int $stock_amount)
    {
        $this->sell_price = $sell_price;
        $this->price = $price;
        $this->stock_amount = $stock_amount;
    }
    public function getSellPrice() : float
    {
        return $this->sell_price;
    }
    public function getPrice() : float
    {
        return $this->price;
    }
    public function getStockAmount() : int
    {
        return $this->stock_amount;
    }

}
?>