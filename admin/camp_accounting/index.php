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

if (!($user->admin == 1 || $user->admin == 2 || $user->admin == 3 || $user->admin == 5)) {
    header('Location: /member/mypage/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">合宿集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <form method="post">
                <div class="mb-4">
                    <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">集金リスト</th>
                                <th class="text-nowrap">期限</th>
                                <th class="text-nowrap">金額</th>
                                <th class="text-nowrap">集金率</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM fee_list ORDER BY deadline DESC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            while ($row = $result->fetch_assoc()) {
                                $fee_list = new Fee_List($row);
                                if ($fee_list->admin == 5) {
                                    echo '<tr>';
                                    echo '<td class="text-nowrap"><a href="./detail.php?fee_id=' . $fee_list->id . '" class="text-secondary"><u>' . $fee_list->name . '</u></a></td>';
                                    echo '<td class="text-nowrap">' . $fee_list->get_deadline() . '</td>';
                                    echo '<td class="text-nowrap text-right">' . $fee_list->get_price() . '</td>';
                                    echo '<td class="text-nowrap text-right">' . $fee_list->get_paid_ratio() . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <?php
            if ($user->admin == 1 || $user->admin == 5) {
            ?>
                <a class="btn btn-primary mb-4" href="/member/mypage/admin/camp_accounting/add_fee_list/" role="button">集金リストの追加</a>
            <?php
            }
            ?>
        </div>
        <div class="col-xl-3 col-sm-12">
            <?php
            if ($user->admin == 1 || $user->admin == 5) {
            ?>
                <div class="card shadow mb-4">
                    <div class="card-header">ログ</div>
                    <div class="card-body">
                        <p>このページで行われる操作は全てログとして残ります。</p>
                        <a href="./camp_accounting_log.php">ログを閲覧</a>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<?php
$script = '<script>';
$script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [{ "orderable": false, "targets": 0 },
            { type: "currency", targets: 2 }],
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


?>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');