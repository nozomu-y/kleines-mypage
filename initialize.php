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

$query = "
CREATE TABLE IF NOT EXISTS users (
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
CREATE TABLE IF NOT EXISTS profiles (
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
CREATE TABLE IF NOT EXISTS admins (
    user_id int(5) UNSIGNED ZEROFILL,
    role varchar(32),
    PRIMARY KEY (user_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE IF NOT EXISTS login_histories (
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
CREATE TABLE IF NOT EXISTS password_updates (
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
CREATE TABLE IF NOT EXISTS identity_verifications (
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
CREATE TABLE IF NOT EXISTS accounting_lists (
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
CREATE TABLE IF NOT EXISTS accounting_records (
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
CREATE TABLE IF NOT EXISTS individual_accounting_lists (
    list_id int(5) UNSIGNED ZEROFILL AUTO_INCREMENT,
    name varchar(256),
    datetime datetime,
    PRIMARY KEY (list_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE IF NOT EXISTS individual_accounting_records (
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    price int(10),
    accounting_id int(5) UNSIGNED ZEROFILL,
    list_id int(5) UNSIGNED ZEROFILL
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE IF NOT EXISTS bulletin_boards (
    bulletin_board_id int UNSIGNED ZEROFILL AUTO_INCREMENT,
    user_id int(5) UNSIGNED ZEROFILL,
    status varchar(32),
    PRIMARY KEY (bulletin_board_id)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE IF NOT EXISTS bulletin_board_contents (
    bulletin_board_id int UNSIGNED ZEROFILL,
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    content varchar(21800),
    PRIMARY KEY (bulletin_board_id, datetime)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE IF NOT EXISTS bulletin_board_hashtags (
    bulletin_board_id int UNSIGNED ZEROFILL,
    hashtag varchar(32),
    PRIMARY KEY (bulletin_board_id, hashtag)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "
CREATE TABLE IF NOT EXISTS bulletin_board_views (
    bulletin_board_id int UNSIGNED ZEROFILL,
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    PRIMARY KEY (bulletin_board_id, datetime)
);";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

print("Database initialization finished!\n");

$query = "SELECT user_id FROM users";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$row_cnt = $result->num_rows;
if ($row_cnt == 0) {
    while (true) {
        echo 'Last Name: ';
        $last_name = trim(fgets(STDIN));
        if (preg_match('/^\S+$/', $last_name)) {
            break;
        } else {
            echo 'Format not expected.\n';
        }
    }

    while (true) {
        echo 'First Name: ';
        $first_name = trim(fgets(STDIN));
        if (preg_match('/^\S+$/', $first_name)) {
            break;
        } else {
            echo 'Format not expected.\n';
        }
    }

    while (true) {
        echo 'Name Kana: ';
        $name_kana = trim(fgets(STDIN));
        if (preg_match('/^[ァ-ヶー]+$/u', $name_kana)) {
            break;
        } else {
            echo 'Format not expected.\n';
        }
    }

    while (true) {
        echo 'Grade (integer): ';
        $grade = trim(fgets(STDIN));
        if (preg_match('/^\d+$/', $grade)) {
            break;
        } else {
            echo 'Format not expected.\n';
        }
    }

    while (true) {
        echo 'Part (S,A,T,B): ';
        $part = trim(fgets(STDIN));
        if (preg_match('/^[S|A|T|B]$/', $part)) {
            break;
        } else {
            echo 'Format not expected.\n';
        }
    }

    while (true) {
        echo 'Email: ';
        $email = trim(fgets(STDIN));
        if (preg_match('/^[^\s]+@[^\s]+$/', $email)) {
            break;
        } else {
            echo 'Format not expected.\n';
        }
    }

    while (true) {
        echo 'Password (at least 8 letters): ';
        $password = trim(fgets(STDIN));
        if (preg_match('/^([\x21-\x7E]{8,})$/', $password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            break;
        } else {
            echo 'Format not expected.\n';
        }
    }

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

    $query = "INSERT INTO profiles (user_id, last_name, first_name, name_kana, grade, part) VALUES ('$user_id', '$last_name', '$first_name', '$name_kana', '$grade', '$part')";
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
    print("Account set as admin: " . $grade . $part . " " . $last_name . $first_name . "\n");
}
