<?php
require __DIR__ . '/../../Common/init_page.php';

$PAGE_NAME = "掲示板";
include_once __DIR__ . '/../../Common/head.php';

if (!isset($_GET['bulletin_board_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

$bulletin_board_id = $_GET['bulletin_board_id'];
$query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime AS edited, bulletin_boards.title, bulletin_boards.status, bulletin_board_contents.content, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, bulletin_boards.user_id, (SELECT MIN(datetime) FROM bulletin_board_contents WHERE bulletin_board_id=bulletin_boards.bulletin_board_id) AS created, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_board_contents.user_id=profiles.user_id INNER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.bulletin_board_id=$bulletin_board_id";
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
    $name = $row['name'];
    $hashtags = $row['hashtags'];
    $edited = $row['edited'];
    $edited = date('Y/m/d H:i', strtotime($edited));
    $created = $row['created'];
    $created = date('Y/m/d H:i', strtotime($created));
    $name = $row['name'];
}
$hashtags = explode(" ", $hashtags);


require __DIR__ . '/../../vendor/autoload.php';
$Parsedown = new ParsedownExtra();
$Parsedown->setBreaksEnabled(true);
$Parsedown->setSafeMode(true);
$content = $Parsedown->text($markdown);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css">
<link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/github-markdown.min.css">

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">掲示板</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php
            if ($status == 'RELEASE') {
            ?>
                <li class="breadcrumb-item"><a href="../">掲示板</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
            <?php
            } elseif ($status == 'DRAFT') {
            ?>
                <li class="breadcrumb-item"><a href="../">掲示板</a></li>
                <li class="breadcrumb-item"><a href="../draft">下書き</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
            <?php
            }
            ?>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header py-3">
                    <div class="d-flex w-100 justify-content-between">
                        <div>
                            <span class="font-weight-bold text-primary h6" style="margin: auto 0;"><?= $title ?></span>
                        </div>
                        <div>
                            <?php
                            if ($user_id == $USER->id) {
                            ?>
                                <a class="btn btn-outline-secondary btn-sm ml-1 text-nowrap" href="../edit/?bulletin_board_id=<?= $bulletin_board_id ?>">編集</a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-none d-md-block">
                        <div class="d-flex w-100 justify-content-between text-secondary mb-2">
                            <div>
                                <?php
                                foreach ($hashtags as $hashtag) {
                                ?>
                                    <a href="./?hashtag=<?= $hashtag ?>" class="badge badge-secondary font-weight-normal text-white" style="padding: .3em .5em"><?= $hashtag ?></a>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="text-right">
                                <small><span class="text-nowrap"><i class="fas fa-user mr-1"></i><?= $name ?></span></small>
                                <br />
                                <small><span class="text-nowrap">作成日時：<?= $created ?></span></small>
                                <br>
                                <small><span class="text-nowrap">最終編集：<?= $edited ?></span></small>
                            </div>

                        </div>
                    </div>
                    <div class="d-block d-md-none text-secondary mb-2">
                        <div>
                            <?php
                            foreach ($hashtags as $hashtag) {
                            ?>
                                <a href="./?hashtag=<?= $hashtag ?>" class="badge badge-secondary font-weight-normal text-white" style="padding: .3em .5em"><?= $hashtag ?></a>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="text-right">
                            <small><span class="text-nowrap"><i class="fas fa-user mr-1"></i><?= $name ?></span></small>
                            <br />
                            <small><span class="text-nowrap">作成日時：<?= $created ?></span></small>
                            <br>
                            <small><span class="text-nowrap">最終編集：<?= $edited ?></span></small>
                        </div>

                    </div>
                    <div class="markdown-body">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/prism.min.js"></script>

<?php
include_once __DIR__ . '/../../Common/foot.php';
