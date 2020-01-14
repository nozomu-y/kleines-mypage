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
            <table id="accountList" class="display">
                <thead>
                    <tr>
                        <th>学年</th>
                        <th>パート</th>
                        <th>氏名</th>
                        <th>滞納額</th>
                        <th>メールアドレス</th>
                        <th>パスワード</th>
                        <th>管理者権限</th>
                        <th>削除</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM members";
                    $result = $mysqli->query($query);
                    if (!$result) {
                        print('Query Failed : ' . $mysqli->error);
                        $mysqli->close();
                        exit();
                    }
                    while ($row = $result->fetch_assoc()) {
                        $account = new User($row);
                        echo '<tr>';
                        echo '<td>' . $account->grade . '</td>';
                        echo '<td>' . $account->part . '</td>';
                        echo '<td>' . $account->name . '</td>';
                        echo '<td></td>';
                        echo '<td>' . $account->name . '</td>';
                        echo '<td>' . $account->name . '</td>';
                        echo '<td>' . $account->name . '</td>';
                        echo '<td>' . $account->name . '</td>';
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
$script .= '$(document).ready( function () {
    $(\'#accountList\').DataTable();
} );';
$script .= '</script>';

?>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
