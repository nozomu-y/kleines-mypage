<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

$PAGE_NAME = "個別会計管理";
include_once __DIR__ . '/../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">個別会計管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../">個別会計管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page">集金時の個別会計利用</li>
                </ol>
            </nav>
            <div class="mb-4">
                <form method="POST">
                    <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">項目</th>
                                <th class="text-nowrap">期限</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT individual_accounting_records.accounting_id, accounting_lists.name, accounting_lists.deadline FROM individual_accounting_records INNER JOIN accounting_lists ON individual_accounting_records.accounting_id=accounting_lists.accounting_id WHERE individual_accounting_records.accounting_id IS NOT NULL GROUP BY individual_accounting_records.accounting_id ORDER BY accounting_lists.deadline DESC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            while ($row = $result->fetch_assoc()) {
                                $name = $row['name'];
                                $deadline = date('Y/m/d', strtotime($row['deadline']));
                                $accounting_id = $row['accounting_id'];
                            ?>
                                <tr>
                                    <td class="text-nowrap">
                                        <a href="detail.php?accounting_id=<?= $accounting_id ?>" class="text-secondary"><u><?= $name ?></u></a>
                                    </td>
                                    <td class="text-nowrap"><?= $deadline ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
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
        columnDefs: [{ "orderable": false, "targets": 0 }],
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
include_once __DIR__ . '/../../../Common/foot.php';
