<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/accounting/');
    exit();
}

$fee_id = $_POST['fee_id'];
$query = "SELECT * FROM fee_list WHERE id = $fee_id";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$fee_list = new Fee_List($result->fetch_assoc());

foreach ($_POST as $key => $value) {
    if (strpos($key, 'check') !== false) {
        $account_id = explode('_', $key)[1];
        if ($value == 1) {
            $query = "SELECT * FROM fee_record_$account_id WHERE id = $fee_id";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            $row_cnt = $result->num_rows;
            if ($row_cnt == 0) {
                $query = "INSERT INTO fee_record_$account_id (id, price) VALUES ('$fee_id', '$fee_list->price')";
                $result = $mysqli->query($query);
                if (!$result) {
                    print('Query Failed : ' . $mysqli->error);
                    $mysqli->close();
                    exit();
                }
            }
        } else if ($value == 0) {
            $query = "DELETE FROM fee_record_$account_id WHERE id = $fee_id";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
        }
    }
}

$_SESSION['mypage_update_subject'] = '';

header('Location: ' . MYPAGE_ROOT . '/admin/accounting/detail.php?fee_id=' . $fee_id);
exit();
