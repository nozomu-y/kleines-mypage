<?php

if (php_sapi_name() != 'cli') {
    throw new Exception('This script must be run on the command line.');
}

require __DIR__ . '/Core/config.php';

if (empty(DB_HOST) && empty(DB_USER) && empty(DB_PASS) && empty(DB_NAME) && empty(MYPAGE_ROOT) && empty(WEB_DOMAIN) && empty(ADMIN_EMAIL)) {
    print("Please fill in all the constants in /Core/config.php");
    exit();
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    error_log($mysqli->connect_error);
    exit;
}

$query = "SHOW TABLES";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

if ($result->fetch_assoc() != null) {
    print('MySQL database is already initialized.');
    exit();
}

$query = "
CREATE TABLE users (
    user_id int(5) UNSIGNED ZEROFILL AUTO_INCREMENT,
    email varchar(256) UNIQUE,
    password varchar(256),
    status varchar(32),
    PRIMARY KEY (user_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE profiles (
    user_id int(5) UNSIGNED ZEROFILL,
    last_name varchar(256),
    first_name varchar(256),
    name_kana varchar(256),
    grade int(2) UNSIGNED ZEROFILL,
    part varchar(1),
    birthday date,
    PRIMARY KEY (user_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE admins (
    user_id int(5) UNSIGNED ZEROFILL,
    role varchar(32),
    PRIMARY KEY (user_id, role)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE login_histories (
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    success int(1),
    IP varchar(32),
    PRIMARY KEY (user_id, datetime)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE password_updates (
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    IP varchar(32),
    PRIMARY KEY (user_id, datetime)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE identity_verifications (
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    token varchar(64),
    PRIMARY KEY (user_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE accounting_lists (
    accounting_id int(5) UNSIGNED ZEROFILL AUTO_INCREMENT,
    name varchar(256),
    deadline date,
    admin varchar(32),
    PRIMARY KEY (accounting_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE accounting_records (
    accounting_id int(5) UNSIGNED ZEROFILL,
    user_id int(5) UNSIGNED ZEROFILL,
    price int(10),
    paid_cash int(10),
    datetime datetime,
    PRIMARY KEY (accounting_id, user_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE individual_accounting_lists (
    individual_accounting_id int(5) UNSIGNED ZEROFILL AUTO_INCREMENT,
    name varchar(256),
    datetime datetime,
    price int(10),
    PRIMARY KEY (individual_accounting_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE individual_accounting_records (
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    name varchar(256),
    price int(10),
    accounting_id int(5) UNSIGNED ZEROFILL,
    individual_accounting_id int(5) UNSIGNED ZEROFILL
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$last_name = 'admin';
$grade = 0;
$email = $mysqli->real_escape_string(ADMIN_EMAIL);
$part = "S";
$password = password_hash("password", PASSWORD_DEFAULT);
$query = "INSERT INTO users (email, password, status) VALUES ('$email', '$password', 'PRESENT')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "SELECT user_id FROM users WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];
}

$query = "INSERT INTO profiles (user_id, last_name, grade, part) VALUES ('$user_id', '$last_name', '$grade', '$part')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'MASTER')";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}



print("Database initialization finished!\n");
print("Login as admin...\n");
print("Email: " . ADMIN_EMAIL . "\n");
print("Password: password");
