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

if (!isset($_GET['fee_id'])) {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$fee_id = $_GET['fee_id'];
$query = "SELECT * FROM fee_list WHERE id='$fee_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$fee_list = new Fee_List($result->fetch_assoc());
if ($fee_list->admin != 3) {
    header('Location: /member/mypage/admin/accounting/');
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
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/accounting/detail.php?fee_id=<?php echo $fee_list->id ?>"><?php echo $fee_list->name ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">集金リストの編集</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form method="post" action="./edit_fee_list.php" class="mb-4">
                    <div class="form-group">
                        <label for="fee-list">集金リスト名</label>
                        <input type="text" class="form-control" name="name" id="fee-list" aria-describedby="nameHelp" value="<?php echo $fee_list->name; ?>" required>
                        <small id="nameHelp" class="form-text text-muted">「団費」「演奏会費」のように入力してください。</small>
                    </div>
                    <div class="form-group">
                        <label for="deadline">期限</label>
                        <input type="date" name="deadline" class="form-control" id="deadline" value="<?php echo $fee_list->deadline; ?>" required>
                    </div>
                    <input type="hidden" name="fee_id" value="<?php echo $fee_list->id; ?>">
                    <button type="submit" class="btn btn-primary" name="submit">リストを更新</button>
                    <a class="btn btn-secondary" href="/member/mypage/admin/accounting/detail.php?fee_id=<?php echo $fee_list->id ?>" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
