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

if (!($user->admin == 1 || $user->admin == 3)) {
    header('Location: /member/mypage/');
    exit();
}

if (isset($_POST['name'])) {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $price = $_POST['price'];
} else {
    header('Location: /member/mypage/admin/individual_accounting/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">個別会計管理</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/individual_accounting/">個別会計管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $name ?></li>
                </ol>
            </nav>
            <form method="post" action="./add_multiple.php" name="form" class="mb-4">
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM members ORDER BY CASE WHEN part LIKE 'S' THEN 1 WHEN part LIKE 'A' THEN 2 WHEN part LIKE 'T' THEN 3 WHEN part LIKE 'B' THEN 4 END ASC, grade ASC, kana ASC";
                            $result = $mysqli->query($query);
                            if (!$result) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            while ($row = $result->fetch_assoc()) {
                                $account = new User($row);
                                if ($account->status != 2) {
                                    echo '<tr>';
                                    echo '<td><div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="check_' . $account->id . '"></div></td>';
                                    echo '<td class="text-nowrap">' . $account->grade . '</td>';
                                    echo '<td class="text-nowrap">' . $account->get_part() . '</td>';
                                    echo '<td class="text-nowrap">' . $account->name . '</td>';
                                    echo '<td class="text-nowrap">' . $account->get_status() . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <input type="hidden" name="date" value="<?php echo $date; ?>">
                <input type="hidden" name="price" value="<?php echo $price; ?>">
                <button type="submit" class="btn btn-primary" name="submit">一括追加</button>
                <a class="btn btn-secondary" href="/member/mypage/admin/individual_accounting/" role="button">キャンセル</a>
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
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
