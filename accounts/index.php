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
        <div class="col-sm-4">
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
                    <div class="mt-4 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> Soprano</span>
                        <span class="mr-2"><i class="fas fa-circle text-danger"></i> Alto</span>
                        <span class="mr-2"><i class="fas fa-circle text-info"></i> Tenor</span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Bass
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
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
                    <!-- <div class="mt-4 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> Soprano</span>
                        <span class="mr-2"><i class="fas fa-circle text-danger"></i> Alto</span>
                        <span class="mr-2"><i class="fas fa-circle text-info"></i> Tenor</span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Bass
                        </span>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow mb-4">
                <div class="card-header">アカウント一覧</div>
                <div class="card-body">
                    <table id="accountList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">学年</th>
                                <th class="text-nowrap">パート</th>
                                <th class="text-nowrap">氏名</th>
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
                            $row_cnt = $result->num_rows;
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
        columnDefs: [{ "orderable": true, "orderDataType": "part", "targets": 0 }],
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


$query = "SELECT * FROM members WHERE part='S'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$sop_num = $result->num_rows;
$query = "SELECT * FROM members WHERE part='A'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$alt_num = $result->num_rows;
$query = "SELECT * FROM members WHERE part='T'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$ten_num = $result->num_rows;
$query = "SELECT * FROM members WHERE part='B'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$bas_num = $result->num_rows;

$script .= '<script>';
$script .= 'Chart.defaults.global.defaultFontFamily = "Noto Sans JP", \'sans-serif\';Chart.defaults.global.defaultFontColor = \'#858796\';';
$script .= 'var ctx = document.getElementById("partChart");';
$script .= 'var myPieChart = new Chart(ctx, {
        type: \'doughnut\',
        data: {
            labels: ["Soprano", "Alto", "Tenor", "Bass"],
            datasets: [{
                data: [' . $sop_num . ', ' . $alt_num . ', ' . $ten_num . ', ' . $bas_num . '],
                backgroundColor: [\'#f6c23e\', \'#e74a3b\', \'#36b9cc\', \'#1cc88a\'],
                hoverBackgroundColor: [\'#f6c23e\', \'#e74a3b\', \'#36b9cc\', \'#1cc88a\'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: \'#dddfeb\',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });';
$script .= '</script>';

$query = "SELECT grade FROM members GROUP BY grade";
$result = $mysqli->query($query);
if (!$result) {
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
}
$grade_list = [];
while ($row = $result->fetch_assoc()) {
    foreach ($row as $grade) {
        $query = "SELECT * FROM members WHERE grade=$grade";
        $result_1 = $mysqli->query($query);
        if (!$result_1) {
            print('Query Failed : ' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        // $grade_list = array_merge($grade_list, array($grade => $result_1->num_rows));
        $grade_list[$grade] = $result_1->num_rows;
    }
}
$script .= '<script>';
$script .= 'Chart.defaults.global.defaultFontFamily = "Noto Sans JP", \'sans-serif\';Chart.defaults.global.defaultFontColor = \'#858796\';';
$script .= 'var ctx = document.getElementById("gradeChart");';
$script .= 'var myPieChart = new Chart(ctx, {
        type: \'doughnut\',
        data: {
            labels: [';
$count = 0;
foreach ($grade_list as $key => $value) {
    if ($count != 0) {
        $script .= ', ';
    }
    $script .= '"' . $key . '"';
    $count++;
}
$script .= '],
            datasets: [{
                data: [';
$count = 0;
foreach ($grade_list as $key => $value) {
    if ($count != 0) {
        $script .= ', ';
    }
    $script .= '"' . $value . '"';
    $count++;
}
$script .= '],backgroundColor: [';
$count = 0;
foreach ($grade_list as $key => $value) {
    if ($count != 0) {
        $script .= ', ';
    }
    $script .= '"#4e73df"';
    $count++;
}
$script .= '],
                //hoverBackgroundColor: [\'#f6c23e\', \'#e74a3b\', \'#36b9cc\', \'#1cc88a\'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: \'#dddfeb\',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });';
$script .= '</script>';

?>


<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
