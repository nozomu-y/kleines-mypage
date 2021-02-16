<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
$PAGE_NAME = "個別会計管理";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">個別会計管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <?php
            if (isset($_SESSION['mypage_individual_add_multiple'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_individual_add_multiple'] . '</strong>を追加しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_individual_add_multiple']);
            }
            ?>
            <div class="mb-4">
                <table id="accountList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">学年</th>
                            <th class="text-nowrap">パート</th>
                            <th class="text-nowrap">氏名</th>
                            <th class="text-nowrap">個別会計総額</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, users.status, users.user_id, (SELECT SUM(price) FROM individual_accounting_records WHERE user_id=users.user_id) AS individual_accounting_total FROM profiles INNER JOIN users ON profiles.user_id=users.user_id AND users.status!='RESIGNED' ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
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
                            $individual_accounting_total = $row['individual_accounting_total'];
                        ?>
                            <tr>
                                <td class="text-nowrap"><?= $grade ?></td>
                                <td class="text-nowrap"><?= $part ?></td>
                                <td class="text-nowrap"><span class="d-none">'<?= $kana ?></span><a href="./user/?user_id=<?= $user_id ?>" class="text-secondary"><u><?= $name ?></u></a></td>
                                <td class="text-nowrap text-right">￥<?= number_format($individual_accounting_total) ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-3 col-sm-12">
            <div class="list-group shadow mb-4">
                <a href="./accounting/" class="list-group-item list-group-item-action">集金時の個別会計利用</a>
                <a href="./list/" class="list-group-item list-group-item-action">個別会計一覧</a>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header">ログ</div>
                <div class="card-body">
                    <p>このページで行われる操作は全てログとして残ります。</p>
                    <a href="./individual_accounting_log.php">ログを閲覧</a>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header">個別会計データ</div>
                <div class="card-body">
                    <p>全ての個別会計データをダウンロードできます。（退団者も含む）</p>
                    <a class="btn" style="color: #fff; background-color:#1d6f42; border-color:#1d6f42;" href="../../download/individual_accounting.php"><i class="fas fa-file-excel mr-2"></i>ダウンロード</a>
                </div>
            </div>
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
        columnDefs: [{ "orderable": true, "orderDataType": "part", "targets": 1 },
            { type: "currency", targets: 3 }],
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
include_once __DIR__ . '/../../Common/foot.php';
