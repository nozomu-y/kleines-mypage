<?php
ob_start();
session_start();

require __DIR__ . '/dbconnect.php';
require __DIR__ . '/../Class/User.php';
require __DIR__ . '/../Class/AccountingRecord.php';
require __DIR__ . '/../Class/AccountingList.php';
require __DIR__ . '/../Class/IndividualAccountingList.php';
require __DIR__ . '/function.php';

if (strcmp(getGitBranch(), "master") && WEB_DOMAIN == "chorkleines.com") {  // if current branch is not master
    $MAINTENANCE = true;
} else {
    $MAINTENANCE = false;
}

$USER = new User($_SESSION['mypage_user_id']);
if (!$USER->exists) {
    $_SESSION = array();
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if ($MAINTENANCE && !$USER->isMaster()) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (!isset($_SESSION['mypage_user_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}
