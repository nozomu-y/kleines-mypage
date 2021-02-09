<?php
require __DIR__ . '/../Common/init_page.php';

$PAGE_NAME = "アカウント一覧";
include_once __DIR__ . '/../Common/head.php';
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
                <table id="accountList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">学年</th>
                            <th class="text-nowrap">パート</th>
                            <th class="text-nowrap">氏名</th>
                            <th class="text-nowrap">ステータス</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, users.status FROM profiles INNER JOIN users ON profiles.user_id=users.user_id ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $grade = $row['grade'];
                            if ($row['part'] == 'S') {
                                $part = "Soprano";
                            } else if ($row['part'] == 'A') {
                                $part = "Alto";
                            } else if ($row['part'] == 'T') {
                                $part = "Tenor";
                            } else if ($row['part'] == 'B') {
                                $part = "Bass";
                            }
                            $name = $row['last_name'] . $row['first_name'];
                            $name_kana = $row['name_kana'];
                            $status = $row['status'];
                            if (strcmp($status, "RESIGNED")) {
                                if (!strcmp($status, "PRESENT")) {
                                    $status = "在団";
                                } else {
                                    $status = "休団";
                                }
                                echo '<tr>';
                                echo '<td class="text-nowrap">' . $grade . '</td>';
                                echo '<td class="text-nowrap">' . $part . '</td>';
                                echo '<td class="text-nowrap"><span class="d-none">' . $name_kana . '</span>' . $name . '</td>';
                                echo '<td class="text-nowrap">' . $status . '</td>';
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
    $("#accountList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [{ "orderable": true, "orderDataType": "part", "targets": 1 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
        dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>",
        //    "<\'row\'<\'col-sm-12 mb-2 right\'B>>",
        // buttons: [ "excel", "pdf", "copy" ],
        // dom: "Blfrtip"
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


$query = "SELECT profiles.part, COUNT(*) FROM profiles INNER JOIN users ON profiles.user_id=users.user_id WHERE users.status != 'RESIGNED' group by profiles.part";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    if ($row['part'] == 'S') {
        $sop_num = $row['COUNT(*)'];
    } else if ($row['part'] == 'A') {
        $alt_num = $row['COUNT(*)'];
    } else if ($row['part'] == 'T') {
        $ten_num = $row['COUNT(*)'];
    } else if ($row['part'] == 'B') {
        $bas_num = $row['COUNT(*)'];
    }
}

$script .= '<script>';
$script .= 'Chart.defaults.global.defaultFontFamily = "Noto Sans JP", "sans-serif";Chart.defaults.global.defaultFontColor = "#858796";';
$script .= 'var ctx = document.getElementById("partChart");';
$script .= 'var myPieChart = new Chart(ctx, {
        type: "horizontalBar",
        data: {
            labels: ["Soprano", "Alto", "Tenor", "Bass"],
            datasets: [{
                data: [' . $sop_num . ', ' . $alt_num . ', ' . $ten_num . ', ' . $bas_num . '],
                backgroundColor: ["#f6c23e", "#e74a3b", "#36b9cc", "#1cc88a"],
                hoverBackgroundColor: ["#f6c23e", "#e74a3b", "#36b9cc", "#1cc88a"],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: "#6e707e",
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: "#dddfeb",
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function (tooltipItem, data){
                        return data.datasets[0].data[tooltipItem.index]
                        + "人";
                    }
                }
            },
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    ticks: {
                        beginAtZero: true,
                    }
                }]
            }
        },
    });';
$script .= '</script>';

$query = "SELECT profiles.grade, COUNT(*) FROM profiles INNER JOIN users ON profiles.user_id=users.user_id WHERE users.status != 'RESIGNED' group by profiles.grade";
$result = $mysqli->query($query);
if (!$result) {
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
}
$grade_list = [];
while ($row = $result->fetch_assoc()) {
    $grade_list[$row['grade']] = $row['COUNT(*)'];
}
$script .= '<script>';
$script .= 'Chart.defaults.global.defaultFontFamily = "Noto Sans JP", "sans-serif";Chart.defaults.global.defaultFontColor = "#858796";';
$script .= 'var ctx = document.getElementById("gradeChart");';
$script .= 'var myPieChart = new Chart(ctx, {
        type: "horizontalBar",
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
$script .= '],hoverBackgroundColor: [';
$count = 0;
foreach ($grade_list as $key => $value) {
    if ($count != 0) {
        $script .= ', ';
    }
    $script .= '"#4e73df"';
    $count++;
}
$script .= '],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: "#6e707e",
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: "#dddfeb",
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function (tooltipItem, data){
                        return data.datasets[0].data[tooltipItem.index]
                        + "人";
                    }
                }
            },
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    ticks: {
                        beginAtZero: true,
                    }
                }]
            }
        },
    });';
$script .= '</script>';

?>


<?php
include_once __DIR__ . '/../Common/foot.php';
