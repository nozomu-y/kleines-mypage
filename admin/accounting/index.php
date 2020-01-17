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

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金記録</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <div class="mb-4">
                <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">集金名</th>
                            <th class="text-nowrap">期限</th>
                            <th class="text-nowrap text-right">金額</th>
                            <th class="text-nowrap">削除</th>
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
                        $row_cnt = $result->num_rows;
                        while ($row = $result->fetch_assoc()) {
                            $fee_list = new Fee_List($row);
                            if ($fee_list->admin == 3) {
                                echo '<tr>';
                                echo '<td class="text-nowrap"><a href="./detail.php?fee_id=' . $fee_list->id . '" class="text-secondary"><u>' . $fee_list->name . '</u></a></td>';
                                echo '<td class="text-nowrap">' . $fee_list->get_deadline() . '</td>';
                                echo '<td class="text-nowrap text-right">' . $fee_list->get_price() . '</td>';
                                echo '<td class="text-nowrap"><button type="submit" name="delete" formaction="/member/mypage/admin/accounting/delete_fee_list.php" class="btn btn-danger btn-sm" value="' . $fee_list->id . '" Onclick="return confirm(\'' . $fee_list->name . 'を削除しますか？\');">削除</button></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- <div class="list-group mb-4">
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-0">団費集金(10~12月分)</h6>
                        <small>2020/01/01</small>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-0">団費集金(10~12月分)</h6>
                        <small>2020/01/01</small>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-0">団費集金(10~12月分)</h6>
                        <small>2020/01/01</small>
                    </div>
                </a>
            </div> -->
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
        columnDefs: [{ "orderable": false, "targets": 0 },
            { "orderable": false, "targets": 3 }],
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
