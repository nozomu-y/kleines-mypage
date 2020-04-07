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

require_once("/home/chorkleines/www/member/mypage/download/PHPExcel-1.8/Classes/PHPExcel.php");
$fpath = './member_list_email.xlsx';
$book = new PHPExcel();
$sheet = $book->getActiveSheet();
$sheet->getColumnDimension('E')->setWidth('25');
$sheet->getColumnDimension('F')->setWidth('40');
$borderStyle = array(
    'borders' => array(
        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    )
);
$sheet->getStyle('A1:F1')->applyFromArray($borderStyle);
$sheet->freezePane('A2');
$sheet->setCellValue('A1', "学年");
$sheet->setCellValue('B1', "パート");
$sheet->setCellValue('C1', "姓");
$sheet->setCellValue('D1', "名");
$sheet->setCellValue('E1', "フリガナ");
$sheet->setCellValue('F1', "メールアドレス");
$sheet->setCellValue('G1', "ステータス");
$ROW = 2;
// $query = "SELECT * FROM members WHERE status != 2 ORDER BY grade ASC, CASE WHEN part LIKE 'S' THEN 1 WHEN part LIKE 'A' THEN 2 WHEN part LIKE 'T' THEN 3 WHEN part LIKE 'B' THEN 4 END ASC, kana ASC";
$query = "SELECT * FROM members WHERE status != 2 ORDER BY grade ASC, CASE WHEN part LIKE 'S' THEN 1 WHEN part LIKE 'A' THEN 2 WHEN part LIKE 'T' THEN 3 WHEN part LIKE 'B' THEN 4 END ASC, kana ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $account = new User($row);
    $sheet->setCellValue('A' . $ROW, $account->grade);
    $sheet->setCellValue('B' . $ROW, $account->get_part());
    $sheet->setCellValue('C' . $ROW, $account->last_name);
    $sheet->setCellValue('D' . $ROW, $account->first_name);
    $sheet->setCellValue('E' . $ROW, $account->kana);
    $sheet->setCellValue('F' . $ROW, $account->email);
    $sheet->setCellValue('G' . $ROW, $account->get_status());
    $ROW += 1;
}

$writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
$writer->save($fpath);

$fname = 'クライネス最新名簿（' . date("Y年m月d日H時i分s秒") . '現在）.xlsx';
header('Content-Type: application/force-download');
header('Content-Length: ' . filesize($fpath));
header('Content-disposition: attachment; filename="' . $fname . '"');
readfile($fpath);
/** ログファイル作成の処理 **/
error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が団員名簿（メアドあり）をダウンロードしました。\n", 3, "/home/chorkleines/www/member/mypage/Core/download.log");
