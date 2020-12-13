<?php
require __DIR__ . '/../Common/init_page.php';

if (isset($_POST['delete'])) {
    $token = $_POST['delete'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/profile/sessions.php');
    exit();
}

$query = "SELECT * FROM auto_login WHERE token = '$token'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
    $login_platform = $row['device'];
    $login_browser = $row['browser'];
}
if ($user_id == $USER->id) {
    $query = "DELETE FROM auto_login WHERE token = '$token'";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
}

$_SESSION['mypage_delete_session'] = $login_platform . ' (' . $login_browser . ') ';

header('Location: ' . MYPAGE_ROOT . '/profile/sessions.php');
exit();
