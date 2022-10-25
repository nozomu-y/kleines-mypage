<?php
require __DIR__ . '/Common/init_page.php';
include_once __DIR__ . '/Common/head.php';

$bulletin_boards = array();
$query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime, bulletin_boards.title, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id) AS views, (SELECT COUNT(*) FROM bulletin_board_views WHERE bulletin_board_id=bulletin_boards.bulletin_board_id AND user_id=$USER->id) AS user_views, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_boards.user_id=profiles.user_id LEFT OUTER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.status='RELEASE' ORDER BY bulletin_board_contents.datetime DESC LIMIT 5";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $bulletin_board = array();
    $bulletin_board = $row;
    $bulletin_board['datetime'] = date('Y/m/d H:i', strtotime($bulletin_board['datetime']));
    $bulletin_board['hashtags'] = explode(" ", $bulletin_board['hashtags']);
    if (isset($_GET['hashtag']) && !in_array($_GET['hashtag'], $bulletin_board['hashtags'])) continue;
    array_push($bulletin_boards, $bulletin_board);
}
?>

<div class="d-none d-md-block">
    <div class="container-fluid">
        <div class="row">
            <?php
            if (isset($_SESSION['mypage_edit_profile'])) {
                echo '<div class="col-sm-12">';
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo 'プロフィールを編集しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                echo '</div>';
                unset($_SESSION['mypage_edit_profile']);
            }
            if (isset($_SESSION['mypage_password_success'])) {
                echo '<div class="col-sm-12">';
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo 'パスワードを更新しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                echo '</div>';
                unset($_SESSION['mypage_password_success']);
            }
            ?>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary mb-1">個別会計総額</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $USER->get_individual_accounting_total() ?></div>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $USER->get_delinquent() ?></div>
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
<!--
            <div class="col-xl-4 col-sm-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">新着記事</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php
                            $border_top_0 = 'style="border-top: 0;"';
                            foreach ($bulletin_boards as $bulletin_board) {
                                $bulletin_board_id = $bulletin_board['bulletin_board_id'];
                                $title = $bulletin_board['title'];
                                $name = $bulletin_board['name'];
                                $datetime = $bulletin_board['datetime'];
                                $hashtags = $bulletin_board['hashtags'];
                                $views = $bulletin_board['views'];
                                $user_views = $bulletin_board['user_views'];
                                if ($user_views > 0) {
                                    $unread = "";
                                } else {
                                    $unread = "unread";
                                }
                            ?>
                                <a href="./bulletin_board/view/?bulletin_board_id=<?= $bulletin_board_id ?>" class="list-group-item list-group-item-action pt-1 <?= $unread ?>" <?= $border_top_0 ?>>
                                    <div class="text-right mb-2">
                                        <small>
                                            <span class="mr-2 text-nowrap"><i class="fas fa-user mr-1"></i><?= h($name) ?></span>
                                            <span class="text-nowrap"><?= $datetime ?></span>
                                        </small>
                                    </div>
                                    <h5 class="mb-0 text-truncate"><?= h($title) ?></h5>
                                </a>
                            <?php
                                $border_top_0 = '';
                            }
                            ?>
                            <a class="list-group-item list-group-item-action text-primary mb-1 text-right" href="./bulletin_board/" style="border-bottom: 0;">記事一覧<i class="fas fa-chevron-right ml-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
-->
            <!-- <div class="col-xl-4 col-sm-6">
                <?php
                // $query = "SELECT * FROM accounting_records WHERE user_id='$USER->id'";
                // $result = $mysqli->query($query);
                // if (!$result) {
                //     print('Query Failed : ' . $mysqli->error);
                //     $mysqli->close();
                //     exit();
                // }
                // $unpaid = 0;
                // $paid = 0;
                // while ($row = $result->fetch_assoc()) {
                //     $price = $row['price'];
                //     $datetime = $row['datetime'];
                //     if ($datetime == null) {
                //         $unpaid += $price;
                //     } else {
                //         $paid += $price;
                //     }
                // }
                ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">集金記録</h6>
                    </div>
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
                                <i class="fas fa-circle text-primary"></i> 既納 ￥ <?= number_format($paid) ?>
                            </span>
                            <br>
                            <span class="mr-2">
                                <i class="fas fa-circle text-danger"></i> 未納 ￥ <?= number_format($unpaid) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div> -->
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
</div>

