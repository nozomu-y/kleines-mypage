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

if (!($user->admin == 1)) {
    header('Location: /member/mypage/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">アカウント管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/account_manage/">アカウント管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page">認証ログ</li>
                </ol>
            </nav>
            <?php
            //$log = file_get_contents("../core/logs.log");
            $filename = "/home/chorkleines/www/member/mypage/Core/auth.log";
            $lines = file($filename);
            $lines = array_reverse($lines);
            foreach ($lines as $line) {
                echo nl2br($line);
            }
            //echo nl2br($log);
            ?>
        </div>
        <div class="col-xl-3 col-sm-12">

        </div>
    </div>
</div>


<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
