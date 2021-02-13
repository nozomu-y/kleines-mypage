<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_GET['fee_id'])) {
    $accounting_id = $_GET['fee_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$accounting = new AccountingList($accounting_id);

if ($accounting->admin != 'GENERAL') {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$PAGE_NAME = "集金リスト";
include_once __DIR__ . '/../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../">集金リスト一覧</a></li>
                    <li class="breadcrumb-item"><a href="../detail.php?fee_id=<?= $accounting->accounting_id ?>"><?= $accounting->name ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">集金金額の一括変更</li>
                </ol>
            </nav>
            <p>金額を入力し、集金金額を変更したい集金対象者を選択してください。<br>既に支払いが済んでいる集金対象者の集金金額は変更できません。</p>
            <form method="post" action="./change_price_multiple.php" name="form" class="mb-4">
                <div class="form-group">
                    <label for="price">金額</label>
                    <input type="number" name="price" class="form-control" id="price" required>
                </div>
                <input type="button" class="btn btn-primary mb-2" value="全て選択" onclick="allcheck(true);">
                <input type="button" class="btn btn-primary mb-2" value="選択解除" onclick="allcheck(false);">
                <div class="mb-4">
                    <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap"></th>
                                <th class="text-nowrap">学年</th>
                                <th class="text-nowrap">パート</th>
                                <th class="text-nowrap">氏名</th>
                                <th class="text-nowrap">ステータス</th>
                                <th class="text-nowrap">集金金額</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, profiles.user_id, users.status, accounting_records.price, accounting_records.datetime, accounting_records.accounting_id FROM profiles INNER JOIN users ON profiles.user_id=users.user_id LEFT OUTER JOIN (SELECT accounting_records.price, accounting_records.datetime, accounting_records.accounting_id, accounting_records.user_id FROM accounting_records WHERE accounting_id=$accounting_id) as accounting_records ON users.user_id=accounting_records.user_id WHERE accounting_records.accounting_id IS NOT NULL AND accounting_records.datetime IS NULL ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            while ($row = $result->fetch_assoc()) {
                                $user_status = $row['status'];
                                $accounting_datetime = $row['datetime'];
                                $account_id = $row['user_id'];
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
                                if ($row['status'] == "PRESENT") {
                                    $status = "在団";
                                } else if ($row['status'] == "ABSENT") {
                                    $status = "休団";
                                } else if ($row['status'] == "RESIGNED") {
                                    $status = "退団";
                                }
                                $price = "￥" . number_format($row['price']);
                            ?>
                                <tr>
                                    <td>
                                        <div class="form-check form-check-inline"><input type="hidden" name="check_<?= $account_id ?>" value="0"><input class="form-check-input" type="checkbox" name="check_<?= $account_id ?>" value="1"></div>
                                    </td>
                                    <td class="text-nowrap"><?= $grade ?></td>
                                    <td class="text-nowrap"><?= $part ?></td>
                                    <td class="text-nowrap"><?= $name ?></td>
                                    <td class="text-nowrap"><?= $status ?></td>
                                    <td class="text-nowrap"><?= $price ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="fee_id" value="<?= $accounting->accounting_id ?>">
                <button type="submit" class="btn btn-primary" name="submit">集金金額を変更</button>
                <a class="btn btn-secondary" href="../detail.php?fee_id=<?= $accounting->accounting_id ?>" role="button">キャンセル</a>
            </form>
        </div>
    </div>
</div>

<?php
$script = '<script>
    function allcheck(tf) {
      var ElementsCount = document.form.elements.length; // チェックボックスの数
      for (i = 0; i < ElementsCount; i++) {
        document.form.elements[i].checked = tf; // ON・OFFを切り替え
      }
    }
  </script>';

$script .= '<script>';
$script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthChange: false,
        displayLength: -1,
        columnDefs: [{ "orderable": false, "targets": 0 },
            { "orderable": true, "orderDataType": "part", "targets": 2 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'>>"
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
include_once __DIR__ . '/../../../Common/foot.php';
