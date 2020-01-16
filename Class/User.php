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
    public $name;

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
        $this->name = $user['last_name'] . $user['first_name'];
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
        if ($this->datetime == NULL) {
            return "未納";
        } else {
            return "既納";
        }
    }

    public function get_submission_time()
    {
        return date('Y/m/d H:i:s', strtotime($this->datetime));
    }

    public function get_price()
    {
        return "￥" . $this->price;
    }

    public function get_paid_cash()
    {
        return "￥" . $this->paid_cash;
    }

    public function get_paid_individual()
    {
        return "￥" . $this->paid_individual;
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
        return "￥" . $this->price;
    }

    public function get_date()
    {
        return date('Y/m/d', strtotime($this->date));
    }
}
