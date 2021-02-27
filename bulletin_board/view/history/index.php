<?php
require __DIR__ . '/../../../Common/init_page.php';

$PAGE_NAME = "掲示板";
include_once __DIR__ . '/../../../Common/head.php';

if (!isset($_GET['bulletin_board_id'])) {
    // error
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}
$bulletin_board_id = $_GET['bulletin_board_id'];

$bulletin_boards = array();

$query = "SELECT bulletin_boards.title, bulletin_boards.status, bulletin_boards.user_id, bulletin_board_contents.datetime FROM bulletin_board_contents INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_board_contents.bulletin_board_id=$bulletin_board_id AND bulletin_boards.user_id=$USER->id ORDER BY bulletin_board_contents.datetime DESC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
if ($result->num_rows == 0) {
    // error
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/view/?bulletin_board_id=' . $bulletin_board_id);
    exit();
}
while ($row = $result->fetch_assoc()) {
    $title = $row['title'];
    $status = $row['status'];
    $user_id = $row['user_id'];
    $bulletin_board = array();
    $bulletin_board = $row;
    $bulletin_board['datetime_str'] = date('Y/m/d H:i', strtotime($bulletin_board['datetime']));
    array_push($bulletin_boards, $bulletin_board);
}
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">掲示板</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php
            if ($status == 'RELEASE') {
            ?>
                <li class="breadcrumb-item"><a href="../../">掲示板</a></li>
                <li class="breadcrumb-item"><a href="../?bulletin_board_id=<?= $bulletin_board_id ?>"><?= $title ?></a></li>
                <li class="breadcrumb-item text-truncate active" aria-current="page">編集履歴</li>
            <?php
            } elseif ($status == 'DRAFT') {
            ?>
                <li class="breadcrumb-item"><a href="../../">掲示板</a></li>
                <li class="breadcrumb-item"><a href="../../?owner">自分の投稿</a></li>
                <li class="breadcrumb-item"><a href="../?bulletin_board_id=<?= $bulletin_board_id ?>"><?= $title ?></a></li>
                <li class="breadcrumb-item text-truncate active" aria-current="page">編集履歴</li>
            <?php
            }
            ?>
        </ol>
    </nav>
    <?php
    if (isset($_GET['owner'])) {
    ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./">掲示板</a></li>
                <li class="breadcrumb-item active" aria-current="page">自分の投稿</li>
            </ol>
        </nav>
    <?php
    }
    ?>
    <?php
    if (isset($_GET['hashtag'])) {
    ?>
        <div class="mb-3">
            <span><a href="./<?= $owner_first ?>" class="badge badge-secondary font-weight-normal text-white"><?= $_GET['hashtag'] ?><i class="fas fa-times ml-2"></i></a></span>
        </div>
    <?php
    }
    ?>
    <div class="d-none d-md-block">
        <div class="row">
            <div class="col-md-12">
                <div class="list-group mb-3">
                    <?php
                    foreach ($bulletin_boards as $bulletin_board) {
                        $datetime = $bulletin_board['datetime'];
                        $datetime_str = $bulletin_board['datetime_str'];
                    ?>
                        <a class="list-group-item list-group-item-action" href="../../view/?bulletin_board_id=<?= $bulletin_board_id ?>&datetime=<?= $datetime ?>"><?= $datetime_str  ?></a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="list-group list-group-flush mb-3 d-block d-md-none">
    <?php
    foreach ($bulletin_boards as $bulletin_board) {
        $datetime = $bulletin_board['datetime'];
        $datetime_str = $bulletin_board['datetime_str'];
    ?>
        <a class="list-group-item list-group-item-action" href="../../view/?bulletin_board_id=<?= $bulletin_board_id ?>&datetime=<?= $datetime ?>"><?= $datetime_str  ?></a>
    <?php
    }
    ?>
</div>



<?php
include_once __DIR__ . '/../../../Common/foot.php';
