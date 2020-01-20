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
$fpath = './member_list.xlsx';
$book = new PHPExcel();
$sheet = $book->getActiveSheet();
$sheet->getColumnDimension('E')->setWidth('25');
$borderStyle = array(
    'borders' => array(
        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
    )
);
$sheet->getStyle('A1:E1')->applyFromArray($borderStyle);
$sheet->freezePane('A2');
$sheet->setCellValue('A1', "学年");
$sheet->setCellValue('B1', "パート");
$sheet->setCellValue('C1', "姓");
$sheet->setCellValue('D1', "名");
$sheet->setCellValue('E1', "フリガナ");
$ROW = 2;
$query = "SELECT * FROM members WHERE status != 2 ORDER BY grade ASC, CASE WHEN part LIKE 'S' THEN 1 WHEN part LIKE 'A' THEN 2 WHEN part LIKE 'T' THEN 3 WHEN part LIKE 'B' THEN 4 END ASC, kana ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $last_name_u = $row['last_name'];
    $first_name_u = $row['first_name'];
    $grade_u = $row['grade'];
    $part_u = $row['part'];
    $kana_u = $row['kana'];
    $sheet->setCellValue('A' . $ROW, $grade_u);
    $sheet->setCellValue('B' . $ROW, $part_u);
    $sheet->setCellValue('C' . $ROW, $last_name_u);
    $sheet->setCellValue('D' . $ROW, $first_name_u);
    $sheet->setCellValue('E' . $ROW, $kana_u);
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
error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . "が団員名簿（メアドなし）をダウンロードしました。\n", 3, "/home/chorkleines/www/member/mypage/Core/download.log");
