<?php
require __DIR__ . '/../../Common/init_page.php';

if (!isset($_POST['bulletin_board_id']) || !isset($_POST['delete'])) {
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

$bulletin_board_id = $_POST['bulletin_board_id'];

$query = "SELECT * FROM bulletin_boards WHERE bulletin_board_id=$bulletin_board_id and user_id=$USER->id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$row_cnt = $result->num_rows;
if ($row_cnt == 0) {
    // error
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

$query = "DELETE FROM bulletin_boards WHERE bulletin_board_id=$bulletin_board_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "DELETE FROM bulletin_board_contents WHERE bulletin_board_id=$bulletin_board_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "DELETE FROM bulletin_board_views WHERE bulletin_board_id=$bulletin_board_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}

$query = "DELETE FROM bulletin_board_hashtags WHERE bulletin_board_id=$bulletin_board_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}



header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
exit();
