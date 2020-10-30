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

if (isset($_GET['fee_id'])) {
    $id = $_GET['fee_id'];
} else {
    header('Location: /member/mypage/admin/camp_accounting/');
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

if ($fee_list->admin != 5) {
    header('Location: /member/mypage/admin/camp_accounting/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<?php
if ($user->admin == 1 || $user->admin == 5) {
?>
    <script>
        function getPaid(id, name, price) {
            var result = window.confirm(name + "さんの提出状況を既納に変更して、集金完了メールを送信します。");
            if (result) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/member/mypage/admin/camp_accounting/change_status_paid.php';

                var form_fee_id = document.createElement('input');
                form_fee_id.type = 'hidden';
                form_fee_id.name = 'fee_id';
                form_fee_id.value = '<?php echo $fee_list->id; ?>';
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
                form.action = '/member/mypage/admin/camp_accounting/change_status_unpaid.php';

                var form_fee_id = document.createElement('input');
                form_fee_id.type = 'hidden';
                form_fee_id.name = 'fee_id';
                form_fee_id.value = '<?php echo $fee_list->id; ?>';
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
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/camp_accounting/">合宿集金記録一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><? echo $fee_list->name; ?></li>
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
            if (isset($_SESSION['mypage_update_subject'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '集金対象者を更新しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_update_subject']);
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
                            if ($user->admin == 1 || $user->admin == 5) {
                                echo '<th class="text-nowrap">変更</th>';
                            }
                            ?>
                            <th class="text-nowrap">提出状況</th>
                            <th class="text-nowrap">提出日時</th>
                            <th class="text-nowrap">金額</th>
                            <?php
                            if ($user->admin == 1 || $user->admin == 5) {
                                echo '<th class="text-nowrap">編集</th>';
                            }
                            ?>
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
                                if ($account->status == 2 && !$fee->paid()) {
                                    echo '<tr class="table-danger">';
                                } else {
                                    echo '<tr>';
                                }
                                echo '<td class="text-nowrap">' . $account->grade . '</td>';
                                echo '<td class="text-nowrap">' . $account->get_part() . '</td>';
                                echo '<td class="text-nowrap"><span class="d-none">' . $account->kana . '</span>' . $account->name . '</td>';
                                if ($user->admin == 1 || $user->admin == 5) {
                                    echo '<td class="text-nowrap"><input type="button" id="paid_' . $id_u . '" name="paid" class="btn btn-secondary btn-sm" value="既納" Onclick="getPaid(\'' . $account->id . '\',\'' . $account->name . '\',\'' . $fee->price . '\');" ' . $disabled_paid . '> <input type="button" id="unpaid_' . $account->id . '" name="unpaid" class="btn btn-secondary btn-sm" value="未納" Onclick="getUnpaid(\'' . $account->id . '\',\'' . $account->name . '\');" ' . $disabled_unpaid . '></td>';
                                }
                                echo '<td class="text-nowrap">' . $fee->get_status() . '</td>';
                                echo '<td class="text-nowrap">' . $fee->get_submission_time() . '</td>';
                                echo '<td class="text-nowrap text-right">' . $fee->get_price() . '</td>';
                                if ($user->admin == 1 || $user->admin == 5) {
                                    echo '<td class="text-nowrap"><a href="./edit.php?id=' . $account->id . '&fee_id=' . $fee_list->id . '" class="text-secondary"><u>編集</u></a></td>';
                                }
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
            if ($user->admin == 1 || $user->admin == 5) {
            ?>
                <form method="post">
                    <div class="list-group shadow mb-4">
                        <a href="/member/mypage/admin/camp_accounting/add_fee_list/edit.php?fee_id=<?php echo $fee_list->id; ?>" class="list-group-item list-group-item-action">集金リストの編集</a>
                        <a href="/member/mypage/admin/camp_accounting/add_fee_list/subject.php?fee_id=<?php echo $fee_list->id; ?>" class="list-group-item list-group-item-action">集金対象者の選択</a>
                        <button type="submit" name="delete" formaction="/member/mypage/admin/camp_accounting/delete_fee_list.php" class="list-group-item list-group-item-action text-danger" value="<?php echo $fee_list->id ?>" Onclick="return confirm('集金リスト「<?php echo $fee_list->name; ?>」を削除しますか？\n削除した場合、関連する全ての集金記録が削除されます。');">集金リストの削除</button>
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
if ($user->admin == 1 || $user->admin == 5) {
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

// sop
$query = "SELECT * FROM members WHERE part = 'S'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$paid_cnt = 0;
$unpaid_cnt = 0;
while ($row = $result->fetch_assoc()) {
    $account = new User($row);
    $query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_list->id";
    $result_2 = $mysqli->query($query);
    if (!$result_2) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result_2->num_rows;
    if ($row_cnt != 0) {
        while ($row_2 = $result_2->fetch_assoc()) {
            if ($row_2['datetime'] == null) {
                $unpaid_cnt += 1;
            } else {
                $paid_cnt += 1;
            }
        }
    }
}
if ($paid_cnt + $unpaid_cnt == 0) {
    $sop_ratio = 0;
} else {
    $sop_ratio = round($paid_cnt / ($paid_cnt + $unpaid_cnt), 3) * 100;
}

// alt
$query = "SELECT * FROM members WHERE part = 'A'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$paid_cnt = 0;
$unpaid_cnt = 0;
while ($row = $result->fetch_assoc()) {
    $account = new User($row);
    $query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_list->id";
    $result_2 = $mysqli->query($query);
    if (!$result_2) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result_2->num_rows;
    if ($row_cnt != 0) {
        while ($row_2 = $result_2->fetch_assoc()) {
            if ($row_2['datetime'] == null) {
                $unpaid_cnt += 1;
            } else {
                $paid_cnt += 1;
            }
        }
    }
}
if ($paid_cnt + $unpaid_cnt == 0) {
    $alt_ratio = 0;
} else {
    $alt_ratio = round($paid_cnt / ($paid_cnt + $unpaid_cnt), 3) * 100;
}

// ten
$query = "SELECT * FROM members WHERE part = 'T'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$paid_cnt = 0;
$unpaid_cnt = 0;
while ($row = $result->fetch_assoc()) {
    $account = new User($row);
    $query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_list->id";
    $result_2 = $mysqli->query($query);
    if (!$result_2) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result_2->num_rows;
    if ($row_cnt != 0) {
        while ($row_2 = $result_2->fetch_assoc()) {
            if ($row_2['datetime'] == null) {
                $unpaid_cnt += 1;
            } else {
                $paid_cnt += 1;
            }
        }
    }
}
if ($paid_cnt + $unpaid_cnt == 0) {
    $ten_ratio = 0;
} else {
    $ten_ratio = round($paid_cnt / ($paid_cnt + $unpaid_cnt), 3) * 100;
}

// bas
$query = "SELECT * FROM members WHERE part = 'B'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$paid_cnt = 0;
$unpaid_cnt = 0;
while ($row = $result->fetch_assoc()) {
    $account = new User($row);
    $query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_list->id";
    $result_2 = $mysqli->query($query);
    if (!$result_2) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result_2->num_rows;
    if ($row_cnt != 0) {
        while ($row_2 = $result_2->fetch_assoc()) {
            if ($row_2['datetime'] == null) {
                $unpaid_cnt += 1;
            } else {
                $paid_cnt += 1;
            }
        }
    }
}
if ($paid_cnt + $unpaid_cnt == 0) {
    $bas_ratio = 0;
} else {
    $bas_ratio = round($paid_cnt / ($paid_cnt + $unpaid_cnt), 3) * 100;
}

$script .= '<script>';
$script .= 'Chart.defaults.global.defaultFontFamily = "Noto Sans JP", "sans-serif";Chart.defaults.global.defaultFontColor = "#858796";';
$script .= 'var ctx = document.getElementById("partChart");';
$script .= 'var myPieChart = new Chart(ctx, {
        type: "horizontalBar",
        data: {
            labels: ["Soprano", "Alto", "Tenor", "Bass"],
            datasets: [{
                data: [' . $sop_ratio . ', ' . $alt_ratio . ', ' . $ten_ratio . ', ' . $bas_ratio . '],
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
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
