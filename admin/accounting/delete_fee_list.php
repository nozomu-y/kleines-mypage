<?php
if (isset($_POST['delete'])) {
    $check = $_POST['check'];
    foreach ($check as $value) {
        require_once('../../dbconnect.php');
        $query = "SELECT * FROM fee_list WHERE id = $value";
        $result = $mysqli->query($query);
        while ($row = $result->fetch_assoc()) {
            $fee_name_tmp = $row['name'];
        }
        if (!$result) {
            print('クエリーが失敗しました。' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        require_once('../../dbconnect.php');
        $query = "DELETE FROM fee_list WHERE id = $value";
        $result = $mysqli->query($query);
        if (!$result) {
            print('クエリーが失敗しました。' . $mysqli->error);
            $mysqli->close();
            exit();
        }
        require_once('../../dbconnect.php');
        $query = "SELECT * FROM members ORDER BY id";
        $result_1 = $mysqli->query($query);
        while ($row_1 = $result_1->fetch_assoc()) {
            $id_u = $row_1['id'];
            require_once('../../dbconnect.php');
            $query = "DELETE FROM fee_record_$id_u WHERE id = $value";
            $result_2 = $mysqli->query($query);
            if (!$result_2) {
                print('クエリーが失敗しました。' . $mysqli->error);
                $mysqli->close();
                exit();
            }
        }
    }
    /** ログファイル作成の処理 **/
    date_default_timezone_set('Asia/Tokyo');
    error_log("[" . date('Y/m/d H:i:s') . "] " . $last_name . $first_name . "が集金リスト「" . $fee_name_tmp . "」を削除しました。\n", 3, "../core/fee.log");
    header('Location: index.php');
    exit();
}
