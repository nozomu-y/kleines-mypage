<?php
require __DIR__ . '/../Common/init_page.php';

$PAGE_NAME = "掲示板";
include_once __DIR__ . '/../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">掲示板</h1>
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <a href="./edit/" class="btn btn-primary">新規作成</a>
                <a href="./draft/" class="btn btn-primary">下書き</a>
            </div>
            <div class="list-group mb-3">
                <?php
                $query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime, bulletin_boards.title, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_board_contents.user_id=profiles.user_id INNER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.status='RELEASE' ORDER BY bulletin_board_contents.datetime DESC";
                $result = $mysqli->query($query);
                if (!$result) {
                    print('Query Failed : ' . $mysqli->error);
                    $mysqli->close();
                    exit();
                }
                while ($row = $result->fetch_assoc()) {
                    $bulletin_board_id = $row['bulletin_board_id'];
                    $title = $row['title'];
                    $name = $row['name'];
                    $datetime = $row['datetime'];
                    $datetime = date('Y/m/d H:i', strtotime($datetime));
                    $hashtags = $row['hashtags'];
                    $hashtags = explode(" ", $hashtags);
                ?>
                    <div class="list-group-item flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between">
                            <a class="mb-1 h5 text-dark" href="./view/?bulletin_board_id=<?= $bulletin_board_id ?>"><?= $title ?></a>
                            <small><span class="mr-3"><i class="fas fa-user mr-1"></i><?= $name ?></span><span><?= $datetime ?></span></small>
                        </div>
                        <div class="mt-1">
                            <?php
                            foreach ($hashtags as $hashtag) {
                            ?>
                                <a href="./?hashtag=<?= $hashtag ?>" class="badge badge-secondary font-weight-normal text-white" style="padding: .3em .5em"><?= $hashtag ?></a>
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
    </div>
</div>


<?php
include_once __DIR__ . '/../Common/foot.php';
