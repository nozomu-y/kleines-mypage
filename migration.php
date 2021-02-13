<?php
if (php_sapi_name() != 'cli') {
    throw new Exception('This script must be run on the command line.');
}

require(__DIR__ . '/Core/config.php');

$mysqli_old = new mysqli(DB_HOST, DB_USER, DB_PASS, "member_list");
if ($mysqli_old->connect_error) {
    error_log($mysqli_old->connect_error);
    exit;
}

$mysqli_new = new mysqli(DB_HOST, DB_USER, DB_PASS, "kleines_mypage");
if ($mysqli_new->connect_error) {
    error_log($mysqli_new->connect_error);
    exit;
}

$query = "SHOW TABLES";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}

if ($result->fetch_assoc() != null) {
    print('MySQL database is already initialized.');
    exit();
}

/*** create tables ***/

$query = "
CREATE TABLE users (
    user_id int(5) UNSIGNED ZEROFILL AUTO_INCREMENT,
    email varchar(256) UNIQUE,
    password varchar(256),
    status varchar(32),
    PRIMARY KEY (user_id)
);";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
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
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}

$query = "
CREATE TABLE admins (
    user_id int(5) UNSIGNED ZEROFILL,
    role varchar(32),
    PRIMARY KEY (user_id)
);";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
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
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}

$query = "
CREATE TABLE password_updates (
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    IP varchar(32),
    PRIMARY KEY (user_id, datetime)
);";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}

$query = "
CREATE TABLE identity_verifications (
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    token varchar(64),
    PRIMARY KEY (user_id)
);";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
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
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
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
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}

$query = "
CREATE TABLE individual_accounting_lists (
    list_id int(5) UNSIGNED ZEROFILL AUTO_INCREMENT,
    name varchar(256),
    datetime datetime,
    PRIMARY KEY (list_id)
);";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}

$query = "
CREATE TABLE individual_accounting_records (
    user_id int(5) UNSIGNED ZEROFILL,
    datetime datetime,
    name varchar(256),
    price int(10),
    accounting_id int(5) UNSIGNED ZEROFILL,
    list_id int(5) UNSIGNED ZEROFILL
);";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}

/*** start migration ***/
$query = "SELECT * FROM fee_list ORDER BY id ASC";
$fee_list = $mysqli_old->query($query);
if (!$fee_list) {
    print('Query Failed : ' . $mysqli_old->error);
    $mysqli_old->close();
    exit();
}
$zenkoku_list = [];
while ($row = $fee_list->fetch_assoc()) {
    $accounting_id = $row['id'];
    $name = $row['name'];
    $deadline = $row['deadline'];
    $price = $row['price'];
    $admin = $row['admin'];

    $admin_new = "GENERAL";
    if ($admin == 3) {
        $admin_new = "GENERAL";
    } elseif ($admin == 5) {
        $admin_new = "CAMP";
        array_push($zenkoku_list, $accounting_id);
        continue;
    }

    $query = "INSERT INTO accounting_lists (accounting_id, name, deadline, admin) VALUES ('$accounting_id', '$name', '$deadline', '$admin_new')";
    $result = $mysqli_new->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
        $mysqli_new->close();
        exit();
    }
    print("Finished: " . $name . "\n");
}
$query = "INSERT INTO accounting_lists (name, deadline, admin) VALUES ('全国大会集金', '2019/10/31', 'CAMP')";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}
$zenkoku_id = $mysqli_new->insert_id;

