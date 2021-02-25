<?php
require __DIR__ . '/../../Common/init_page.php';

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

if ($_POST['markdown'] == '' || $_POST['title'] == '' || !($_POST['status'] == 'DRAFT' || $_POST['status'] == 'RELEASE')) {
    // error
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

if (mb_strlen($_POST['markdown']) > 20000) {
    // error
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

$hashtags = $_POST['hashtags'];
$hashtags = preg_split('/[\s#,]+/', $hashtags);

$title = $mysqli->real_escape_string($_POST['title']);
$markdown = $mysqli->real_escape_string($_POST['markdown']);
$status = $_POST['status'];

$bulletin_board_id = $_POST['bulletin_board_id'];
if ($bulletin_board_id == '') {
    $query = "INSERT INTO bulletin_boards (user_id, title, status) VALUES ('$USER->id', '$title', '$status')";
    $result = $mysqli->query($query);
    $bulletin_board_id = $mysqli->insert_id;
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
} else {
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

    $query = "UPDATE bulletin_boards SET title='$title', status='$status' WHERE bulletin_board_id=$bulletin_board_id and user_id=$USER->id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
}

$query = "INSERT INTO bulletin_board_contents (bulletin_board_id, user_id, datetime, content) VALUES ('$bulletin_board_id','$USER->id', now(), '$markdown')";
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

$hashtag_count = 0;
$query = "INSERT INTO bulletin_board_hashtags (bulletin_board_id, hashtag) VALUES";
foreach ($hashtags as $hashtag) {
    if ($hashtag == '') continue;
    $hashtag = $mysqli->real_escape_string($hashtag);
    $query .= " ($bulletin_board_id, '$hashtag'),";
    $hashtag_count++;
}
$query = mb_substr($query, 0, -1);

if ($hashtag_count > 0) {
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
}


header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
exit();
