<?php
require(__DIR__.'/../Core/config.php');

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

if ($result->fetch_assoc() == null) {
    print('You need to initialize MySQL table.');
    exit();
}
