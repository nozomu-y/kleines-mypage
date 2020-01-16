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

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <!-- <h1 class="h3 text-gray-800 mb-4">アカウント一覧</h1> -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">パート比率</div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                        <canvas id="partChart" width="516" height="506" class="chartjs-render-monitor" style="display: block; height: 253px; width: 258px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header">学年比率</div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                        <canvas id="gradeChart" width="516" height="506" class="chartjs-render-monitor" style="display: block; height: 253px; width: 258px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="mb-4">
                <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">集金名</th>
                            <th class="text-nowrap">金額</th>
                            <th class="text-nowrap">期限</th>
                            <th class="text-nowrap">提出状況</th>
                            <th class="text-nowrap">提出日時</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM fee_list ORDER BY id ASC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('クエリーが失敗しました。' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $fee = new Fee($row);
                        }


                        while ($row = $result->fetch_assoc()) {
                            $account = new User($row);
                            echo '<tr>';
                            echo '<td class="text-nowrap">' . $account->grade . '</td>';
                            echo '<td class="text-nowrap">' . $account->get_part() . '</td>';
                            echo '<td class="text-nowrap"><span class="d-none">' . $account->kana . '</span>' . $account->name . '</td>';
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
$script = '<script>';
$script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        // columnDefs: [{ "orderable": true, "orderDataType": "part", "targets": 0 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
// $script .= '$.fn.dataTable.ext.order["part"] = function(settings, col) {
//             return this.api().column(col, {
//                 order: "index"
//             }).nodes().map(function(td, i) {
//                 if (!$(td).html()) return 0;
//                 if ($(td).html() == "Soprano") {
//                     return "b";
//                 } else if ($(td).html() == "Alto") {
//                     return "c";
//                 } else if ($(td).html() == "Tenor") {
//                     return "d";
//                 } else if ($(td).html() == "Bass") {
//                     return "e";
//                 } else {
//                     return "a";
//                 }
//             });
//         }';
$script .= '</script>';



?>


<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
