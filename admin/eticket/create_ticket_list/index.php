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
    <h1 class="h3 text-gray-800 mb-4">電子チケット</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/eticket/">チケット一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page">チケット作成</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form method="post" action="./execute.php" class="mb-4">
                    <div class="form-group">
                        <label for="name">チケット名</label>
                        <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" placeholder="2020年度演奏会" required>
                    </div>
                    <div class="form-group">
                        <label for="date">日付</label>
                        <input type="date" name="date" class="form-control" id="date" required>
                    </div>
                    <div class="form-group">
                        <label for="open_time">開場時間</label>
                        <input type="time" name="open_time" class="form-control" id="open_time" required>
                    </div>
                    <div class="form-group">
                        <label for="start_time">開演時間</label>
                        <input type="time" name="start_time" class="form-control" id="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="place">場所</label>
                        <input type="text" name="place" class="form-control" id="place" required>
                    </div>
                    <div class="form-group">
                        <label for="ticket_price">当日価格</label>
                        <input type="number" name="ticket_price" class="form-control" id="ticket_price" required>
                    </div>
                    <div class="form-group">
                        <label for="pre_ticket_price">前売価格</label>
                        <input type="number" name="pre_ticket_price" class="form-control" id="pre_ticket_price" required>
                    </div>
                    <div class="form-group">
                        <label for="max_num">最大発行枚数</label>
                        <input type="number" name="max_num" class="form-control" id="max_num" required>
                    </div>
                    <div class="form-group">
                        <label for="start_num">開始番号</label>
                        <input type="number" name="start_num" class="form-control" id="start_num" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">チケットを作成</button>
                    <a class="btn btn-secondary" href="/member/mypage/admin/eticket/" role="button">キャンセル</a>
                </form>
            </div>
        </div>
        <div class="col-xl-3 col-sm-12">
        </div>
    </div>
</div>

<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
