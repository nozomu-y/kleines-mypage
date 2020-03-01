<?php
if (!isset($_GET['list_id'])) {
    header('Location: /member/mypage/admin/eticket/');
    exit();
} else {
    $list_id = $_GET['list_id'];
    $list_id = substr($list_id, 0, 5);
    header('Location: /member/mypage/admin/eticket/detail.php?list_id=' . $list_id);
    exit();
}
