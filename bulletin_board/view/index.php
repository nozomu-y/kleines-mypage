<?php
require __DIR__ . '/../../Common/init_page.php';

$PAGE_NAME = "掲示板";
include_once __DIR__ . '/../../Common/head.php';

if (!isset($_GET['bulletin_board_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

$bulletin_board_id = $_GET['bulletin_board_id'];
if (isset($_GET['datetime'])) {
    $datetime = $_GET['datetime'];
    $query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime AS edited, bulletin_boards.title, bulletin_boards.status, bulletin_board_contents.content, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, bulletin_boards.user_id, (SELECT MIN(datetime) FROM bulletin_board_contents WHERE bulletin_board_id=bulletin_boards.bulletin_board_id) AS created, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id!=bulletin_boards.user_id) AS views, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id=$USER->id) AS user_views, hashtags FROM bulletin_board_contents INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_boards.user_id=profiles.user_id LEFT OUTER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.bulletin_board_id=$bulletin_board_id AND bulletin_board_contents.datetime='$datetime'";
} else {
    $query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime AS edited, bulletin_boards.title, bulletin_boards.status, bulletin_board_contents.content, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, bulletin_boards.user_id, (SELECT MIN(datetime) FROM bulletin_board_contents WHERE bulletin_board_id=bulletin_boards.bulletin_board_id) AS created, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id!=bulletin_boards.user_id) AS views, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id=$USER->id) AS user_views, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_boards.user_id=profiles.user_id LEFT OUTER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.bulletin_board_id=$bulletin_board_id";
}
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
if ($result->num_rows == 0) {
    // error
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
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
    $views = $row['views'];
    $user_views = $row['user_views'];
}
$hashtags = explode(" ", $hashtags);

