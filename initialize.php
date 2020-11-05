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
CREATE TABLE members (
    id int(5) UNSIGNED ZEROFILL AUTO_INCREMENT,
    email varchar(256) UNIQUE,
    password varchar(256),
    last_name varchar(32),
    first_name varchar(32),
    kana varchar(32),
    grade int(2),
    part varchar(1),
    token varchar(256),
    validation_time datetime,
    login_failure int(2),
    admin int(1),
    status int(1) DEFAULT 0,
    PRIMARY KEY (id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE fee_list (
    id int(3) UNSIGNED ZEROFILL PRIMARY KEY,
    name varchar(32),
    deadline datetime,
    price int(10),
    admin int(0)
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
$query = "INSERT INTO members (email, last_name, grade, part, password, admin) VALUES ('$email', '$last_name','$grade', '$part', '$password', 1)";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "SELECT id FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
}

$id = sprintf('%05d', $id);
$id = strval($id);
$query = "
          CREATE TABLE individual_accounting_$id (
            id int(3) UNSIGNED ZEROFILL PRIMARY KEY,
            date date,
            name varchar(256),
            memo varchar(256),
            price int(10),
            fee_id int(3) UNSIGNED ZEROFILL
          );";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$query = "
          CREATE TABLE fee_record_$id (
            id int(3) UNSIGNED ZEROFILL PRIMARY KEY,
            datetime datetime,
            price int(10),
            paid_cash int(10),
            status int(1)
          );";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

print("Database initialization finished!\n");
print("Login as admin...\n");
print("Email: kleines-mypage@example.com\n");
print("Password: password");
