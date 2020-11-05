<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 2 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
$PAGE_NAME = "アカウント管理";
include_once __DIR__ . '/../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">アカウント管理</h1>
    <div class="row">
        <div class="col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./">アカウント管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page">アカウントの追加</li>
                </ol>
            </nav>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <p>
                        以下の形式（CSV形式）で入力して下さい。<br>
                        19,S,山田,太郎,カナ,example@gmail.com<br>
                        改行する事で複数同時に追加できます。<br>
                        空白や最後の行の改行は入れないでください。
                    </p>
                    <form method="post" action="./confirm.php" class="mt-2">
                        <div class="form-group">
                            <textarea class="form-control" rows="10" placeholder="Grade,Part,Last-name,First-name,kana,email" name="csv"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">アカウントを追加</button>
                        <a class="btn btn-secondary" href="../" role="button">キャンセル</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
include_once __DIR__ . '/../../../Common/foot.php';
