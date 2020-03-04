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

$PAGE_NAME = "セッション管理";
include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800">セッション管理</h1>
    <div class="row">
        <div class="col-sm-12">
            <div class="mb-4">
                <table id="sessionList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">ログイン日時</th>
                            <th class="text-nowrap">プラットフォーム</th>
                            <th class="text-nowrap">ブラウザ</th>
                            <th class="text-nowrap"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM auto_login ORDER BY datetime DESC WHERE id = '$user->id'";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $login_datetime = $row['datetime'];
                            $login_platform = $row['device'];
                            $login_browser = $row['browser'];
                            $login_token = $row['token'];
                            echo '<tr>';
                            echo '<td class="text-nowrap">' . $login_datetime . '</td>';
                            echo '<td class="text-nowrap">' . $login_platform . '</td>';
                            echo '<td class="text-nowrap">' . $login_browser . '</td>';
                            echo '<td class="text-nowrap"><button type="submit" name="delete" class="btn btn-outline-white btn-rounded btn-sm px-2" formaction="./delete_session.php" value="' . $login_token . '"><i class="far fa-trash-alt mt-0"></i></button></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-3 col-sm-12">
        </div>
    </div>
</div>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
