<?php
require __DIR__ . '/../Common/init_page.php';

header('Location: ' . MYPAGE_ROOT);
exit();

$PAGE_NAME = "プロフィール";
include_once __DIR__ . '/../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">セッション管理</h1>
    <div class="row">
        <div class="col-sm-12">
            <?php
            if (isset($_SESSION['mypage_delete_session'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_delete_session'] . '</strong>のセッションを削除しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_delete_session']);
            }
            ?>
            <div class="mb-4">
                <form method="POST">
                    <table id="sessionList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">ログイン日時</th>
                                <th class="text-nowrap">プラットフォーム</th>
                                <th class="text-nowrap">ブラウザ</th>
                                <th class="text-nowrap">削除</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM auto_login WHERE id = '$USER->id' ORDER BY datetime DESC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            while ($row = $result->fetch_assoc()) {
                                $login_datetime = $row['datetime'];
                                $login_platform = $row['device'];
                                $login_browser = $row['browser'];
                                $login_token = $row['token'];
                                $this_device = '';
                                if (!empty($_COOKIE['mypage_auto_login']) && $login_token == $_COOKIE['mypage_auto_login']) {
                                    $this_device = '（この端末）';
                                }
                                echo '<tr>';
                                echo '<td class="text-nowrap">' . $login_datetime . '</td>';
                                echo '<td class="text-nowrap">' . $login_platform . $this_device . '</td>';
                                echo '<td class="text-nowrap">' . $login_browser . '</td>';
                                echo '<td class="text-nowrap"><button type="submit" name="delete" class="btn btn-danger rounded-circle btn-sm" formaction="./delete_session.php" value="' . $login_token . '"><i class="far fa-trash-alt mt-0"></i></button></td>';
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
    $("#sessionList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [';
$script .= '{ "orderable": false, "targets": 3 }';
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
$script .= '</script>';

include_once __DIR__ . '/../Common/foot.php';
