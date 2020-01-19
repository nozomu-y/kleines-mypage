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

if (isset($_GET['fee_id'])) {
    $id = $_GET['fee_id'];
} else {
    header('Location: /member/mypage/admin/accounting/');
    exit();
}

$query = "SELECT * FROM fee_list WHERE id=$id";
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

<?php
if ($user->admin == 1 || $user->admin == 3) {
?>
    <script>
        function getPaid(id, name, i_a_price, price) {
            if (i_a_price > 0) {
                // 個別会計を使える場合（値が正）
                var paid_cash = window.prompt(name + "さんの集金処理を行います。\n現金で受け取る金額を指定してください。\n個別会計残高：" + i_a_price + "\n集金額：" + price);
                if ((paid_cash != "") && (paid_cash != null) && !isNaN(paid_cash)) {
                    if (Number(paid_cash) < 0) {
                        window.alert("非負整数を入力して下さい。");
                    } else if (Number(paid_cash) > Number(price)) {
                        window.alert("入力された金額が集金額よりも多いです。");
                    } else if (Number(price) - Number(paid_cash) > Number(i_a_price)) {
                        window.alert("個別会計の残高が足りません。");
                    } else if (Number(price) - Number(paid_cash) <= Number(i_a_price)) {
                        window.alert("現金で" + String(paid_cash) + "円徴収してください。\n残りの" + String(price - paid_cash) + "円は個別会計から差し引きます。");
                        var result = window.confirm(name + "さんの提出状況を既納に変更して、集金完了メールを送信します。");
                        if (result) {
                            var form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/member/mypage/admin/accounting/change_status_paid.php';

                            var form_fee_id = document.createElement('input');
                            form_fee_id.type = 'hidden';
                            form_fee_id.name = 'fee_id';
                            form_fee_id.value = '<?php echo $fee_list->id; ?>';
                            form.appendChild(form_fee_id);

                            var form_price = document.createElement('input');
                            form_price.type = 'hidden';
                            form_price.name = 'price';
                            form_price.value = price;
                            form.appendChild(form_price);

                            var form_user_id = document.createElement('input');
                            form_user_id.type = 'hidden';
                            form_user_id.name = 'user_id';
                            form_user_id.value = id;
                            form.appendChild(form_user_id);

                            var form_paid_cash = document.createElement('input');
                            form_paid_cash.type = 'hidden';
                            form_paid_cash.name = 'paid_cash';
                            form_paid_cash.value = String(paid_cash);
                            form.appendChild(form_paid_cash);

                            document.body.appendChild(form);

                            form.submit();
                        }
                    }
                } else if ((paid_cash != "") && isNaN(paid_cash)) {
                    window.alert("値は数字で入力してください。");
                } else if (paid_cash == "") {
                    window.alert("値を入力してください。");
                }
            } else {
                // 個別会計を使えない場合（値が負）
                window.alert("個別会計の残高がありません。\n現金で" + price + "円徴収してください。");
                var result = window.confirm(name + "さんの提出状況を既納に変更して、集金完了メールを送信します。");
                if (result) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/member/mypage/admin/accounting/change_status_paid.php';

                    var form_fee_id = document.createElement('input');
                    form_fee_id.type = 'hidden';
                    form_fee_id.name = 'fee_id';
                    form_fee_id.value = '<?php echo $fee_list->id; ?>';
                    form.appendChild(form_fee_id);

                    var form_price = document.createElement('input');
                    form_price.type = 'hidden';
                    form_price.name = 'price';
                    form_price.value = price;
                    form.appendChild(form_price);

                    var form_user_id = document.createElement('input');
                    form_user_id.type = 'hidden';
                    form_user_id.name = 'user_id';
                    form_user_id.value = id;
                    form.appendChild(form_user_id);

                    var form_paid_cash = document.createElement('input');
                    form_paid_cash.type = 'hidden';
                    form_paid_cash.name = 'paid_cash';
                    form_paid_cash.value = String(price);
                    form.appendChild(form_paid_cash);

                    document.body.appendChild(form);

                    form.submit();
                }
            }
        }
    </script>
    <script>
        function getUnpaid(id, name) {
            var result = window.confirm(name + "さんの提出状況を未納に変更しますか？");
            if (result) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/member/mypage/admin/accounting/change_status_unpaid.php';

                var form_fee_id = document.createElement('input');
                form_fee_id.type = 'hidden';
                form_fee_id.name = 'fee_id';
                form_fee_id.value = '<?php echo $fee_list->id; ?>';
                form.appendChild(form_fee_id);

                var form_user_id = document.createElement('input');
                form_user_id.type = 'hidden';
                form_user_id.name = 'user_id';
                form_user_id.value = id;
                form.appendChild(form_user_id);

                document.body.appendChild(form);

                form.submit();
            }
        }
    </script>
