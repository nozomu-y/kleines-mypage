<?php

class User
{
    public $id;
    public $last_name;
    public $first_name;
    public $kana;
    public $grade;
    public $part;
    public $name;
    public $email;
    public $exists;
    private $password;

    public function __construct($user)
    {
        $this->id = $user;

        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT * FROM profiles INNER JOIN users ON profiles.user_id=users.user_id WHERE profiles.user_id='$this->id'";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $row_cnt = $result->num_rows;
        if ($row_cnt == 0) {
            $this->exists = False;
            return;
        } else {
            $this->exists = True;
        }
        $profiles = $result->fetch_assoc();
        $this->last_name = $profiles['last_name'];
        $this->first_name = $profiles['first_name'];
        $this->kana = $profiles['name_kana'];
        $this->grade = $profiles['grade'];
        $this->part = $profiles['part'];
        $this->name = $this->grade . $this->part . ' ' . $this->last_name . $this->first_name;
        $this->email = $profiles['email'];
        if ($profiles['password'] != '') {
            $this->password = '**********';
        } else {
            $this->password = '';
        }
    }

    public function get_name()
    {
        return $this->grade . $this->part . ' ' . $this->last_name . $this->first_name;
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
            return '**********（登録済）';
        } else {
            return '----------（未登録）';
        }
    }

    public function isAdmin()
    {
        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT * FROM admins WHERE user_id='$this->id'";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $row_cnt = $result->num_rows;
        if ($row_cnt >= 1) {
            return True;
        } else {
            return False;
        }
    }

    public function isMaster()
    {
        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT * FROM admins WHERE user_id='$this->id' AND role='MASTER'";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $row_cnt = $result->num_rows;
        if ($row_cnt == 1) {
            return True;
        } else {
            return False;
        }
    }

    public function isManager()
    {
        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT * FROM admins WHERE user_id='$this->id' AND (role='MANAGER' OR role='MASTER')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $row_cnt = $result->num_rows;
        if ($row_cnt == 1) {
            return True;
        } else {
            return False;
        }
    }

    public function isAccountant()
    {
        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT * FROM admins WHERE user_id='$this->id' AND (role='ACCOUNTANT' OR role='MASTER')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $row_cnt = $result->num_rows;
        if ($row_cnt == 1) {
            return True;
        } else {
            return False;
        }
    }

    public function isCamp()
    {
        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT * FROM admins WHERE user_id='$this->id' AND (role='CAMP' OR role='MASTER')";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $row_cnt = $result->num_rows;
        if ($row_cnt == 1) {
            return True;
        } else {
            return False;
        }
    }

    public function get_status()
    {
        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT status FROM users WHERE user_id='$this->id'";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $status = $result->fetch_assoc()['status'];
        if (strcmp($status, "PRESENT") == 0) {
            return "在団";
        } elseif (strcmp($status, "ABSENT") == 0) {
            return "休団";
        } elseif (strcmp($status, "RESIGNED") == 0) {
            return "退団";
        }
    }

    public function get_delinquent()
    {
        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT SUM(price) FROM accounting_records WHERE user_id='$this->id' AND datetime IS NULL";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $delinquent = $result->fetch_assoc()['SUM(price)'];

        return  "￥" . number_format($delinquent);
    }

    public function get_individual_accounting_total()
    {
        require __DIR__ . '/../Common/dbconnect.php';
        $query = "SELECT SUM(price) FROM individual_accounting_records WHERE user_id='$this->id'";
        $result = $mysqli->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $individual_accounting_total = $result->fetch_assoc()['SUM(price)'];

        return "￥" . number_format($individual_accounting_total);
    }
}
