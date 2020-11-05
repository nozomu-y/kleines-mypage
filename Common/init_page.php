<?php
ob_start();
session_start();

require __DIR__ . '/dbconnect.php';
require __DIR__ . '/../Class/User.php';
require __DIR__ . '/../Class/Fee.php';
require __DIR__ . '/../Class/Fee_List.php';
require __DIR__ . '/../Class/Individual_Accounting.php';
require __DIR__ . '/function.php';

if (strcmp(getGitBranch(), "master") && WEB_DOMAIN == "chorkleines.com") {  // if current branch is not master
    $MAINTENANCE = true;
} else {
    $MAINTENANCE = false;
}

$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$USER = new User($result->fetch_assoc());

if ($MAINTENANCE && $USER->admin != 1) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (!isset($_SESSION['mypage_email'])) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}
