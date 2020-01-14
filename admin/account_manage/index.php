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

if ($user->admin != 1 && $user->admin != 2 && $user->adnin != 3) {
    header('Location: /member/mypage/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">アカウント管理</h1>
    <div class="row">
        <div class="col-sm-12">
            <table id="accountList" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-nowrap">学年</th>
                        <th class="text-nowrap">パート</th>
                        <th class="text-nowrap">氏名</th>
                        <th class="text-nowrap">滞納額</th>
                        <th class="text-nowrap">メールアドレス</th>
                        <th class="text-nowrap">パスワード</th>
                        <th class="text-nowrap">管理者権限</th>
                        <th class="text-nowrap">削除</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM members ORDER BY grade ASC, CASE WHEN part LIKE 'S' THEN 1 WHEN part LIKE 'A' THEN 2 WHEN part LIKE 'T' THEN 3 WHEN part LIKE 'B' THEN 4 END ASC, kana ASC";
                    $result = $mysqli->query($query);
                    if (!$result) {
                        print('Query Failed : ' . $mysqli->error);
                        $mysqli->close();
                        exit();
                    }
                    while ($row = $result->fetch_assoc()) {
                        $account = new User($row);
                        echo '<tr>';
                        echo '<td class="text-nowrap">' . $account->grade . '</td>';
                        echo '<td class="text-nowrap">' . $account->part . '</td>';
                        echo '<td class="text-nowrap"><span class="d-none">' . $account->kana . '</span>' . $account->name . '</td>';
                        echo '<td class="text-nowrap"></td>';
                        echo '<td class="text-nowrap">' . $account->email . '</td>';
                        echo '<td class="text-nowrap">**********</td>';
                        echo '<td class="text-nowrap">' . $account->admin . '</td>';
                        echo '<td class="text-nowrap"></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php
$script = '<script>';
// $script .= '$(document).ready( function () {
//     $(\'#accountList\').DataTable({
//         // responsive: true,
//         fixedHeader: true
//     });
// } );';
$script .= '$(document).ready(function() {
    $("#accountList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        scrollX: true,
        fixedHeader: true
    }); 
});';
$script .= '</script>';


?>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
