<?php
require __DIR__ . '/../../Common/init_page.php';

$PAGE_NAME = "個別会計";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">個別会計</h1>
    <div class="row">
        <div class="col-sm-12">
            <div class="mb-4">
                <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">日付</th>
                            <th class="text-nowrap">項目</th>
                            <th class="text-nowrap">金額</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT individual_accounting_records.price, individual_accounting_records.datetime, individual_accounting_records.accounting_id, individual_accounting_records.list_id, CONCAT(IFNULL(individual_accounting_lists.name,''),IFNULL(accounting_lists.name,'')) AS name FROM individual_accounting_records LEFT OUTER JOIN individual_accounting_lists ON individual_accounting_records.list_id=individual_accounting_lists.list_id LEFT OUTER JOIN accounting_lists ON individual_accounting_records.accounting_id=accounting_lists.accounting_id WHERE user_id=$USER->id ORDER BY `datetime` DESC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $name = $row['name'];
                            $date = date('Y/m/d', strtotime($row['datetime']));
                            $price = number_format($row['price']);
                        ?>
                            <tr>
                                <td class="text-nowrap"><?= $date ?></td>
                                <td class="text-nowrap"><?= h($name) ?></td>
                                <td class="text-nowrap text-right">￥<?= $price ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align:right">総計</th>
                            <th class="text-right"><?php echo $USER->get_individual_accounting_total(); ?></th>
                        </tr>
                    </tfoot>
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
        columnDefs: [{"orderable": false, "targets": 1},
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

include_once __DIR__ . '/../../Common/foot.php';
