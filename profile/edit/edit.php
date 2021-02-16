<?php
require __DIR__ . '/../../Common/init_page.php';

if (!isset($_POST['submit']) || !isset($_POST['email'])) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

$query = "SELECT email FROM users WHERE user_id=$USER->id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $email_old = $row['email'];
}

$email = $mysqli->real_escape_string($_POST['email']);
$query = "UPDATE users SET email='$email' WHERE user_id=$USER->id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$_SESSION['mypage_edit_profile'] = '';
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "がプロフィールを編集しました。（編集前：" . $email_old . "）（編集後：" . $email . "）\n", 3, __DIR__ . "/../../Core/account_manage.log");

header('Location: ' . MYPAGE_ROOT);
exit();
