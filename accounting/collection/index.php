<?php
require __DIR__ . '/../../Common/init_page.php';

$PAGE_NAME = "集金記録";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金記録</h1>
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
                            <th class="text-nowrap">現金支払い</th>
                            <th class="text-nowrap">個別会計支払い</th>
                            <th class="text-nowrap">提出日時</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM accounting_lists INNER JOIN (SELECT * FROM accounting_records WHERE user_id='$USER->id') as accounting_records ON accounting_lists.accounting_id = accounting_records.accounting_id ORDER BY accounting_lists.deadline DESC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed 1: ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $accounting_name = $row['name'];
                            $accounting_price = $row['price'];
                            $accounting_deadline = $row['deadline'];
                            $accounting_deadline = date('Y/m/d', strtotime($accounting_deadline));
                            if ($row['datetime'] != NULL) {
                                $accounting_status = "既納";
                            } else {
                                $accounting_status = "未納";
                            }
                            $accounting_paid_cash = $row['paid_cash'];
                            if ($row['datetime'] == NULL || strtotime($row['datetime'] == 0)) {
                                $accounting_paid_individual = '';
                                $accounting_paid_cash = '';
                            } else {
                                $accounting_paid_individual = $accounting_price - $accounting_paid_cash;
                                $accounting_paid_cash = "￥" . number_format($accounting_paid_cash);
                                $accounting_paid_individual = "￥" . number_format($accounting_paid_individual);
                            }
                            $accounting_price = "￥" . number_format($accounting_price);
                            if ($row['datetime'] == NULL || strtotime($row['datetime'] == 0)) {
                                $accounting_datetime = '';
                            } else {
                                $accounting_datetime = date('Y/m/d H:i:s', strtotime($row['datetime']));
                            }
                        ?>
                            <tr>
                                <td class="text-nowrap"><?= h($accounting_name) ?></td>
                                <td class="text-nowrap text-right"><?= $accounting_price ?></td>
                                <td class="text-nowrap"><?= $accounting_deadline ?></td>
                                <td class="text-nowrap"><?= $accounting_status ?></td>
                                <td class="text-nowrap text-right"><?= $accounting_paid_cash ?></td>
                                <td class="text-nowrap text-right"><?= $accounting_paid_individual ?></td>
                                <td class="text-nowrap"><?= $accounting_datetime ?></td>
                            </tr>
                        <?php
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
        lengthMenu: [[ 10, 20, -1 ],[10, 20, "全件"]],
        columnDefs: [{"orderable": false, "targets": 0},
        { type: "currency", targets: 1 },
        { type: "currency", targets: 4 },
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
$script .= '</script>';

include_once __DIR__ . '/../../Common/foot.php';
