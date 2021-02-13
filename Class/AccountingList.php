<?php

class AccountingList
{
    public $accounting_id;
    public $name;
    public $deadline;
    public $admin;

    public function __construct($accounting)
    {
        if (gettype($accounting) == 'array') {
            $this->accounting_id = $accounting['accounting_id'];
            $this->name = $accounting['name'];
            $this->deadline = $accounting['deadline'];
            $this->admin = $accounting['admin'];
        } else if (gettype($accounting) == 'string') {
            require __DIR__ . '/../Common/dbconnect.php';
            $query = "SELECT * FROM accounting_lists WHERE accounting_id=$accounting";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $row = $result->fetch_assoc();
            $this->accounting_id = $row['accounting_id'];
            $this->name = $row['name'];
            $this->deadline = $row['deadline'];
            $this->admin = $row['admin'];
        } else {
            throw new Exception("Input not expected.");
        }
    }

    public function get_price()
    {
        return "￥" . number_format($this->price);
    }

    public function get_deadline()
    {
        return date('Y/m/d', strtotime($this->deadline));
    }
}
