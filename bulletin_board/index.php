<?php
require __DIR__ . '/../Common/init_page.php';

$PAGE_NAME = "掲示板";
include_once __DIR__ . '/../Common/head.php';

$bulletin_boards = array();
if (isset($_GET['owner'])) {
    $owner = "&owner";
    $owner_first = "?owner";
    $query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime, bulletin_boards.title, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id!=bulletin_board_contents.user_id) AS views, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id=$USER->id) AS user_views, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_boards.user_id=profiles.user_id LEFT OUTER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.user_id=$USER->id ORDER BY bulletin_board_contents.datetime DESC";
} else {
    $owner = "";
    $owner_first = "";
    $query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime, bulletin_boards.title, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id!=bulletin_board_contents.user_id) AS views, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id=$USER->id) AS user_views, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_boards.user_id=profiles.user_id LEFT OUTER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.status='RELEASE' ORDER BY bulletin_board_contents.datetime DESC";
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

<div class="container-fluid">
    <div class="d-flex w-100 justify-content-between mb-4">
        <h1 class="h3 text-gray-800 m-0">掲示板</h1>
        <div class="d-block d-md-none my-auto">
            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-h"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="./edit/" class="dropdown-item">新規作成</a>
                <?php
                if (!isset($_GET['owner'])) {
                ?>
                    <a href="./?owner" class="dropdown-item">自分の投稿</a>
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
            <span><a href="./<?= $owner_first ?>" class="badge badge-secondary font-weight-normal text-white"><?= h($_GET['hashtag']) ?><i class="fas fa-times ml-2"></i></a></span>
        </div>
    <?php
    }
    ?>
    <div class="d-none d-md-block">
        <div class="row">
            <div class="col-md-9">
                <div class="d-flex justify-content-center my-5" id="spinner-md">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="list-group mb-3 d-none" id="list-group-md">
                    <?php
                    foreach ($bulletin_boards as $bulletin_board) {
                        $bulletin_board_id = $bulletin_board['bulletin_board_id'];
                        $title = $bulletin_board['title'];
                        $name = $bulletin_board['name'];
                        $datetime = $bulletin_board['datetime'];
                        $hashtags = $bulletin_board['hashtags'];
                        $views = $bulletin_board['views'];
                        $user_views = $bulletin_board['user_views'];
                        if ($user_views > 0) {
                            $unread = "";
                        } else {
                            $unread = "unread";
                        }
                    ?>
                        <a class="list-group-item list-group-item-action flex-column align-items-start <?= $unread ?>" href="./view/?bulletin_board_id=<?= $bulletin_board_id ?>" style="min-height: 82px;">
                            <div class="d-flex w-100 justify-content-between">
                                <div class="mt-auto text-truncate">
                                    <h5 class="mb-1"><?= h($title)  ?></h5>
                                </div>
                                <div class="text-right text-nowrap">
                                    <small><span class="mr-2 text-nowrap"><i class="fas fa-user mr-1"></i><?= h($name) ?></span></small>
                                    <small><span class="text-nowrap"><?= $datetime ?></span></small>
                                </div>
                            </div>
                            <div class="mt-1 text-truncate mr-5">
                                <?php
                                if ($bulletin_board['status'] == 'DRAFT') {
                                ?>
                                    <span class="badge badge-danger font-weight-normal">下書き</span>
                                <?php
                                }
                                ?>
                                <?php
                                foreach ($hashtags as $hashtag) {
                                    if ($hashtag == '') continue;
                                ?>
                                    <object><a href="./?hashtag=<?= h($hashtag) . $owner ?>" class="badge badge-secondary font-weight-normal text-white"><?= h($hashtag) ?></a></object>
                                <?php
                                }
                                ?>
                            </div>
                            <!-- <small style="position:absolute;bottom:0;right:0;margin-right:1.25rem;margin-bottom:0.75rem;">
                                <span class="text-nowrap"><i class="fas fa-eye mr-1"></i><?= $views ?></span>
                            </small> -->
                        </a>
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

<div class="d-block d-md-none">
    <div class="d-flex justify-content-center my-5" id="spinner-sm">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <div id="list-group-sm" class="d-none">
        <div class="list-group list-group-flush mb-3">
            <?php
            foreach ($bulletin_boards as $bulletin_board) {
                $bulletin_board_id = $bulletin_board['bulletin_board_id'];
                $title = $bulletin_board['title'];
                $name = $bulletin_board['name'];
                $datetime = $bulletin_board['datetime'];
                $hashtags = $bulletin_board['hashtags'];
                $views = $bulletin_board['views'];
                $user_views = $bulletin_board['user_views'];
                if ($user_views > 0) {
                    $unread = "";
                } else {
                    $unread = "unread";
                }
            ?>
                <a class="list-group-item list-group-item-action pt-1 <?= $unread ?>" href="./view/?bulletin_board_id=<?= $bulletin_board_id ?>">
                    <div class="text-right mb-2">
                        <small>
                            <span class="mr-2 text-nowrap"><i class="fas fa-user mr-1"></i><?= h($name) ?></span>
                            <span class="text-nowrap"><?= $datetime ?></span>
                        </small>
                    </div>
                    <div class="text-truncate">
                        <h6 class="mb-1 text-dark"><?= h($title) ?></h6>
                    </div>
                    <div class="mt-1 text-truncate mr-5">
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
                            <object><a href="./?hashtag=<?= h($hashtag) . $owner ?>" class="badge badge-secondary font-weight-normal text-white"><?= h($hashtag) ?></a></object>
                        <?php
                        }
                        ?>
                    </div>
                    <!-- <small style="position:absolute;bottom:0;right:0;margin-right:1.25rem;margin-bottom:0.75rem;">
                        <span class="text-nowrap"><i class="fas fa-eye mr-1"></i><?= $views ?></span>
                    </small> -->
                </a>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        document.getElementById("spinner-md").remove();
        document.getElementById("list-group-md").classList.remove("d-none");
        document.getElementById("spinner-sm").remove();
        document.getElementById("list-group-sm").classList.remove("d-none");
    }
</script>

<?php

include_once __DIR__ . '/../Common/foot.php';
