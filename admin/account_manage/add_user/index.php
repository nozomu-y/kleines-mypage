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

if (!($user->admin == 1 || $user->admin == 2)) {
    header('Location: /member/mypage/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">アカウント管理</h1>
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">アカウントの追加</h6>
                </div>
                <div class="card-body">
                    <p>
                        以下の形式（CSV形式）で入力して下さい。<br>
                        山田,太郎,カナ,19,example@gmail.com,S<br>
                        改行する事で複数同時に追加できます。<br>
                        空白や最後の行の改行は入れないでください。
                    </p>
                    <form method="post" action="./add_user.php" class="mb-4 mt-2">
                        <div class="form-group">
                            <textarea class="form-control" rows="10" placeholder="Last-name,First-name,kana,Grade,email,Part" name="csv"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">アカウントを追加</button>
                        <a class="btn btn-secondary" href="/member/mypage/admin/account_manage/" role="button">キャンセル</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