if ($status == 'DRAFT' && $user_id != $USER->id) {
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

$query = "INSERT INTO bulletin_board_views (bulletin_board_id, user_id, datetime) VALUES ($bulletin_board_id, $USER->id, now())";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
if ($user_id != $USER->id) {
    $views++;
}

require __DIR__ . '/../../vendor/autoload.php';
$Parsedown = new ParsedownExtra();
$Parsedown->setBreaksEnabled(true);
$Parsedown->setSafeMode(true);
$content = $Parsedown->text($markdown);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css">
<link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/github-markdown.min.css">
<link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/tagsinput.min.css">

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">掲示板</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php
            if (isset($_GET['datetime'])) {
                if ($status == 'RELEASE') {
            ?>
                    <li class="breadcrumb-item"><a href="../">掲示板</a></li>
                    <li class="breadcrumb-item"><a href="./?bulletin_board_id=<?= $bulletin_board_id ?>"><?= $title ?></a></li>
                    <li class="breadcrumb-item"><a href="./history/?bulletin_board_id=<?= $bulletin_board_id ?>">編集履歴</a></li>
                    <li class="breadcrumb-item text-truncate active" aria-current="page"><?= $edited ?></li>
                <?php
                } elseif ($status == 'DRAFT') {
                ?>
                    <li class="breadcrumb-item"><a href="../">掲示板</a></li>
                    <li class="breadcrumb-item"><a href="../?owner">自分の投稿</a></li>
                    <li class="breadcrumb-item"><a href="./?bulletin_board_id=<?= $bulletin_board_id ?>"><?= $title ?></a></li>
                    <li class="breadcrumb-item"><a href="./history/?bulletin_board_id=<?= $bulletin_board_id ?>">編集履歴</a></li>
                    <li class="breadcrumb-item text-truncate active" aria-current="page"><?= $edited ?></li>
                <?php
                }
            } else {
                if ($status == 'RELEASE') {
                ?>
                    <li class="breadcrumb-item"><a href="../">掲示板</a></li>
                    <li class="breadcrumb-item text-truncate active" aria-current="page"><?= $title ?></li>
                <?php
                } elseif ($status == 'DRAFT') {
                ?>
                    <li class="breadcrumb-item"><a href="../">掲示板</a></li>
                    <li class="breadcrumb-item"><a href="../?owner">自分の投稿</a></li>
                    <li class="breadcrumb-item text-truncate active" aria-current="page"><?= $title ?></li>
            <?php
                }
            }
            ?>
        </ol>
    </nav>
    <div class="d-none d-md-block">
        <div class="card mb-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
                <?php
                if (!isset($_GET['datetime'])) {
                ?>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-h fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <a href="../edit/?fork=<?= $bulletin_board_id ?>" class="dropdown-item" type="button"><i class="fas fa-code-branch mr-2"></i>この記事をもとに新規作成</a>
                            <a href="./download.php?bulletin_board_id=<?= $bulletin_board_id ?>" class="dropdown-item" type="button"><i class="fab fa-markdown mr-2"></i>Markdownをダウンロード</a>
                            <?php
                            if ($user_id == $USER->id) {
                            ?>
                                <div class="dropdown-divider"></div>
                                <a href="../edit/?bulletin_board_id=<?= $bulletin_board_id ?>" class="dropdown-item" type="button"><i class="fas fa-edit mr-2"></i>編集</a>
                                <a href="./history/?bulletin_board_id=<?= $bulletin_board_id ?>" class="dropdown-item" type="button"><i class="fas fa-history mr-2"></i>編集履歴</a>
                                <a class="dropdown-item text-danger" onclick="delete_bulletin_board();" type="button"><i class="fas fa-trash-alt mr-2"></i>削除</a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="card-body">
                <div class="d-flex w-100 justify-content-between text-secondary">
                    <div class="mt-auto">
                        <?php
                        foreach ($hashtags as $hashtag) {
                        ?>
                            <a href="../?hashtag=<?= $hashtag ?>" class="badge badge-secondary font-weight-normal text-white"><?= $hashtag ?></a>
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
                        <br>
                        <small><i class="fas fa-eye mr-1"></i><?= $views ?></span></small>
                    </div>
                </div>
                <hr style="border-top: 2px solid rgba(0,0,0,.1);">
                <div class="markdown-body">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-block d-md-none">
    <div class="card card-flush mb-3">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
            <?php
            if (!isset($_GET['datetime'])) {
            ?>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-h fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <a href="../edit/?fork=<?= $bulletin_board_id ?>" class="dropdown-item" type="button"><i class="fas fa-code-branch mr-2"></i>この記事をもとに新規作成</a>
                        <a href="./download.php?bulletin_board_id=<?= $bulletin_board_id ?>" class="dropdown-item" type="button"><i class="fab fa-markdown mr-2"></i>Markdownをダウンロード</a>
                        <?php
                        if ($user_id == $USER->id) {
                        ?>
                            <div class="dropdown-divider"></div>
                            <a href="../edit/?bulletin_board_id=<?= $bulletin_board_id ?>" class="dropdown-item" type="button"><i class="fas fa-edit mr-2"></i>編集</a>
                            <a href="./history/?bulletin_board_id=<?= $bulletin_board_id ?>" class="dropdown-item" type="button"><i class="fas fa-history mr-2"></i>編集履歴</a>
                            <a class="dropdown-item text-danger" onclick="delete_bulletin_board();" type="button"><i class="fas fa-trash-alt mr-2"></i>削除</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="card-body">
            <div class="text-secondary">
                <div class="text-right">
                    <small><span class="text-nowrap"><i class="fas fa-user mr-1"></i><?= $name ?></span></small>
                    <br />
                    <small><span class="text-nowrap">作成日時：<?= $created ?></span></small>
                    <br>
                    <small><span class="text-nowrap">最終編集：<?= $edited ?></span></small>
                    <br>
                    <small><span class="text-nowrap">閲覧数：<?= $views ?></span></small>
                </div>
                <div>
                    <?php
                    foreach ($hashtags as $hashtag) {
                    ?>
                        <a href="../?hashtag=<?= $hashtag ?>" class="badge badge-secondary font-weight-normal text-white"><?= $hashtag ?></a>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <hr style="border-top: 2px solid rgba(0,0,0,.1);">
            <div class="markdown-body">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<?php
if ($user_id == $USER->id) {
?>
    <form action="./delete.php" method="POST" id="delete">
        <input type="hidden" name="bulletin_board_id" value="<?= $bulletin_board_id ?>">
        <input type="hidden" name="delete">
    </form>
    <script>
        function delete_bulletin_board() {
            var ret = window.confirm("「<?= $title ?>」を削除しますか？過去の履歴も含め全てのデータが削除されます。");
            if (ret) {
                document.getElementById("delete").submit();
            }
        }
    </script>
<?php
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/prism.min.js"></script>

<?php
include_once __DIR__ . '/../../Common/foot.php';
