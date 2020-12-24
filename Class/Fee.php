<?php

class Fee
{
    public $id;
    public $datetime;
    public $price;
    public $paid_cash;
    public $paid_individual;
    public $status;
    public $name;
    public $deadline;
    public $admin;

    public function __construct($fee)
    {
        $this->id = $fee['id'];
        $this->datetime = $fee['datetime'];
        $this->price = (int) $fee['price'];
        $this->paid_cash = (int) $fee['paid_cash'];
        $this->paid_individual = $this->price - $this->paid_cash;
        $this->status = $fee['status'];

        require __DIR__.'/../Common/dbconnect.php';

        $query = "SELECT * FROM fee_list WHERE id = $this->id";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $row = $result->fetch_assoc();
        $this->name = $row['name'];
        $this->deadline = $row['deadline'];
        $this->admin = $row['admin'];
    }

    public function get_deadline()
    {
        return date('Y/m/d', strtotime($this->deadline));
    }

    public function get_status()
    {
        if ($this->datetime == null || strtotime($this->datetime) == 0) {
            return "未納";
        } else {
            return "既納";
        }
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