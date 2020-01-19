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

if (isset($_GET['fee_id'])) {
    $fee_id = $_GET['fee_id'];
} else {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$query = "SELECT * FROM fee_list WHERE id='$fee_id'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$fee_list = new Fee_List($result->fetch_assoc());
if ($fee_list->admin != 3) {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金リスト</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/accounting/">集金リスト一覧</a></li>
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/accounting/detail.php?fee_id=<?php echo $fee_list->id ?>"><?php echo $fee_list->name ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">集金対象者の編集</li>
                </ol>
            </nav>
            <p>集金対象者を選択し、除外したい人の選択を解除してください。既納の場合は変更できません。</p>
            <form method="post" action="./change_subject.php" id="sampleform" class="mb-4">
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
                                $query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_id";
                                $result_1 = $mysqli->query($query);
                                if (!$result_1) {
                                    print('Query Failed : ' . $mysqli->error);
                                    $mysqli->close();
                                    exit();
                                }
                                $row_cnt = $result_1->num_rows;
                                if ($row_cnt != 0) {
                                    // when the user is already added to the list
                                    $fee = new Fee($result_1->fetch_assoc());
                                    if ($fee->datetime != NULL) {
                                        // if it is already paid
                                        $check = 'checked="checked"';
                                        $hidden = 'type="hidden"';
                                    } else {
                                        // if it is not paid yet
                                        $check = 'checked="checked"';
                                        $hidden = '';
                                    }
                                } else {
                                    // when the user is not added to the list
                                    $check = '';
                                    $hidden = '';
                                }

                                if ($user->status != 2) {
                                    echo '<tr>';
                                    echo '<td><div class="form-check form-check-inline"><input type="hidden" name="check_' . $account->id . '" value="0"><input ' . $hidden . ' class="form-check-input" ' . $check . ' type="checkbox" name="check_' . $account->id . '" value="1"></div></td>';
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
                <input type="hidden" name="fee_id" value="<?php echo $fee_list->id; ?>">
                <button type="submit" class="btn btn-primary" name="submit">集金対象に追加</button>
                <a class="btn btn-secondary" href="index.php" role="button">キャンセル</a>
            </form>
        </div>
    </div>
</div>

<?php
$script = '<script>
    function allcheck(tf) {
      var ElementsCount = document.sampleform.elements.length; // チェックボックスの数
      for (i = 0; i < ElementsCount; i++) {
        document.sampleform.elements[i].checked = tf; // ON・OFFを切り替え
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
