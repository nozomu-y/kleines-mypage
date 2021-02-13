<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/admin/individual_accounting/');
    exit();
}

$account = new User($user_id);

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
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= $account->get_name() ?>
                    </li>
                </ol>
            </nav>
            <?php
            if (isset($_SESSION['mypage_individual'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '個別会計「<strong>' . $_SESSION['mypage_individual'] . '</strong>」を編集しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_individual']);
            }
            if (isset($_SESSION['mypage_individual_delete'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '個別会計「<strong>' . $_SESSION['mypage_individual_delete'] . '</strong>」を削除しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_individual_delete']);
            }
            ?>
            <div class="mb-4">
                <form method="POST">
                    <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">日付</th>
                                <th class="text-nowrap">項目</th>
                                <th class="text-nowrap">金額</th>
                                <th class="text-nowrap">編集</th>
                                <th class="text-nowrap">削除</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT individual_accounting_records.price, individual_accounting_records.datetime, individual_accounting_records.accounting_id, individual_accounting_records.list_id, CONCAT(IFNULL(individual_accounting_lists.name,''),IFNULL(accounting_lists.name,'')) AS name FROM individual_accounting_records LEFT OUTER JOIN individual_accounting_lists ON individual_accounting_records.list_id=individual_accounting_lists.list_id LEFT OUTER JOIN accounting_lists ON individual_accounting_records.accounting_id=accounting_lists.accounting_id WHERE user_id=$user_id ORDER BY `datetime` DESC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            while ($row = $result->fetch_assoc()) {
                                $date = date('Y/m/d', strtotime($row['datetime']));
                                $name = $row['name'];
                                $price = "￥" . number_format($row['price']);
                                $list_id = $row['list_id'];
                                $accounting_id = $row['accounting_id'];
                            ?>
                                <tr>
                                    <td class="text-nowrap"><?= $date ?></td>
                                    <?php
                                    if ($row['accounting_id'] == NULL) {
                                    ?>
                                        <td class="text-nowrap">
                                            <a href="../list/detail.php?list_id=<?= $list_id ?>" class="text-secondary"><u><?= $name ?></u></a>
                                        </td>
                                    <?php
                                    } else {
                                    ?>
                                        <td class="text-nowrap">
                                            <a href="../accounting/detail.php?accounting_id=<?= $accounting_id ?>" class="text-secondary"><u><?= $name ?></u></a>
                                        </td>
                                    <?php
                                    }
                                    ?>
                                    <td class="text-nowrap text-right"><?= $price ?></td>
                                    <td class="text-nowrap">
                                        <?php
                                        if ($row['accounting_id'] == NULL) {
                                        ?>
                                            <a href="edit/?user_id=<?= $user_id ?>&list_id=<?= $list_id ?>" class="text-secondary"><u>編集</u></a>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <?php
                                        if ($row['accounting_id'] == NULL) {
                                        ?>
                                            <button type="submit" name="delete" formaction="delete.php" class="btn btn-danger btn-sm" value="<?= $user_id ?>_<?= $list_id ?>" Onclick="return confirm('個別会計「<?= $name ?>」を削除しますか？');">削除</button>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" style="text-align:right">総計</th>
                                <th class="text-right"><?php echo $account->get_individual_accounting_total(); ?></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
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
        columnDefs: [{ "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 4 },
            { "orderable": false, "targets": 1 },
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
