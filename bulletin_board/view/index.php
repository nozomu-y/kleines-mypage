<?php
require __DIR__ . '/../../Common/init_page.php';

$PAGE_NAME = "掲示板";
include_once __DIR__ . '/../../Common/head.php';

if (!isset($_GET['bulletin_board_id'])) {
    header('Location: ' . MYPAGE_ROOT . '/bulletin_board/');
    exit();
}

$bulletin_board_id = $_GET['bulletin_board_id'];
$query = "SELECT * FROM bulletin_boards INNER JOIN bulletin_board_contents ON bulletin_boards.bulletin_board_id=bulletin_board_contents.bulletin_board_id WHERE bulletin_boards.bulletin_board_id=$bulletin_board_id";
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
}
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
            <li class="breadcrumb-item"><a href="../">掲示板</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header py-3">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="font-weight-bold text-primary" style="margin: auto 0;"><?= $title ?></h6>
                        <?php
                        if ($user_id == $USER->id) {
                        ?>
                            <a class="btn btn-outline-secondary btn-sm" href="../edit/?bulletin_board_id=<?= $bulletin_board_id ?>"">編集</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class=" card-body markdown-body">
                                <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/prism.min.js"></script>

    <?php
    include_once __DIR__ . '/../../Common/foot.php';
