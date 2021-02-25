<?php
require __DIR__ . '/../../Common/init_page.php';

$PAGE_NAME = "掲示板";
include_once __DIR__ . '/../../Common/head.php';

$nav_title = "新規作成";
$title = "";
$hashtags = "";
$markdown = "";
$draft = "selected";
$release = "";
$bulletin_board_id = "";
if (isset($_GET['bulletin_board_id'])) {
    $bulletin_board_id = $_GET['bulletin_board_id'];
    $query = "SELECT * FROM bulletin_boards INNER JOIN bulletin_board_contents ON bulletin_boards.bulletin_board_id=bulletin_board_contents.bulletin_board_id WHERE bulletin_boards.bulletin_board_id=$bulletin_board_id";
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
    while ($row = $result->fetch_assoc()) {
        $markdown = $row['content'];
        $title = $row['title'];
        $nav_title = $title;
        $status = $row['status'];
    }
    $query = "SELECT hashtag FROM bulletin_board_hashtags WHERE bulletin_board_id=$bulletin_board_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    while ($row = $result->fetch_assoc()) {
        $hashtags .= "#" . $row['hashtag'] . " ";
    }
    if ($status == "DRAFT") {
        $draft = "selected";
        $release = "";
    } elseif ($status == "RELEASE") {
        $draft = "";
        $release = "selected";
    }
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css">
<link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/github-markdown.min.css">
<style>
    .markdown-body {
        padding: 20px;
        background-color: #FFFFFF;
    }
</style>
<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">掲示板</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../">掲示板</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $nav_title ?></li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12 mb-3">
            <form method="POST" action="./edit.php">
                <div class="form-group">
                    <label for="title">タイトル</label>
                    <input type="text" class="form-control" name="title" value="<?= $title ?>" required>
                </div>
                <div class="form-group">
                    <label for="hashtags">タグ</label>
                    <input type="text" class="form-control" name="hashtags" value="<?= $hashtags ?>" pattern="^#[^\s#,]+([\s,]#[^\s#,]+)*\s*$" placeholder="#引き継ぎ資料 #Kleines_Mypage">
                    <small id="hashtagslHelp" class="form-text text-muted">半角スペース区切りで入力してください。</small>
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-center my-5" id="spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <textarea class="form-control d-none" name="markdown" id="markdown" rows="10"><?= $markdown ?></textarea>
                </div>
                <div class="form-group">
                    <!-- <label for="exampleFormControlSelect1"></label> -->
                    <select class="form-control" name="status">
                        <option value="DRAFT" <?= $draft ?>>下書き</option>
                        <option value="RELEASE" <?= $release ?>>公開</option>
                    </select>
                </div>
                <input type="hidden" name="bulletin_board_id" value="<?= $bulletin_board_id ?>">
                <div class="form-group">
                    <button class="btn btn-primary" type="submit" name="submit">保存する</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/prism.min.js"></script>

<script>
    window.onload = function() {
        var maxHeight = (document.getElementById("content").clientHeight - 300) + "px";
        console.log(maxHeight);
        var easymde = new EasyMDE({
            sideBySideFullscreen: false,
            spellChecker: false,
            element: document.getElementById("markdown"),
            maxHeight: maxHeight,
            syncSideBySidePreviewScroll: true,
            previewClass: "markdown-body",
            toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "link", "image", "|", "preview", "side-by-side", "|", "guide"],
            status: ["autosave", "lines", "words", "cursor", {
                className: "keystrokes",
                defaultValue: function(el) {
                    var md = document.getElementById('markdown');
                    el.innerHTML = md.innerText.length + " letters";
                },
                onUpdate: function(el) {
                    var text = easymde.value();
                    text = text.replace("\r\n", "a");
                    text = text.replace("\n", "a");
                    el.innerHTML = text.length + " letters";
                }
            }]
        });
        document.getElementById("spinner").remove();
    }
</script>

<?php
include_once __DIR__ . '/../../Common/foot.php';