<?php
}
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">集金記録</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/member/mypage/admin/accounting/">集金記録一覧</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><? echo $fee_list->name; ?></li>
                </ol>
            </nav>
            <?php

            if (isset($_SESSION['mypage_fee_status'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo '<strong>' . $_SESSION['mypage_account_name'] . '</strong>の集金状況を<strong>' . $_SESSION['mypage_fee_status'] . '</strong>に変更しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_fee_status']);
                unset($_SESSION['mypage_account_name']);
            }
            ?>
            <div class="mb-4">
                <table id="accountingList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">学年</th>
                            <th class="text-nowrap">パート</th>
                            <th class="text-nowrap">氏名</th>
                            <?php
                            if ($user->admin == 1 || $user->admin == 3) {
                                echo '<th class="text-nowrap">変更</th>';
                            }
                            ?>
                            <th class="text-nowrap">提出状況</th>
                            <th class="text-nowrap">提出日時</th>
                            <th class="text-nowrap">金額</th>
                            <?php
                            if ($user->admin == 1 || $user->admin == 3) {
                                echo '<th class="text-nowrap">編集</th>';
                            }
                            ?>
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
                        $row_cnt = $result->num_rows;
                        while ($row = $result->fetch_assoc()) {
                            $account = new User($row);
                            $query = "SELECT * FROM fee_record_$account->id WHERE id = $fee_list->id";
                            $result_1 = $mysqli->query($query);
                            if (!$result_1) {
                                print('Query Failed : ' . $mysqli->error);
                                $mysqli->close();
                                exit();
                            }
                            if ($result_1->num_rows != 0) {
                                // if the list exists
                                $fee = new Fee($result_1->fetch_assoc());
                                if ($fee->paid()) {
                                    $disabled_paid = "disabled";
                                    $disabled_unpaid = "";
                                } else {
                                    $disabled_paid = "";
                                    $disabled_unpaid = "disabled";
                                }
                                echo '<tr>';
                                echo '<td class="text-nowrap">' . $account->grade . '</td>';
                                echo '<td class="text-nowrap">' . $account->get_part() . '</td>';
                                echo '<td class="text-nowrap"><span class="d-none">' . $account->kana . '</span>' . $account->name . '</td>';
                                if ($user->admin == 1 || $user->admin == 3) {
                                    echo '<td class="text-nowrap"><input type="button" id="paid_' . $id_u . '" name="paid" class="btn btn-secondary btn-sm" value="既納" Onclick="getPaid(\'' . $account->id . '\',\'' . $account->name . '\',\'' . $account->get_individual_accounting_total() . '\',\'' . $fee->price . '\');" ' . $disabled_paid . '> <input type="button" id="unpaid_' . $account->id . '" name="unpaid" class="btn btn-secondary btn-sm" value="未納" Onclick="getUnpaid(\'' . $account->id . '\',\'' . $account->name . '\');" ' . $disabled_unpaid . '></td>';
                                }
                                echo '<td class="text-nowrap">' . $fee->get_status() . '</td>';
                                echo '<td class="text-nowrap">' . $fee->get_submission_time() . '</td>';
                                echo '<td class="text-nowrap text-right">' . $fee->get_price() . '</td>';
                                if ($user->admin == 1 || $user->admin == 3) {
                                    echo '<td class="text-nowrap"><a href="./edit_price.php?id=' . $account->id . '&fee_id=' . $fee_list->id . '" class="text-secondary"><u>編集</u></a></td>';
                                }
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        if ($user->admin == 1 || $user->admin == 3) {
        ?>
            <div class="col-xl-3 col-sm-12">
                <div class="list-group">
                    <a href="/member/mypage/admin/accounting/add_fee_list/subject.php?fee_id=<?php echo $fee_list->id; ?>" class="list-group-item list-group-item-action">集金リストの編集</a>
                    <a href="#" class="list-group-item list-group-item-action">集金対象者の選択</a>
                    <a href="#" class="list-group-item list-group-item-action">Morbi leo risus</a>
                    <a href="#" class="list-group-item list-group-item-action">Porta ac consectetur ac</a>
                    <a href="#" class="list-group-item list-group-item-action disabled">Vestibulum at eros</a>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<?php
$script = '<script>';
if ($user->admin == 1 || $user->admin == 3) {
    $script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [{ "orderable": false, "targets": 3 },
            { "orderable": false, "targets": 7 },
            { "orderable": true, "orderDataType": "part", "targets": 1 },
            { type: "currency", targets: 6 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
} else {
    $script .= '$(document).ready(function() {
    $("#accountingList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        columnDefs: [{ "orderable": true, "orderDataType": "part", "targets": 1 },
            { type: "currency", targets: 5 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
}

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