<div class="d-block d-md-none">
    <div class="container-fluid">
        <div class="row">
            <?php
            if (isset($_SESSION['mypage_edit_profile'])) {
                echo '<div class="col-sm-12">';
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo 'プロフィールを編集しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                echo '</div>';
                unset($_SESSION['mypage_edit_profile']);
            }
            if (isset($_SESSION['mypage_password_success'])) {
                echo '<div class="col-sm-12">';
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo 'パスワードを更新しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                echo '</div>';
                unset($_SESSION['mypage_password_success']);
            }
            ?>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary mb-1">個別会計総額</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $USER->get_individual_accounting_total() ?></div>
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $USER->get_delinquent() ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-yen-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--
    <div class="card card-flush shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">新着記事</h6>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                <?php
                $bulletin_count = 0;
                $border_top_0 = 'style="border-top: 0;"';
                foreach ($bulletin_boards as $bulletin_board) {
                    if ($bulletin_count == 3) break;
                    $bulletin_board_id = $bulletin_board['bulletin_board_id'];
                    $title = $bulletin_board['title'];
                    $name = $bulletin_board['name'];
                    $datetime = $bulletin_board['datetime'];
                    $hashtags = $bulletin_board['hashtags'];
                    $views = $bulletin_board['views'];
                    $user_views = $bulletin_board['user_views'];
                    if ($user_views > 0) {
                        $unread = "";
                    } else {
                        $unread = "unread";
                    }
                    $bulletin_count++;

                ?>
                    <a href="./bulletin_board/view/?bulletin_board_id=<?= $bulletin_board_id ?>" class="list-group-item list-group-item-action pt-1 <?= $unread ?>" <?= $border_top_0 ?>>
                        <div class="text-right mb-2">
                            <small>
                                <span class="mr-2 text-nowrap"><i class="fas fa-user mr-1"></i><?= h($name) ?></span>
                                <span class="text-nowrap"><?= $datetime ?></span>
                            </small>
                        </div>
                        <h6 class="mb-0 text-truncate"><?= h($title) ?></h6>
                    </a>
                <?php
                    $border_top_0 = '';
                }
                ?>
                <a class="list-group-item list-group-item-action text-primary text-right" href="./bulletin_board/" style="border-bottom: 0;">記事一覧<i class="fas fa-chevron-right ml-2"></i></a>
            </div>
        </div>
    </div>
-->
    <div class="card card-flush shadow mb-4">
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

<?php
// $script = '<script>';
// $script .= 'Chart.defaults.global.defaultFontFamily = "Noto Sans JP", \'sans-serif\';Chart.defaults.global.defaultFontColor = \'#858796\';';
// $script .= 'var ctx = document.getElementById("myPieChart");';
// $script .= 'var myPieChart = new Chart(ctx, {
//         type: \'doughnut\',
//         data: {
//             labels: ["既納", "未納"],
//             datasets: [{
//                 data: [' . $paid . ', ' . $unpaid . '],
//                 backgroundColor: [\'#4e73df\', \'#e74a3b\'],
//                 hoverBackgroundColor: [\'#4e73df\', \'#e74a3b\'],
//                 hoverBorderColor: "rgba(234, 236, 244, 1)",
//             }],
//         },
//         options: {
//             maintainAspectRatio: false,
//             tooltips: {
//                 backgroundColor: "rgb(255,255,255)",
//                 bodyFontColor: "#858796",
//                 borderColor: \'#dddfeb\',
//                 borderWidth: 1,
//                 xPadding: 15,
//                 yPadding: 15,
//                 displayColors: false,
//                 caretPadding: 10,
//             },
//             legend: {
//                 display: false
//             },
//             cutoutPercentage: 80,
//         },
//     });';
// $script .= '</script>';

?>



<?php
include_once __DIR__ . '/Common/foot.php';
