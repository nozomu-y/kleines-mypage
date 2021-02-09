<?php

class Individual_Accounting
{
    public $id;
    public $date;
    public $name;
    public $price;
    public $accounting_id;

    public function __construct($individual_accounting)
    {
        $this->id = $individual_accounting['individual_accounting_id'];
        $this->date = $individual_accounting['datetime'];
        $this->name = $individual_accounting['name'];
        $this->price = $individual_accounting['price'];
        $this->accounting_id = $individual_accounting['accounting_id'];
    }

    public function get_price()
    {
        return "￥" . number_format($this->price);
    }

    public function get_date()
    {
        return date('Y/m/d', strtotime($this->date));
    }
}
