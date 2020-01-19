<?php

class User
{
    public $id;
    public $email;
    public $password;
    public $last_name;
    public $first_name;
    public $kana;
    public $grade;
    public $part;
    public $token;
    public $validation_time;
    public $login_failure;
    public $admin;
    public $status;
    public $name;
    public $delinquent;
    public $individual_accounting_total;

    public function __construct($user)
    {
        $this->id = $user['id'];
        $this->email = $user['email'];
        $this->password = $user['password'];
        $this->last_name = $user['last_name'];
        $this->first_name = $user['first_name'];
        $this->kana = $user['kana'];
        $this->grade = $user['grade'];
        $this->part = $user['part'];
        $this->token = $user['token'];
        $this->validation_time = $user['validation_time'];
        $this->login_failure = $user['login_failure'];
        $this->admin = (int) $user['admin'];
        $this->status = (int) $user['status'];
        $this->name = $user['last_name'] . $user['first_name'];

        require('/home/chorkleines/www/member/mypage/Core/config.php');

        $mysqli = new mysqli($host, $username, $password, $dbname);
        if ($mysqli->connect_error) {
            error_log($mysqli->connect_error);
            exit;
        }
        $query = "SELECT * FROM fee_record_$this->id WHERE datetime IS NULL";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $this->delinquent = 0;
        while ($row = $result->fetch_assoc()) {
            $this->delinquent += $row['price'];
        }
        $query = "SELECT * FROM individual_accounting_$this->id";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $this->individual_accounting_total = 0;
        while ($row = $result->fetch_assoc()) {
            $this->individual_accounting_total += $row['price'];
        }
    }

    public function get_name()
    {
        return $this->grade . $this->part . ' ' . $this->name;
    }

    public function get_part()
    {
        if ($this->part == 'S') {
            return 'Soprano';
        } else if ($this->part == 'A') {
            return 'Alto';
        } else if ($this->part == 'T') {
            return 'Tenor';
        } else if ($this->part == 'B') {
            return 'Bass';
        }
    }

    public function get_password()
    {
        if ($this->password != '') {
            return '**********';
        } else {
            return '';
        }
    }

    public function get_admin()
    {
        if ($this->admin == 1) {
            return "マスター権限";
        } else if ($this->admin == 2) {
            return "アカウント管理";
        } else if ($this->admin == 3) {
            return "会計システム";
        } else if ($this->admin == 5) {
            return "合宿会計システム";
        } else {
            return '';
        }
    }
    public function get_admin_en()
    {
        if ($this->admin == 1) {
            return "master";
        } else if ($this->admin == 2) {
            return "account management";
        } else if ($this->admin == 3) {
            return "accounting management";
        } else if ($this->admin == 5) {
            return "camp accounting management";
        } else {
            return '';
        }
    }

    public function get_status()
    {
        if ($this->status == 0) {
            return "在団";
        } else if ($this->status == 1) {
            return "休団";
        } else {
            return "退団";
        }
    }

    public function get_delinquent()
    {
        return '￥' . number_format($this->delinquent);
    }
    public function get_individual_accounting_total()
    {
        return '￥' . number_format($this->individual_accounting_total);
    }
}

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

        require('/home/chorkleines/www/member/mypage/Core/config.php');

        $mysqli = new mysqli($host, $username, $password, $dbname);
        if ($mysqli->connect_error) {
            error_log($mysqli->connect_error);
            exit;
        }

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
        if ($this->datetime == NULL || strtotime($this->datetime) == 0) {
            return "未納";
        } else {
            return "既納";
        }
    }

    public function paid()
    {
        if ($this->datetime == NULL || strtotime($this->datetime) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function get_submission_time()
    {
        if ($this->datetime == NULL || strtotime($this->datetime) == 0) {
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
        if ($this->datetime == NULL) {
            return '';
        } else {
            return "￥" . number_format($this->paid_cash);
        }
    }

    public function get_paid_individual()
    {
        if ($this->datetime == NULL) {
            return '';
        } else {
            return "￥" . number_format($this->paid_individual);
        }
    }
}

class Individual_Accounting
{
    public $id;
    public $date;
    public $name;
    public $price;

    public function __construct($individual_accounting)
    {
        $this->id = $individual_accounting['id'];
        $this->date = $individual_accounting['date'];
        $this->name = $individual_accounting['name'];
        $this->price = $individual_accounting['price'];
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

        require('/home/chorkleines/www/member/mypage/Core/config.php');

        $mysqli = new mysqli($host, $username, $password, $dbname);
        if ($mysqli->connect_error) {
            error_log($mysqli->connect_error);
            exit;
        }

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
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $row_cnt = $result->num_rows;
            if ($row_cnt != 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['datetime'] == NULL) {
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
