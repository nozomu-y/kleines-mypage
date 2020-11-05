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

        require __DIR__.'/../Common/dbconnect.php';

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
        } elseif ($this->part == 'A') {
            return 'Alto';
        } elseif ($this->part == 'T') {
            return 'Tenor';
        } elseif ($this->part == 'B') {
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
        } elseif ($this->admin == 2) {
            return "アカウント管理";
        } elseif ($this->admin == 3) {
            return "会計システム";
        } elseif ($this->admin == 5) {
            return "合宿会計システム";
        } else {
            return '';
        }
    }
    public function get_admin_en()
    {
        if ($this->admin == 1) {
            return "master";
        } elseif ($this->admin == 2) {
            return "account management";
        } elseif ($this->admin == 3) {
            return "accounting management";
        } elseif ($this->admin == 5) {
            return "camp accounting management";
        } else {
            return '';
        }
    }

    public function get_status()
    {
        if ($this->status == 0) {
            return "在団";
        } elseif ($this->status == 1) {
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
