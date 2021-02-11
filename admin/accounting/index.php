<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isManager() || $USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
$PAGE_NAME = "集金リスト";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <form method="post">
                <div class="mb-4">
                    <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">集金リスト</th>
                                <th class="text-nowrap">期限</th>
                                <!-- <th class="text-nowrap">金額（最頻値）</th> -->
                                <th class="text-nowrap">集金率</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT accounting_id, name, deadline, (SELECT COUNT(*) FROM accounting_records WHERE accounting_id=accounting_lists.accounting_id) as all_count, (SELECT COUNT(*) FROM accounting_records WHERE accounting_id=accounting_lists.accounting_id AND datetime IS NOT NULL) as paid_count FROM accounting_lists WHERE admin='GENERAL' ORDER BY deadline DESC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            while ($row = $result->fetch_assoc()) {
                                if ($row['paid_count'] == 0) {
                                    $paid_rate = '0.00 %';
                                } else {
                                    $paid_rate = strval(round($row['paid_count'] / $row['all_count'], 3) * 100) . ' %';
                                }
                            ?>
                                <tr>
                                    <td class="text-nowrap"><a href="./detail.php?fee_id=<?= $row['accounting_id'] ?>" class="text-secondary"><u><?= $row['name'] ?></u></a></td>
                                    <td class="text-nowrap"><?= date('Y/m/d', strtotime($row['deadline'])) ?></td>
                                    <!-- <td class="text-nowrap text-right"></td> -->
                                    <td class="text-nowrap text-right"><?= $paid_rate ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <?php
            if ($USER->isAccountant()) {
            ?>
                <a class="btn btn-primary mb-4" href="./add_accounting_list/" role="button">集金リストの追加</a>
            <?php
            }
            ?>
        </div>
        <div class="col-xl-3 col-sm-12">
            <?php
            if ($USER->isAccountant()) {
            ?>
                <div class="card shadow mb-4">
                    <div class="card-header">ログ</div>
                    <div class="card-body">
                        <p>このページで行われる操作は全てログとして残ります。</p>
                        <a href="./accounting_log.php">ログを閲覧</a>
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
$script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [{ "orderable": false, "targets": 0 },
            { type: "currency", targets: 2 }],
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
include_once __DIR__ . '/../../Common/foot.php';
