<?php
require __DIR__ . '/../Common/init_page.php';

$PAGE_NAME = "ログイン履歴";
include_once __DIR__ . '/../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">ログイン履歴</h1>
    <div class="row">
        <div class=" col-xl-6 col-sm-12">
            <h2 class="h4 text-gray-800 mb-4">ログイン履歴</h2>
            <div class="mb-4">
                <table id="loginHistories" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">日時</th>
                            <th class="text-nowrap">成功</th>
                            <th class="text-nowrap">IPアドレス</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT login_histories.datetime, login_histories.IP, login_histories.success FROM login_histories INNER JOIN profiles ON login_histories.user_id=profiles.user_id WHERE profiles.user_id=$USER->id ORDER BY login_histories.datetime DESC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $datetime = date('Y/m/d H:i:s', strtotime($row['datetime']));
                            $IP = $row['IP'];
                            if ($row['success'] == 1) {
                                $success = "成功";
                            } else {
                                $success = "失敗";
                            }
                        ?>
                            <tr>
                                <td class="text-nowrap"><?= $datetime ?></td>
                                <td class="text-nowrap"><?= $success ?></td>
                                <td class="text-nowrap"><?= $IP ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class=" col-xl-6 col-sm-12">
            <h2 class="h4 text-gray-800 mb-4">パスワード更新履歴</h2>
            <div class="mb-4">
                <table id="passwordUpdateHistories" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">日時</th>
                            <th class="text-nowrap">IPアドレス</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT password_updates.datetime, password_updates.IP FROM password_updates INNER JOIN profiles ON password_updates.user_id=profiles.user_id WHERE profiles.user_id=$USER->id ORDER BY password_updates.datetime DESC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $datetime = date('Y/m/d H:i:s', strtotime($row['datetime']));
                            $IP = $row['IP'];
                        ?>
                            <tr>
                                <td class="text-nowrap"><?= $datetime ?></td>
                                <td class="text-nowrap"><?= $IP ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$script = '<script>';
$script .= '$(document).ready(function() {
    $("#loginHistories").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 10, 20, -1 ],[10, 20, "全件"]],
        columnDefs: [{"orderable": false, "targets": 1},{"orderable": false, "targets": 2}],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
        dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
$script .= '$(document).ready(function() {
    $("#passwordUpdateHistories").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 10, 20, -1 ],[10, 20, "全件"]],
        columnDefs: [{"orderable": false, "targets": 1}],
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
