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
            <div class="mb-4 mt-3">
                <form method="POST">
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
                            $query = "SELECT * FROM auto_login WHERE id = '$user->id' ORDER BY datetime DESC";
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
                                echo '<td class="text-nowrap"><button type="submit" name="delete" class="btn btn-danger rounded-circle btn-sm" formaction="./delete_session.php" value="' . $login_token . '"><i class="far fa-trash-alt mt-0"></i></button></td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <div class="col-xl-3 col-sm-12">
        </div>
    </div>
</div>

<?php
$script = '<script>';
$script .= '$(document).ready(function() {
    $("#accountList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [';
$script .= '{ "orderable": false, "targets": 3 }';
$script .= '],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
$script .= '</script>';


include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
