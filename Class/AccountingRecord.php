<?php

class AccountingRecord
{
    public $id;
    public $datetime;
    public $price;
    public $paid_cash;
    public $paid_individual;
    public $status;
    public $name;
    public $admin;

    public function __construct($accounting)
    {
        $this->id = $accounting['accounting_id'];
        $this->user_id = $accounting['user_id'];
        $this->datetime = $accounting['datetime'];
        $this->price = (int) $accounting['price'];
        $this->paid_cash = (int) $accounting['paid_cash'];
        $this->paid_individual = $this->price - $this->paid_cash;
        if ($this->datetime == null || strtotime($this->datetime) == 0) {
            $this->status = "未納";
        } else {
            $this->status = "既納";
        }
    }

    public function get_status()
    {
        return $this->status;
    }

    public function paid()
    {
        if ($this->datetime == null || strtotime($this->datetime) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function get_submission_time()
    {
        if ($this->datetime == null || strtotime($this->datetime) == 0) {
            return '';
        } else {
            return date('Y/m/d H:i:s', strtotime($this->datetime));
        }
    }

    public function get_price()
    {
        return "￥" . number_format($this->price);
    }

    public function get_paid_cash()
    {
        if ($this->datetime == null) {
            return '';
        } else {
            return "￥" . number_format($this->paid_cash);
        }
    }

    public function get_paid_individual()
    {
        if ($this->datetime == null) {
            return '';
        } else {
            return "￥" . number_format($this->paid_individual);
        }
    }
}
