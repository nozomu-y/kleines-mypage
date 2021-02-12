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
                    <li class="breadcrumb-item active" aria-current="page">個別会計一覧</li>
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
            if (isset($_SESSION['mypage_individual_add'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '個別会計「<strong>' . $_SESSION['mypage_individual_add'] . '</strong>」を追加しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_individual_add']);
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
                                <th class="text-nowrap">項目</th>
                                <th class="text-nowrap">作成日</th>
                                <th class="text-nowrap">編集</th>
                                <th class="text-nowrap">削除</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT individual_accounting_lists.list_id, individual_accounting_lists.name, individual_accounting_lists.datetime FROM individual_accounting_lists ORDER BY individual_accounting_lists.datetime DESC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            while ($row = $result->fetch_assoc()) {
                                $name = $row['name'];
                                $datetime = date('Y/m/d', strtotime($row['datetime']));
                                $list_id = $row['list_id'];
                            ?>
                                <tr>
                                    <td class="text-nowrap">
                                        <a href="detail.php?list_id=<?= $list_id ?>" class="text-secondary"><u><?= $name ?></u></a>
                                    </td>
                                    <td class="text-nowrap"><?= $datetime ?></td>
                                    <td class="text-nowrap">
                                        <a href="edit_list/?list_id=<?= $list_id ?>" class="text-secondary"><u>編集</u></a>
                                    </td>
                                    <td class="text-nowrap">
                                        <button type="submit" name="delete" formaction="delete_list.php" class="btn btn-danger btn-sm" value="<?= $list_id ?>" Onclick="return confirm('個別会計「<?= $name ?>」を削除しますか？');">削除</button>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <a class="btn btn-primary mb-4" href="./add_list/" role="button">個別会計の追加</a>
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
