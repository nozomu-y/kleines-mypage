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
        echo $this->grade . $this->part . ' ' . $this->name;
    }
}
