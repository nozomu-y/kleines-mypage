<?php
if (isset($_POST['submit'])) {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'check') !== false) {
            $id_u = explode('_', $key)[1];
            if ($value == 1) {
                require_once('../../dbconnect.php');
                $query = "SELECT * FROM fee_record_$id_u WHERE id = $fee_id";
                $result = $mysqli->query($query);
                $row_cnt = $result->num_rows;
                if (!$result) {
                    print('クエリーが失敗しました1。' . $mysqli->error);
                    $mysqli->close();
                    exit();
                }
                if ($row_cnt == 0) {
                    require_once('../../dbconnect.php');
                    $query = "INSERT INTO fee_record_$id_u (id, price) VALUES ('$fee_id', '$price')";
                    $result = $mysqli->query($query);
                    if (!$result) {
                        print('クエリーが失敗しました2。' . $mysqli->error);
                        $mysqli->close();
                        exit();
                    }
                }
            } else if ($value == 0) {
                require_once('../../dbconnect.php');
                $query = "DELETE FROM fee_record_$id_u WHERE id = $fee_id";
                $result = $mysqli->query($query);
                if (!$result) {
                    print('クエリーが失敗しました3。' . $mysqli->error);
                    $mysqli->close();
                    exit();
                }
            }
        }
    }
}
header('Location: /member/mypage/admin/');
exit();
