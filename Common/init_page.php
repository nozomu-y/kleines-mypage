<?php
ob_start();
session_start();

require __DIR__ . '/dbconnect.php';
require __DIR__ . '/../Class/User.php';
require __DIR__ . '/../Class/AccountingRecord.php';
require __DIR__ . '/../Class/AccountingList.php';
require __DIR__ . '/../Class/Fee.php';
require __DIR__ . '/../Class/Fee_List.php';
require __DIR__ . '/../Class/Individual_Accounting.php';
require __DIR__ . '/function.php';

if (strcmp(getGitBranch(), "master") && WEB_DOMAIN == "chorkleines.com") {  // if current branch is not master
    $MAINTENANCE = true;
} else {
    $MAINTENANCE = false;
}

$user_id = $_SESSION['mypage_user_id'];
$USER = new User($user_id);

if ($MAINTENANCE && !$USER->isMaster()) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (!isset($_SESSION['mypage_user_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}
