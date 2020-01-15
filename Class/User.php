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
}

class Fee
{
    public $id;
    public $datetime;
    public $price;
    public $paid_cash;
    public $status;
    public $name;
    public $deadline;
    public $admin;

    public function __construct($fee)
    {
        $this->id = $fee['id'];
        $this->datetime = $fee['datetime'];
        $this->price = $fee['price'];
        $this->paid_cash = $fee['paid_cash'];
        $this->status = $fee['status'];

        // require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
        // $mysqli = new mysqli($host, $username, $password, $dbname);
        // if ($mysqli->connect_error) {
        //     error_log($mysqli->connect_error);
        //     exit;
        // }

        // $query = "SELECT * FROM fee_list WHERE id = $this->id";
        // $result = $mysqli->query($query);
        // if (!$result) {
        //     print('Query Failed : ' . $mysqli->error);
        //     $mysqli->close();
        //     exit();
        // }
        // $row = $result->fetch_assoc();
        // $this->name = $row['name'];
        // $this->deadline = $row['deadline'];
        // $this->deadline = date('Y/m/d', strtotime($this->deadline));
        // $this->admin = $row['admin'];
    }
}
