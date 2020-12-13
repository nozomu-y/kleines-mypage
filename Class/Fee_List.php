<?php

class Fee_List
{
    public $id;
    public $name;
    public $deadline;
    public $price;
    public $admin;
    public $paid_cnt;
    public $unpaid_cnt;

    public function __construct($fee)
    {
        $this->id = $fee['id'];
        $this->name = $fee['name'];
        $this->deadline = $fee['deadline'];
        $this->price = $fee['price'];
        $this->admin = (int) $fee['admin'];

        require __DIR__.'/../Common/dbconnect.php';

        $query = "SELECT * FROM members ORDER BY id";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $this->paid_cnt = 0;
        $this->unpaid_cnt = 0;
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $query = "SELECT * FROM fee_record_$id WHERE id = $this->id";
            $result_2 = $mysqli->query($query);
            if (!$result_2) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $row_cnt = $result_2->num_rows;
            if ($row_cnt != 0) {
                while ($row_2 = $result_2->fetch_assoc()) {
                    if ($row_2['datetime'] == null) {
                        $this->unpaid_cnt += 1;
                    } else {
                        $this->paid_cnt += 1;
                    }
                }
            }
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

    public function get_paid_ratio()
    {
        if ($this->paid_cnt + $this->unpaid_cnt == 0) {
            return '0.00 %';
        } else {
            return strval(round($this->paid_cnt / ($this->paid_cnt + $this->unpaid_cnt), 3) * 100) . ' %';
        }
    }
}
