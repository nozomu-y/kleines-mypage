<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isManager() || $USER->isAccountant() || $USER->isCamp())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_GET['fee_id'])) {
    $accounting_id = $_GET['fee_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/admin/camp_accounting/');
    exit();
}

$accounting = new AccountingList($accounting_id);
if ($accounting->admin != 'CAMP') {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$PAGE_NAME = "合宿集金";
include_once __DIR__ . '/../../Common/head.php';
?>

<?php
if ($USER->isCamp()) {
?>
    <script>
        function getPaid(id, name, price) {
            var result = window.confirm(name + "さんの提出状況を既納に変更して、集金完了メールを送信します。");
            if (result) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = './change_status_paid.php';

                var form_fee_id = document.createElement('input');
                form_fee_id.type = 'hidden';
                form_fee_id.name = 'fee_id';
                form_fee_id.value = '<?= $accounting->accounting_id ?>';
                form.appendChild(form_fee_id);

                var form_price = document.createElement('input');
                form_price.type = 'hidden';
                form_price.name = 'price';
                form_price.value = price;
                form.appendChild(form_price);

                var form_user_id = document.createElement('input');
                form_user_id.type = 'hidden';
                form_user_id.name = 'user_id';
                form_user_id.value = id;
                form.appendChild(form_user_id);

                document.body.appendChild(form);

                form.submit();
            }
        }
    </script>
    <script>
        function getUnpaid(id, name) {
            var result = window.confirm(name + "さんの提出状況を未納に変更しますか？");
            if (result) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = './change_status_unpaid.php';

                var form_fee_id = document.createElement('input');
                form_fee_id.type = 'hidden';
                form_fee_id.name = 'fee_id';
                form_fee_id.value = '<?= $accounting->accounting_id ?>';
                form.appendChild(form_fee_id);

                var form_user_id = document.createElement('input');
                form_user_id.type = 'hidden';
                form_user_id.name = 'user_id';
                form_user_id.value = id;
                form.appendChild(form_user_id);

                document.body.appendChild(form);

                form.submit();
            }
        }
    </script>
