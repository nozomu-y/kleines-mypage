<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_GET['list_id'])) {
    $list_id = $_GET['list_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/accounting/');
    exit();
}

$individual_accounting = new IndividualAccountingList($list_id);

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
                    <li class="breadcrumb-item"><a href="./">個別会計一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $individual_accounting->name ?></li>
                </ol>
            </nav>
            <?php
            if (isset($_SESSION['mypage_add_subject'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '集金対象者を追加しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_add_subject']);
            }
            if (isset($_SESSION['mypage_delete_subject'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo  '対象者から<strong>' . $_SESSION['mypage_delete_subject'] . '</strong>を削除しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_delete_subject']);
            }
            if (isset($_SESSION['mypage_individual'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_individual'] . '</strong>の個別会計を編集しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_individual']);
            }
            ?>
            <div class="mb-4">
                <form method="POST">
                    <table id="accountList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">学年</th>
                                <th class="text-nowrap">パート</th>
                                <th class="text-nowrap">氏名</th>
                                <th class="text-nowrap">金額</th>
                                <th class="text-nowrap">日付</th>
                                <th class="text-nowrap">編集</th>
                                <th class="text-nowrap">削除</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, profiles.user_id, individual_accounting_records.price, individual_accounting_records.datetime FROM profiles INNER JOIN individual_accounting_records ON profiles.user_id=individual_accounting_records.user_id WHERE individual_accounting_records.list_id=$list_id ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
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
                                $date = date('Y/m/d', strtotime($row['datetime']));

                            ?>
                                <tr>
                                    <td class="text-nowrap"><?= $grade ?></td>
                                    <td class="text-nowrap"><?= $part ?></td>
                                    <td class="text-nowrap"><span class="d-none">'<?= $kana ?></span><a href="../user/?user_id=<?= $user_id ?>" class="text-secondary"><u><?= $name ?></u></a></td>
                                    <td class="text-nowrap text-right">￥<?= number_format($price) ?></td>
                                    <td class="text-nowrap"><?= $date ?></td>
                                    <td class="text-nowrap">
                                        <a href="./edit/?user_id=<?= $user_id ?>&list_id=<?= $list_id ?>" class="text-secondary"><u>編集</u></a>
                                    </td>
                                    <td class="text-nowrap">
                                        <button type="submit" name="delete" formaction="delete_subject.php" class="btn btn-danger btn-sm" value="<?= $user_id ?>_<?= $list_id ?>" Onclick="return confirm('個別会計「<?= $individual_accounting->name ?>」から<?= $name ?>を削除しますか？');">削除</button>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <a class="btn btn-primary mb-4" href="./add_subject/?list_id=<?= $list_id ?>" role="button">対象者の追加</a>
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
include_once __DIR__ . '/../../../Common/foot.php';