$query = "SELECT * FROM members ORDER BY id ASC";
$members = $mysqli_old->query($query);
if (!$members) {
    print('Query Failed : ' . $mysqli_old->error);
    $mysqli_old->close();
    exit();
}
while ($row = $members->fetch_assoc()) {
    $user_id = $row['id'];
    $email = $row['email'];
    $password = $row['password'];
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $name_kana = $row['kana'];
    $grade = $row['grade'];
    $part = $row['part'];
    $admin = $row['admin'];
    $status = $row['status'];
    print("Start: " . $grade . $part . " " . $last_name . $first_name . "\n");

    $status_new = "PRESENT";
    if ($status == 0) {
        $status_new = "PRESENT";
    } elseif ($status == 1) {
        $status_new = "ABSENT";
    } elseif ($status == 2) {
        $status_new = "RESIGNED";
    }

    if ($email != null) {
        if ($password != null) {
            $query = "INSERT INTO users (user_id, email, password, status) VALUES ('$user_id', '$email', '$password', '$status_new')";
        } else {
            $query = "INSERT INTO users (user_id, email, status) VALUES ('$user_id', '$email', '$status_new')";
        }
    } else {
        if ($password != null) {
            $query = "INSERT INTO users (user_id, password, status) VALUES ('$user_id', '$password', '$status_new')";
        } else {
            $query = "INSERT INTO users (user_id, status) VALUES ('$user_id', '$status_new')";
        }
    }

    $result = $mysqli_new->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
        $mysqli_new->close();
        exit();
    }

    $query = "INSERT INTO profiles (user_id, last_name, first_name, name_kana, grade, part) VALUES ('$user_id', '$last_name', '$first_name', '$name_kana', '$grade', '$part')";
    $result = $mysqli_new->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
        $mysqli_new->close();
        exit();
    }

    if ($admin == 1) {
        $query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'MASTER')";
        $result = $mysqli_new->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
            $mysqli_new->close();
            exit();
        }
    } elseif ($admin == 2) {
        $query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'MANAGER')";
        $result = $mysqli_new->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
            $mysqli_new->close();
            exit();
        }
    } elseif ($admin == 3) {
        $query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'ACCOUNTANT')";
        $result = $mysqli_new->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
            $mysqli_new->close();
            exit();
        }
    } elseif ($admin == 5) {
        $query = "INSERT INTO admins (user_id, role) VALUES ('$user_id', 'CAMP')";
        $result = $mysqli_new->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
            $mysqli_new->close();
            exit();
        }
    }
    /*** fee_record ***/
    $query = "SELECT * FROM fee_record_$user_id";
    $fee_record = $mysqli_old->query($query);
    if (!$fee_record) {
        print('Query Failed : ' . $mysqli_old->error);
        $mysqli_old->close();
        exit();
    }
    while ($row = $fee_record->fetch_assoc()) {
        $accounting_id = $row['id'];
        $datetime = $row['datetime'];
        $price = $row['price'];
        $paid_cash = $row['paid_cash'];
        if ($paid_cash == null) {
            $paid_cash = 0;
        }
        if (in_array($accounting_id, $zenkoku_list)) {
            $accounting_id = $zenkoku_id;
        }
        if ($datetime != null) {
            $query = "INSERT INTO accounting_records (accounting_id, user_id, price, paid_cash, datetime) VALUES ('$accounting_id', '$user_id', '$price', '$paid_cash', '$datetime')";
        } else {
            $query = "INSERT INTO accounting_records (accounting_id, user_id, price, paid_cash) VALUES ('$accounting_id', '$user_id', '$price', '$paid_cash')";
        }
        $result = $mysqli_new->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
            $mysqli_new->close();
            exit();
        }
    }
    $query = "SELECT * FROM individual_accounting_$user_id";
    $individual_accounting = $mysqli_old->query($query);
    if (!$individual_accounting) {
        print('Query Failed : ' . $mysqli_old->error);
        $mysqli_old->close();
        exit();
    }
    while ($row = $individual_accounting->fetch_assoc()) {
        $individual_accounting_id = $row['id'];
        $date = $row['date'];
        $name = $row['name'];
        $price = $row['price'];
        $accounting_id = $row['fee_id'];
        if ($accounting_id != null) {
            $query = "INSERT INTO individual_accounting_records (user_id, datetime, name, price, accounting_id) VALUES ('$user_id', '$date', '$name', '$price','$accounting_id')";
        } else {
            $query = "INSERT INTO individual_accounting_records (user_id, datetime, name, price) VALUES ('$user_id', '$date', '$name', '$price')";
        }
        $result = $mysqli_new->query($query);
        if (!$result) {
            print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
            $mysqli_new->close();
            exit();
        }
    }
    print("Finished: " . $grade . $part . " " . $last_name . $first_name . "\n");
}

$query = "SELECT name, MIN(datetime) AS datetime from individual_accounting_records WHERE accounting_id IS NULL GROUP BY name";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    $datetime = $row['datetime'];
    $query_1 = "INSERT INTO individual_accounting_lists (name, datetime) VALUES ('$name', '$datetime')";
    $result_1 = $mysqli_new->query($query_1);
    if (!$result_1) {
        print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
        $mysqli_new->close();
        exit();
    }
    $list_id = $mysqli_new->insert_id;
    $query_1 = "UPDATE individual_accounting_records SET list_id='$list_id' WHERE name='$name'";
    $result_1 = $mysqli_new->query($query_1);
    if (!$result_1) {
        print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
        $mysqli_new->close();
        exit();
    }
}

$query = "ALTER TABLE individual_accounting_records DROP name";
$result = $mysqli_new->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli_new->error . ' on line ' . __LINE__);
    $mysqli_new->close();
    exit();
}
