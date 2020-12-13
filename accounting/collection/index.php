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
                        $query = "SELECT * FROM fee_record_$USER->id ORDER BY id DESC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $fee = new Fee($row);
                            echo '<tr>';
                            echo '<td class="text-nowrap">' . $fee->name . '</td>';
                            echo '<td class="text-nowrap text-right">' . $fee->get_price() . '</td>';
                            echo '<td class="text-nowrap">' . $fee->get_deadline() . '</td>';
                            echo '<td class="text-nowrap">' . $fee->get_status() . '</td>';
                            echo '<td class="text-nowrap text-right">' . $fee->get_paid_cash() . '</td>';
                            echo '<td class="text-nowrap text-right">' . $fee->get_paid_individual() . '</td>';
                            echo '<td class="text-nowrap">' . $fee->get_submission_time() . '</td>';
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
