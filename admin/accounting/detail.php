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

if (isset($_GET['fee_id'])) {
    $id = $_GET['fee_id'];
} else {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$query = "SELECT * FROM fee_list WHERE id=$id";
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
    <h1 class="h3 text-gray-800 mb-4">集金記録</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <!-- <h2 class="h5 text-gray-800 mb-4"><? echo $fee_list->name; ?></h2> -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/accounting/">集金記録一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><? echo $fee_list->name; ?></li>
                </ol>
            </nav>
            <div class="mb-4">
                <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">学年</th>
                            <th class="text-nowrap">パート</th>
                            <th class="text-nowrap">氏名</th>
                            <th class="text-nowrap">変更</th>
                            <th class="text-nowrap">提出状況</th>
                            <th class="text-nowrap">提出日時</th>
                            <th class="text-nowrap">金額</th>
                            <th class="text-nowrap">編集</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM members ORDER BY CASE WHEN part LIKE 'S' THEN 1 WHEN part LIKE 'A' THEN 2 WHEN part LIKE 'T' THEN 3 WHEN part LIKE 'B' THEN 4 END ASC, grade ASC, kana ASC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        $row_cnt = $result->num_rows;
                        while ($row = $result->fetch_assoc()) {
                            $account = new User($row);
                            $query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_list->id";
                            $result_1 = $mysqli->query($query);
                            if (!$result_1) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            if ($result_1->num_rows != 0) {
                                // if the list exists
                                $fee = new Fee($result_1->fetch_assoc());
                                if ($fee->paid()) {
                                    $disabled_paid = "disabled";
                                    $disabled_unpaid = "";
                                } else {
                                    $disabled_paid = "";
                                    $disabled_unpaid = "disabled";
                                }
                                echo '<tr>';
                                echo '<td class="text-nowrap">' . $account->grade . '</td>';
                                echo '<td class="text-nowrap">' . $account->get_part() . '</td>';
                                echo '<td class="text-nowrap"><span class="d-none">' . $account->kana . '</span>' . $account->name . '</td>';
                                echo '<td class="text-nowrap"><input type="button" id="paid_' . $id_u . '" name="paid" class="btn btn-primary btn-sm" value="既納" Onclick="getPaid(\'' . $account->id . '\',\'' . $account->get_name() . '\',\'' . $account->get_individual_accounting_total() . '\',\'' . $fee->price . '\');" ' . $disabled_paid . '> <input type="button" id="unpaid_' . $account->id . '" name="unpaid" class="btn btn-primary btn-sm" value="未納" Onclick="getUnpaid(\'' . $account->id . '\',\'' . $account->get_name() . '\');" ' . $disabled_unpaid . '></td>';
                                echo '<td class="text-nowrap">' . $fee->get_status() . '</td>';
                                echo '<td class="text-nowrap">' . $fee->get_submission_time() . '</td>';
                                echo '<td class="text-nowrap text-right">' . $fee->get_price() . '</td>';
                                echo '<td class="text-nowrap"><a href="./edit_price.php?id=' . $account->id . '&fee_id=' . $fee_list->id . '">編集</a></td>';
                                echo '</tr>';
                            }
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
$script = '<script>';
$script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [{ "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 7 },
            { "orderable": true, "orderDataType": "part", "targets": 1 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
$script .= '$.fn.dataTable.ext.order["part"] = function(settings, col) {
            return this.api().column(col, {
                order: "index"
            }).nodes().map(function(td, i) {
                if (!$(td).html()) return 0;
                if ($(td).html() == "Soprano") {
                    return "b";
                } else if ($(td).html() == "Alto") {
                    return "c";
                } else if ($(td).html() == "Tenor") {
                    return "d";
                } else if ($(td).html() == "Bass") {
                    return "e";
                } else {
                    return "a";
                }
            });
        }';
$script .= '</script>';


?>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
