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
CREATE TABLE IF NOT EXISTS bulletin_boards (
    bulletin_board_id int UNSIGNED ZEROFILL AUTO_INCREMENT,
    user_id int(5) UNSIGNED ZEROFILL,
    title varchar(128),
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
