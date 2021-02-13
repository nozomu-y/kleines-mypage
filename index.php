<?php
require __DIR__ . '/Common/init_page.php';
include_once __DIR__ . '/Common/head.php';
?>

<div class="container-fluid">
    <!-- <h1 class="h3 text-gray-800 mb-4">Home</h1> -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary mb-1">個別会計総額</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $USER->get_individual_accounting_total(); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger mb-1">滞納額</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $USER->get_delinquent(); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-yen-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4 col-sm-6">
            <?php
            $query = "SELECT * FROM accounting_records WHERE user_id='$USER->id'";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $unpaid = 0;
            $paid = 0;
            while ($row = $result->fetch_assoc()) {
                $price = $row['price'];
                $datetime = $row['datetime'];
                if ($datetime == null) {
                    $unpaid += $price;
                } else {
                    $paid += $price;
                }
            }
            ?>
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">集金記録</h6>
                </div>
                <!-- Card Body -->
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
                        <canvas id="myPieChart" width="516" height="506" class="chartjs-render-monitor" style="display: block; height: 253px; width: 258px;"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> 既納 ￥ <?php echo number_format($paid); ?>
                        </span>
                        <br>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> 未納 ￥ <?php echo number_format($unpaid); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">団員名簿</h6>
                </div>
                <div class="card-body">
                    <p>
                        最新の団員名簿をダウンロードできます。
                    </p>
                    <a href="./download/memberlist_download.php">ダウンロード</a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">開発者募集中</h6>
                </div>
                <div class="card-body">
                    <p>
                        マイページの開発に協力してくれる方を募集しています。<br>Web管理人以外でも大丈夫です。
                    </p>
                    <a href="https://github.com/nozomu-y/kleines-mypage" target="_blank"><img class="img-fluid" src="https://gh-card.dev/repos/nozomu-y/kleines-mypage.svg?fullname="></a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
$script = '<script>';
$script .= 'Chart.defaults.global.defaultFontFamily = "Noto Sans JP", \'sans-serif\';Chart.defaults.global.defaultFontColor = \'#858796\';';
$script .= 'var ctx = document.getElementById("myPieChart");';
$script .= 'var myPieChart = new Chart(ctx, {
        type: \'doughnut\',
        data: {
            labels: ["既納", "未納"],
            datasets: [{
                data: [' . $paid . ', ' . $unpaid . '],
                backgroundColor: [\'#4e73df\', \'#e74a3b\'],
                hoverBackgroundColor: [\'#4e73df\', \'#e74a3b\'],
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
include_once __DIR__ . '/Common/foot.php';
