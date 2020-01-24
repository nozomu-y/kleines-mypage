<?php
ob_start();
session_start();
if (!isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/login/');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

if (!($user->admin == 1 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/accounting/">集金リスト一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page">集金リストの追加</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form method="post" action="./add.php" class="mb-4">
                    <div class="form-group">
                        <label for="fee-list">集金リスト名</label>
                        <input type="text" class="form-control" name="name" id="fee-list" aria-describedby="nameHelp" required>
                        <small id="nameHelp" class="form-text text-muted">「団費」「演奏会費」のように入力してください。</small>
                    </div>
                    <div class="form-group">
                        <label for="deadline">期限</label>
                        <input type="date" name="deadline" class="form-control" id="deadline" required>
                    </div>
                    <div class="form-group">
                        <label for="price">金額</label>
                        <input type="number" name="price" class="form-control" id="price" required>
                        <small id="nameHelp" class="form-text text-muted">設定後、個別に金額を変更することも可能です。</small>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">リストを追加</button>
                    <a class="btn btn-secondary" href="/member/mypage/admin/accounting/" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
