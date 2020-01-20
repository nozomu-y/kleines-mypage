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

if (isset($_GET['account_id'])) {
    $account_id = $_GET['account_id'];
} else {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$query = "SELECT * FROM members WHERE id=$account_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$account = new User($result->fetch_assoc());

if (isset($_GET['list_id'])) {
    $list_id = $_GET['list_id'];
} else {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$query = "SELECT * FROM individual_accounting_$account->id WHERE id=$list_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$individual = new Individual_Accounting($result->fetch_assoc());



include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">個別会計管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/individual_accounting/">個別会計管理</a></li>
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/individual_accounting/detail.php?account_id=<?php echo $account->id; ?>"><? echo $account->get_name(); ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $individual->name; ?></li>
                </ol>
            </nav>
            <div class="mb-4">
                <form action="edit_" method="post"></form>
            </div>
        </div>
    </div>
</div>


?>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
