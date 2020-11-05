<?php

class Individual_Accounting
{
    public $id;
    public $date;
    public $name;
    public $price;
    public $fee_id;

    public function __construct($individual_accounting)
    {
        $this->id = $individual_accounting['id'];
        $this->date = $individual_accounting['date'];
        $this->name = $individual_accounting['name'];
        $this->price = $individual_accounting['price'];
        $this->fee_id = $individual_accounting['fee_id'];
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
