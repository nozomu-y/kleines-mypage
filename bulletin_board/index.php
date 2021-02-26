<?php
require __DIR__ . '/../Common/init_page.php';

$PAGE_NAME = "掲示板";
include_once __DIR__ . '/../Common/head.php';

$bulletin_boards = array();
if (isset($_GET['owner'])) {
    $owner = "&owner";
    $owner_first = "?owner";
    $query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime, bulletin_boards.title, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_boards.user_id=profiles.user_id INNER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.user_id=$USER->id ORDER BY bulletin_board_contents.datetime DESC";
} else {
    $owner = "";
    $owner_first = "";
    $query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime, bulletin_boards.title, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_boards.user_id=profiles.user_id INNER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.status='RELEASE' ORDER BY bulletin_board_contents.datetime DESC";
}
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $bulletin_board = array();
    $bulletin_board = $row;
    $bulletin_board['datetime'] = date('Y/m/d H:i', strtotime($bulletin_board['datetime']));
    $bulletin_board['hashtags'] = explode(" ", $bulletin_board['hashtags']);
    if (isset($_GET['hashtag']) && !in_array($_GET['hashtag'], $bulletin_board['hashtags'])) continue;
    array_push($bulletin_boards, $bulletin_board);
}
?>
<style>
    .list-group-flush:last-child .list-group-item:last-child {
        border-bottom: 1px solid rgba(0, 0, 0, .125);
    }

    .badge {
        margin: 2px 0;
        padding: 5px 8px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex w-100 justify-content-between mb-4">
        <h1 class="h3 text-gray-800 m-0">掲示板</h1>
        <div class="d-block d-md-none my-auto">
            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-h"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="./edit/" class="dropdown-item" type="button">新規作成</a>
                <?php
                if (!isset($_GET['owner'])) {
                ?>
                    <a href="./?owner" class="dropdown-item" type="button">自分の投稿</a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
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
            <div class="col-md-9">
                <div class="list-group mb-3">
                    <?php
                    foreach ($bulletin_boards as $bulletin_board) {
                        $bulletin_board_id = $bulletin_board['bulletin_board_id'];
                        $title = $bulletin_board['title'];
                        $name = $bulletin_board['name'];
                        $datetime = $bulletin_board['datetime'];
                        $hashtags = $bulletin_board['hashtags'];
                    ?>
                        <div class="list-group-item flex-column align-items-start">
                            <div class="row">
                                <div class="col-auto mr-auto text-truncate">
                                    <a class="mb-1 h5 text-dark" href="./view/?bulletin_board_id=<?= $bulletin_board_id ?>"><?= $title  ?></a>
                                </div>
                                <div class="col-auto ml-auto">
                                    <small><span class="mr-3 text-nowrap"><i class="fas fa-user mr-1"></i><?= $name ?></span></small>
                                    <small><span class="text-nowrap"><?= $datetime ?></span></small>
                                </div>
                            </div>
                            <div class="mt-1 text-truncate">
                                <?php
                                if ($bulletin_board['status'] == 'DRAFT') {
                                ?>
                                    <span class="badge badge-danger font-weight-normal">下書き</span>
                                <?php
                                }
                                ?>
                                <?php
                                foreach ($hashtags as $hashtag) {
                                ?>
                                    <a href="./?hashtag=<?= $hashtag . $owner ?>" class="badge badge-secondary font-weight-normal text-white"><?= $hashtag ?></a>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="list-group">
                    <a href="./edit/" class="list-group-item list-group-item-action">新規作成</a>
                    <?php
                    if (!isset($_GET['owner'])) {
                    ?>
                        <a href="./?owner" class="list-group-item list-group-item-action">自分の投稿</a>
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
        $bulletin_board_id = $bulletin_board['bulletin_board_id'];
        $title = $bulletin_board['title'];
        $name = $bulletin_board['name'];
        $datetime = $bulletin_board['datetime'];
        $hashtags = $bulletin_board['hashtags'];
    ?>
        <div class="list-group-item pt-1">
            <div class="text-right mb-2">
                <small><span class="mr-3 text-nowrap"><i class="fas fa-user mr-1"></i><?= $name ?></span><span class="text-nowrap"><?= $datetime ?></span></small>
            </div>
            <div class="text-truncate">
                <a class="mb-1 h5 text-dark" href="./view/?bulletin_board_id=<?= $bulletin_board_id ?>"><?= $title ?></a>
            </div>
            <div class="mt-1 text-truncate">
                <?php
                if ($bulletin_board['status'] == 'DRAFT') {
                ?>
                    <span class="badge badge-danger font-weight-normal">下書き</span>
                <?php
                }
                ?>
                <?php
                foreach ($hashtags as $hashtag) {
                ?>
                    <a href="./?hashtag=<?= $hashtag . $owner ?>" class="badge badge-secondary font-weight-normal text-white"><?= $hashtag ?></a>
                <?php
                }
                ?>
            </div>
        </div>
    <?php
    }
    ?>
</div>



<?php
include_once __DIR__ . '/../Common/foot.php';
