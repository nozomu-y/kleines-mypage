<?php

class User
{
    public $id;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $grade;
    public $part;
    public $admin;

    public function __construct($user)
    {
        $this->id = $user['id'];
        $this->email = $user['email'];
        $this->password = $user['password'];
        $this->first_name = $user['first_name'];
        $this->last_name = $user['last_name'];
        $this->grade = $user['grade'];
        $this->part = $user['part'];
        $this->admin = $user['admin'];
    }
}
