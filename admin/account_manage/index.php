<?php
require __DIR__ . '/../../Common/init_page.php';

if (!($USER->isManager() || $USER->isAccountant())) {
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
            <?php
            if (isset($_SESSION['mypage_status'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_account_name'] . '</strong>のステータスを<strong>' . $_SESSION['mypage_status'] . '</strong>に変更しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_status']);
                unset($_SESSION['mypage_account_name']);
            }
            if (isset($_SESSION['mypage_admin'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_account_name'] . '</strong>に管理者権限（<strong>' . $_SESSION['mypage_admin'] . '</strong>）を与えました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_admin']);
                unset($_SESSION['mypage_account_name']);
            }
            if (isset($_SESSION['mypage_admin_deprive'])) {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_account_name'] . '</strong>の管理者権限を剥奪しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_admin_deprive']);
                unset($_SESSION['mypage_account_name']);
            }
            if (isset($_SESSION['mypage_status_failure'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_account_name'] . '</strong>は管理者権限を持っているため、ステータスを退団に変更できません。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_status_failure']);
                unset($_SESSION['mypage_account_name']);
            }
            ?>

            <form action="./change_admin.php" method="POST" id="form">
                <div class="mb-4">
                    <table id="accountList" class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-nowrap">学年</th>
                                <th class="text-nowrap">パート</th>
                                <th class="text-nowrap">氏名</th>
                                <?php
                                if ($USER->isAccountant()) {
                                    echo '<th class="text-nowrap">滞納額</th>';
                                    echo '<th class="text-nowrap">個別会計</th>';
                                }
                                ?>
                                <th class="text-nowrap">ステータス</th>
                                <th class="text-nowrap">編集</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, users.status, users.user_id, (SELECT SUM(price) FROM accounting_records WHERE user_id=users.user_id AND datetime IS NULL) AS delinquent, (SELECT SUM(price) FROM individual_accounting_records WHERE user_id=users.user_id) AS individual_accounting_total FROM profiles INNER JOIN users ON profiles.user_id=users.user_id ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            $row_cnt = $result->num_rows;
                            while ($row = $result->fetch_assoc()) {
                                if ($row['status'] == 'RESIGNED') {
                                    continue;
                                }
                                echo '<tr>';
                                echo '<td class="text-nowrap">' . $row['grade'] . '</td>';
                                if ($row['part'] == 'S') {
                                    echo '<td class="text-nowrap">Soprano</td>';
                                } elseif ($row['part'] == 'A') {
                                    echo '<td class="text-nowrap">Alto</td>';
                                } elseif ($row['part'] == 'T') {
                                    echo '<td class="text-nowrap">Tenor</td>';
                                } elseif ($row['part'] == 'B') {
                                    echo '<td class="text-nowrap">Bass</td>';
                                }
                                echo '<td class="text-nowrap"><span class="d-none">' . $row['name_kana'] . '</span><a href="./detail.php?user_id=' . $row['user_id'] . '" class="text-secondary"><u>' . $row['last_name'] . $row['first_name'] . '</u></a></td>';
                                if ($USER->isAccountant()) {
                                    echo '<td class="text-nowrap text-right">' . format_price($row['delinquent']) . '</td>';
                                    echo '<td class="text-nowrap text-right">' . format_price($row['individual_accounting_total']) . '</td>';
                                }
                                if ($row['status'] == 'PRESENT') {
                                    echo '<td class="text-nowrap">在団</td>';
                                } elseif ($row['status'] == 'ABSENT') {
                                    echo '<td class="text-nowrap">休団</td>';
                                }
                                switch ($row['status']) {
                                    case 'PRESENT':
                                        $disabled_present = "disabled";
                                        $disabled_absent = "";
                                        $disabled_resign = "";
                                        break;
                                    case 'ABSENT':
                                        $disabled_present = "";
                                        $disabled_absent = "disabled";
                                        $disabled_resign = "";
                                        break;
                                }
                                echo '<td class="text-nowrap">
                                <button type="submit" name="present" formaction="./change_status.php" class="btn btn-secondary btn-sm" value="' .  $row['user_id'] . '" Onclick="return confirm(\'' .  $row['last_name'] . $row['first_name'] . 'さんのステータスを在団にしますか？\');" ' . $disabled_present . '>在団</button>
                                <button type="submit" name="absent" formaction="./change_status.php" class="btn btn-secondary btn-sm" value="' .  $row['user_id'] . '" Onclick="return confirm(\'' .  $row['last_name'] . $row['first_name'] . 'さんのステータスを休団にしますか？\');" ' . $disabled_absent . '>休団</button>
                                <button type="submit" name="resign" formaction="./change_status.php" class="btn btn-danger btn-sm" value="' .  $row['user_id'] . '" Onclick="return confirm(\'' .  $row['last_name'] . $row['first_name'] . 'さんのステータスを退団にしますか？\');">退団</button>
                                </td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <a class="btn btn-primary mb-4" href="./add_user/" role="button">アカウントの追加</a>
        </div>
        <div class="col-xl-3 col-sm-12">
            <div class="card shadow mb-4">
                <div class="card-header">ステータス</div>
                <div class="card-body">
                    <p>
                        全てのアカウントは在団・休団・退団のいずれかのステータスで管理されます。
                        <br>
                        このページには在団・休団のみが表示されます。
                        <br>
                        管理者は退団にできません。
                    </p>
                    <a href="./resign_list.php">退団者リスト</a>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header">ログ</div>
                <div class="card-body">
                    <p>このページで行われる操作は全てログとして残ります。</p>
                    <a href="./account_log.php">ログを閲覧</a>
                    <?php
                    if ($USER->isMaster()) {
                        echo '<br><a href="./auth_log.php">認証ログを閲覧</a>';
                    }
                    if ($USER->isMaster()) {
                        echo '<br><a href="./download_log.php">ダウンロードログを閲覧</a>';
                    }
                    ?>
                </div>
            </div>
            <?php
            if ($USER->isMaster()) {
            ?>
                <div class="card shadow mb-4">
                    <div class="card-header">管理者</div>
                    <div class="card-body">
                        <strong>Web管理人</strong><br>
                        <?php
                        $query = "SELECT profiles.last_name, profiles.first_name, profiles.grade, profiles.part FROM admins INNER JOIN profiles ON profiles.user_id=admins.user_id WHERE admins.role = 'MASTER' ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            echo $row['grade'] . $row['part'] . ' ' . $row['last_name'] . $row['first_name'] . '<br>';
                        }
                        ?>
                        <strong>運営</strong><br>
                        <?php
                        $query = "SELECT profiles.last_name, profiles.first_name, profiles.grade, profiles.part FROM admins INNER JOIN profiles ON profiles.user_id=admins.user_id WHERE admins.role = 'MANAGER' ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            echo $row['grade'] . $row['part'] . ' ' . $row['last_name'] . $row['first_name'] . '<br>';
                        }
                        ?>
                        <strong>会計</strong><br>
                        <?php
                        $query = "SELECT profiles.last_name, profiles.first_name, profiles.grade, profiles.part FROM admins INNER JOIN profiles ON profiles.user_id=admins.user_id WHERE admins.role = 'ACCOUNTANT' ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            echo $row['grade'] . $row['part'] . ' ' . $row['last_name'] . $row['first_name'] . '<br>';
                        }
                        ?>
                        <strong>合宿委員</strong><br>
                        <?php
                        $query = "SELECT profiles.last_name, profiles.first_name, profiles.grade, profiles.part FROM admins INNER JOIN profiles ON profiles.user_id=admins.user_id WHERE admins.role = 'CAMP' ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, profiles.name_kana ASC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            echo $row['grade'] . $row['part'] . ' ' . $row['last_name'] . $row['first_name'] . '<br>';
                        }
                        ?>
                    </div>
                </div>
            <?php
            }
            ?>
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
if ($USER->isMaster()) {
    $script .= '{ "orderable": true, "orderDataType": "part", "targets": 1 },
            { "orderable": false, "targets": 6 },
            { type: "currency", targets: 3 },
            { type: "currency", targets: 4 }';
} else if ($USER->isManager()) {
    $script .= '{ "orderable": false, "targets": 4 },
            { "orderable": true, "orderDataType": "part", "targets": 1 }';
} else if ($USER->isAccountant()) {
    $script .= '{ "orderable": false, "targets": 6 },
            { "orderable": true, "orderDataType": "part", "targets": 1 },
            { type: "currency", targets: 3 },
            { type: "currency", targets: 4 }';
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
