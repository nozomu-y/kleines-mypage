<?php
if (php_sapi_name() != 'cli') {
    throw new Exception('This script must be run on the command line.');
}

require __DIR__ . '/Core/config.php';
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
while ($row = $result->fetch_assoc()) {
    $table = $row["Tables_in_kleines_mypage"];
    print($table . "\n");
    $query = "DROP TABLE $table";
    $result_2 = $mysqli->query($query);
    if (!$result_2) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
}
