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
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

$PAGE_NAME = "プロフィール";
include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container">
    <h1>プロフィール</h1>
    <?php echo $user->get_name(); ?>
</div>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
