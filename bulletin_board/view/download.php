<?php
require __DIR__ . '/../../Common/init_page.php';

if (!isset($_GET['bulletin_board_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

$bulletin_board_id = $_GET['bulletin_board_id'];
$query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_boards.title, bulletin_boards.status, bulletin_board_contents.content, bulletin_boards.status, bulletin_boards.user_id FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.bulletin_board_id=$bulletin_board_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $markdown = $row['content'];
    $title = $row['title'];
    $user_id = $row['user_id'];
    $status = $row['status'];
}

if ($status == 'DRAFT' && $user_id != $USER->id) {
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}


header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename=' . $title . '.md');
echo $markdown;
