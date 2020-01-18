<?php
ob_start();
session_start();
if (!isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/login/');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

if (!($user->admin == 1 || $user->admin == 2 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">アカウント管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <form action="/member/mypage/admin/account_manage/change_admin.php" method="POST" id="form">
                <div class="mb-4">
                    <table id="accountList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <?php
                                if ($user->admin == 1) {
                                    echo '<th class="text-nowrap"></th>';
                                }
                                ?>
                                <th class="text-nowrap">学年</th>
                                <th class="text-nowrap">パート</th>
                                <th class="text-nowrap">氏名</th>
                                <?php
                                if ($user->admin == 1 || $user->admin == 3) {
                                    echo '<th class="text-nowrap">滞納額</th>';
                                }
                                ?>
                                <th class="text-nowrap">メールアドレス</th>
                                <th class="text-nowrap">パスワード</th>
                                <?php
                                if ($user->admin == 1) {
                                    echo '<th class="text-nowrap">管理者権限</th>';
                                }
                                ?>
                                <th class="text-nowrap">ステータス</th>
                                <th class="text-nowrap">編集</th>
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
                                if ($account->status == 2) {
                                    continue;
                                }
                                echo '<tr>';
                                if ($user->admin == 1) {
                                    echo '<td><div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="check[]" value="' . $account->id . '"></div></td>';
                                }
                                echo '<td class="text-nowrap">' . $account->grade . '</td>';
                                echo '<td class="text-nowrap">' . $account->get_part() . '</td>';
                                echo '<td class="text-nowrap"><span class="d-none">' . $account->kana . '</span>' . $account->name . '</td>';
                                if ($user->admin == 1 || $user->admin == 3) {
                                    echo '<td class="text-nowrap text-right">' . $account->get_delinquent() . '</td>';
                                }
                                echo '<td class="text-nowrap">' . $account->email . '</td>';
                                echo '<td class="text-nowrap">' . $account->get_password() . '</td>';
                                if ($user->admin == 1) {
                                    echo '<td class="text-nowrap">' . $account->get_admin() . '</td>';
                                }
                                echo '<td class="text-nowrap">' . $account->get_status() . '</td>';
                                switch ($account->status) {
                                    case 0:
                                        $disabled_present = "disabled";
                                        $disabled_absent = "";
                                        $disabled_resign = "";
                                        break;
                                    case 1:
                                        $disabled_present = "";
                                        $disabled_absent = "disabled";
                                        $disabled_resign = "";
                                        break;
                                }
                                echo '<td class="text-nowrap">
                                <button type="submit" name="present" formaction="/member/mypage/admin/account_manage/change_status.php" class="btn btn-secondary btn-sm" value="' . $account->id . '" Onclick="return confirm(\'' . $account->name . 'さんのステータスを在団にしますか？\');" ' . $disabled_present . '>在団</button>
                                <button type="submit" name="absent" formaction="/member/mypage/admin/account_manage/change_status.php" class="btn btn-secondary btn-sm" value="' . $account->id . '" Onclick="return confirm(\'' . $account->name . 'さんのステータスを休団にしますか？\');" ' . $disabled_absent . '>休団</button>
                                <button type="submit" name="resign" formaction="/member/mypage/admin/account_manage/change_status.php" class="btn btn-danger btn-sm" value="' . $account->id . '" Onclick="return confirm(\'' . $account->name . 'さんのステータスを退団にしますか？\');" >退団</button>
                            </td>';
                                // echo '<td class="text-nowrap"><button type="submit" name="delete" formaction="/member/mypage/admin/account_manage/delete_user.php" class="btn btn-danger btn-sm" value="' . $account->id . '" Onclick="return confirm(\'' . $account->name . 'さんのアカウントを削除しますか？\nこれを実行すると会計記録も消えます！\');">削除</button></td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if ($user->admin == 1) {
                    echo '<span class="dropdown">
                        <a class="btn btn-primary dropdown-toggle mb-4" href="" id="dropdownMenu-admin" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            管理者権限を付与
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu-admin">
                            <button class="dropdown-item" type="submit" name="admin-give-1">マスター権限</button>
                            <button class="dropdown-item" type="submit" name="admin-give-2">アカウント管理</button>
                            <button class="dropdown-item" type="submit" name="admin-give-3">会計システム</button>
                            <!-- <button class="dropdown-item" type="submit" name="admin-give-4">練習計画管理</button> -->
                            <button class="dropdown-item" type="submit" name="admin-give-5">合宿会計システム</button>
                        </div>
                    </span>
                    <button type="submit" class="btn btn-secondary mb-4" name="admin-take">管理者権限を剥奪</button>';
                }
                ?>
            </form>
            <a class="btn btn-primary mb-4" href="/member/mypage/admin/account_manage/add_user/" role="button">アカウントの追加</a>
        </div>
        <div class="col-xl-3 col-sm-12">
            <div class="card shadow mb-4">
                <div class="card-header">管理者一覧</div>
                <div class="card-body"></div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header">ログ</div>
                <div class="card-body">
                    <p>このページで行われる操作は全てログとして残ります。</p>
                    <a href="./account_log.php">ログを閲覧</a>
                </div>
            </div>
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
if ($user->admin == 1) {
    $script .= '{ "orderable": false, "targets": 0 },
            { "orderable": false, "targets": 6 },
            { "orderable": false, "targets": 9 },
            { "orderable": true, "orderDataType": "part", "targets": 2 }';
} else if ($user->admin == 2) {
    $script .= '{ "orderable": false, "targets": 4 },
            { "orderable": false, "targets": 6 },
            { "orderable": true, "orderDataType": "part", "targets": 1 }';
} else {
    $script .= '{ "orderable": false, "targets": 5 },
            { "orderable": false, "targets": 7 },
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
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