<?php
}
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金記録</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./">合宿集金記録一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= $accounting->name ?>
                    </li>
                </ol>
            </nav>
            <?php
            if (isset($_SESSION['mypage_fee_status'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_account_name'] . '</strong>の集金状況を<strong>' . $_SESSION['mypage_fee_status'] . '</strong>に変更しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_fee_status']);
                unset($_SESSION['mypage_account_name']);
            }
            if (isset($_SESSION['mypage_update_fee'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '集金リストの情報を更新しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_update_fee']);
            }
            if (isset($_SESSION['mypage_add_subject'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '集金対象者を追加しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_add_subject']);
            }
            if (isset($_SESSION['mypage_change_price'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '集金金額を変更しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_change_price']);
            }
            if (isset($_SESSION['mypage_delete_subject'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '集金対象者を削除しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_delete_subject']);
            }
            if (isset($_SESSION['mypage_update_price'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_account_name'] . '</strong>の集金額を<strong>￥' . number_format($_SESSION['mypage_update_price']) . '</strong>に変更しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_update_price']);
                unset($_SESSION['mypage_account_name']);
            }
            ?>
            <div class="mb-4">
                <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">学年</th>
                            <th class="text-nowrap">パート</th>
                            <th class="text-nowrap">氏名</th>
                            <?php
                            if ($USER->isCamp()) {
                                echo '<th class="text-nowrap">変更</th>';
                            }
                            ?>
                            <th class="text-nowrap">提出状況</th>
                            <th class="text-nowrap">提出日時</th>
                            <th class="text-nowrap">集金金額</th>
                            <?php
                            if ($USER->isCamp()) {
                                echo '<th class="text-nowrap">編集</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, profiles.user_id, accounting_records.price, accounting_records.paid_cash, accounting_records.datetime FROM profiles INNER JOIN accounting_records ON profiles.user_id=accounting_records.user_id WHERE accounting_records.accounting_id=$accounting_id ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
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
                            $kana = $row['name_kana'];
                            $user_id = $row['user_id'];
                            $price = $row['price'];
                            $paid_cash = $row['paid_cash'];
                            $datetime = $row['datetime'];
                            if ($datetime != NULL && strtotime($datetime) != 0) {
                                $disabled_paid = "disabled";
                                $disabled_unpaid = "";
                                $status = "既納";
                                $datetime = date('Y/m/d H:i:s', strtotime($row['datetime']));
                            } else {
                                $disabled_paid = "";
                                $disabled_unpaid = "disabled";
                                $status = "未納";
                                $datetime = "";
                            }
                        ?>
                            <tr>
                                <td class="text-nowrap"><?= $grade ?></td>
                                <td class="text-nowrap"><?= $part ?></td>
                                <td class="text-nowrap"><span class="d-none"><?= $kana ?></span><?= $name ?></td>
                                <?php
                                if ($USER->isCamp()) {
                                ?>
                                    <td class="text-nowrap"><input type="button" id="paid_<?= $user_id ?>" name="paid" class="btn btn-secondary btn-sm" value="既納" Onclick="getPaid('<?= $user_id ?>','<?= $name ?>','<?= $price ?>');" <?= $disabled_paid ?>> <input type="button" id="unpaid_<?= $user_id ?>" name="unpaid" class="btn btn-secondary btn-sm" value="未納" Onclick="getUnpaid('<?= $user_id ?>','<?= $name ?>');" <?= $disabled_unpaid ?>></td>
                                <?php
                                }
                                ?>
                                <td class="text-nowrap"><?= $status ?></td>
                                <td class="text-nowrap"><?= $datetime ?></td>
                                <td class="text-nowrap text-right"><?= "￥" . number_format($price) ?></td>
                                <?php
                                if ($USER->isCamp()) {
                                ?>
                                    <td class="text-nowrap"><a href="./change_price/?id=<?= $user_id ?>&fee_id=<?= $accounting_id ?>" class="text-secondary"><u>編集</u></a></td>
                                <?php
                                }
                                ?>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
            if ($USER->isCamp()) {
            ?>
                <a class="btn btn-primary mb-4" href="./add_subject/?fee_id=<?= $accounting_id ?>" role="button">集金対象者の追加</a>
                <a class="btn btn-danger mb-4" href="./delete_subject/?fee_id=<?= $accounting_id ?>" role="button">集金対象者の削除</a>
            <?php
            }
            ?>
        </div>
        <div class="col-xl-3 col-sm-12">
            <div class="card shadow mb-4">
                <div class="card-header">提出率</div>
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
            <?php
            if ($USER->isCamp()) {
            ?>
                <form method="post">
                    <div class="list-group shadow mb-4">
                        <a href="./edit_accounting_list/?fee_id=<?= $accounting_id ?>" class="list-group-item list-group-item-action">集金リストの編集</a>
                        <a href="./change_price_multiple/?fee_id=<?= $accounting_id ?>" class="list-group-item list-group-item-action">集金金額の一括変更</a>
                        <button type="submit" name="delete" formaction="./delete_accounting_list.php" class="list-group-item list-group-item-action text-danger" value="<?= $accounting_id ?>" Onclick="return confirm('集金リスト「<?= $accounting->name ?>」を削除しますか？\n削除した場合、関連する全ての集金記録が削除されます。');">集金リストの削除</button>
                    </div>
                </form>
                <div class="card shadow mb-4">
                    <div class="card-header">集金リストを削除した場合...</div>
                    <div class="card-body">
                        <p>
                            削除した集金リストの全てのデータが削除されます。（提出状況・提出日時など）
                        </p>
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
if ($USER->isCamp()) {
    $script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [{ "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 7 },
            { "orderable": true, "orderDataType": "part", "targets": 1 },
            { type: "currency", targets: 6 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
} else {
    $script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [{ "orderable": true, "orderDataType": "part", "targets": 1 },
            { type: "currency", targets: 5 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
}

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

$query = "SELECT profiles.part, (SELECT COUNT(*) FROM accounting_records INNER JOIN profiles AS prof ON accounting_records.user_id=prof.user_id WHERE accounting_records.accounting_id=$accounting_id AND prof.part=profiles.part) AS all_cnt,(SELECT COUNT(*) FROM accounting_records INNER JOIN profiles AS prof ON accounting_records.user_id=prof.user_id WHERE accounting_records.accounting_id=$accounting_id AND prof.part=profiles.part AND accounting_records.datetime IS NOT NULL) AS paid_cnt FROM profiles GROUP BY profiles.part";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $all_cnt = $row['all_cnt'];
    $paid_cnt = $row['paid_cnt'];
    if ($all_cnt == 0) {
        $ratio[$row['part']] = 0;
    } else {
        $ratio[$row['part']] = round($paid_cnt / ($all_cnt), 3) * 100;
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
                data: [' . $ratio['S'] . ', ' . $ratio['A'] . ', ' . $ratio['T'] . ', ' . $ratio['B'] . '],
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
                        + " %";
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
                        min: 0,
                        max: 100
                    }
                }]
            }
        },
    });';
$script .= '</script>';


?>



<?php
include_once __DIR__ . '/../../Common/foot.php';
