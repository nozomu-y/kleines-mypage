<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 2 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
$PAGE_NAME = "アカウント管理";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">アカウント管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="./">アカウント管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page">退団者リスト</li>
                </ol>
            </nav>
            <?php
            if (isset($_SESSION['mypage_delete_user'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_account_name'] . '</strong>のアカウントを削除しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_delete_user']);
                unset($_SESSION['mypage_account_name']);
            }
            ?>
            <div class="mb-4">
                <form method="POST" id="form">
                    <table id="accountList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">学年</th>
                                <th class="text-nowrap">パート</th>
                                <th class="text-nowrap">氏名</th>
                                <?php
                                if ($USER->admin == 1 || $USER->admin == 3) {
                                    echo '<th class="text-nowrap">滞納額</th>';
                                    echo '<th class="text-nowrap">個別会計</th>';
                                }
                                ?>
                                <th class="text-nowrap">メールアドレス</th>
                                <th class="text-nowrap">編集</th>
                                <?php
                                if ($USER->admin == 1) {
                                    echo '<th class="text-nowrap">削除</th>';
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM members ORDER BY grade ASC, CASE WHEN part LIKE 'S' THEN 1 WHEN part LIKE 'A' THEN 2 WHEN part LIKE 'T' THEN 3 WHEN part LIKE 'B' THEN 4 END ASC, kana ASC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            $row_cnt = $result->num_rows;
                            while ($row = $result->fetch_assoc()) {
                                $account = new User($row);
                                if ($account->status != 2) {
                                    continue;
                                }
                                if ($account->delinquent != 0) {
                                    $table_danger = 'class="tabe-danger"';
                                } else {
                                    $table_danger = '';
                                }
                                echo '<tr ' . $table_danger . '>';
                                echo '<td class="text-nowrap">' . $account->grade . '</td>';
                                echo '<td class="text-nowrap">' . $account->get_part() . '</td>';
                                echo '<td class="text-nowrap"><span class="d-none">' . $account->kana . '</span>' . $account->name . '</td>';
                                if ($USER->admin == 1 || $USER->admin == 3) {
                                    echo '<td class="text-nowrap text-right">' . $account->get_delinquent() . '</td>';
                                    echo '<td class="text-nowrap text-right">' . $account->get_individual_accounting_total() . '</td>';
                                }
                                echo '<td class="text-nowrap">' . $account->email . '</td>';
                                echo '<td class="text-nowrap">
                                <button type="submit" name="present" formaction="./change_status.php" class="btn btn-secondary btn-sm" value="' . $account->id . '" Onclick="return confirm(\'' . $account->name . 'さんのステータスを在団にしますか？\');">在団</button>
                                <button type="submit" name="absent" formaction="./change_status.php" class="btn btn-secondary btn-sm" value="' . $account->id . '" Onclick="return confirm(\'' . $account->name . 'さんのステータスを休団にしますか？\');">休団</button>
                            </td>';
                                if ($USER->admin == 1) {
                                    echo '<td class="text-nowrap"><button type="submit" name="delete" formaction="./delete_user.php" class="btn btn-danger btn-sm" value="' . $account->id . '" Onclick="return confirm(\'' . $account->name . 'さんのアカウントを削除しますか？\nこのアカウントに関連する会計データが全て削除されます。\');">削除</button></td>';
                                }
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <div class="col-xl-3 col-sm-12">

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
        columnDefs: [';
if ($USER->admin == 1) {
    $script .= '{ "orderable": false, "targets": 6 },
            { "orderable": false, "targets": 7 },
            { "orderable": true, "orderDataType": "part", "targets": 1 },
            { type: "currency", targets: 3 },
            { type: "currency", targets: 4 }';
} else if ($USER->admin == 3) {
    $script .= '{ "orderable": false, "targets": 6 },
            { "orderable": true, "orderDataType": "part", "targets": 1 },
            { type: "currency", targets: 3 },
            { type: "currency", targets: 4 }';
} else {
    $script .= '{ "orderable": false, "targets": 4 },
            { "orderable": true, "orderDataType": "part", "targets": 1 }';
}

$script .= '],
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
