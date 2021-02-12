<?php

class IndividualAccountingList
{
    public $list_id;
    public $name;
    public $datetime;

    public function __construct($accounting)
    {
        if (gettype($accounting) == 'array') {
            $this->list_id = $accounting['list_id'];
            $this->name = $accounting['name'];
            $this->datetime = $accounting['datetime'];
        } else if (gettype($accounting) == 'string') {
            require __DIR__ . '/../Common/dbconnect.php';
            $query = "SELECT * FROM individual_accounting_lists WHERE list_id=$accounting";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $row = $result->fetch_assoc();
            $this->list_id = $row['list_id'];
            $this->name = $row['name'];
            $this->datetime = $row['datetime'];
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
