<?php
require __DIR__ . '/../Common/init_page.php';

if (!($USER->isManager() || $USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Style\Border;

$fpath = './member_list_email.xlsx';
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->getColumnDimension('E')->setWidth('25');
$sheet->getColumnDimension('F')->setWidth('40');
// $borderStyle = array(
//     'borders' => array(
//         'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
//     )
// );
// $sheet->getStyle('A1:F1')->applyFromArray($borderStyle);
$sheet->freezePane('A2');
$sheet->setCellValue('A1', "学年");
$sheet->setCellValue('B1', "パート");
$sheet->setCellValue('C1', "姓");
$sheet->setCellValue('D1', "名");
$sheet->setCellValue('E1', "フリガナ");
$sheet->setCellValue('F1', "メールアドレス");
$sheet->setCellValue('G1', "ステータス");
$ROW = 2;
$query = "SELECT profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, users.status, users.email FROM profiles INNER JOIN users ON profiles.user_id=users.user_id ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    if ($row['status'] == 'RESIGNED') {
        continue;
    }
    $grade = $row['grade'];
    if ($row['part'] == 'S') {
        $part = "Soprano";
    } else if ($row['part'] == 'A') {
        $part = "Alto";
    } else if ($row['part'] == 'T') {
        $part = "Tenor";
    } else if ($row['part'] == 'B') {
        $part = "Bass";
    }
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $kana = $row['name_kana'];
    if ($row['status'] == "PRESENT") {
        $status = "在団";
    } else if ($row['status'] == "ABSENT") {
        $status = "休団";
    }
    $email = $row['email'];
    $sheet->setCellValue('A' . $ROW, $grade);
    $sheet->setCellValue('B' . $ROW, $part);
    $sheet->setCellValue('C' . $ROW, $last_name);
    $sheet->setCellValue('D' . $ROW, $first_name);
    $sheet->setCellValue('E' . $ROW, $kana);
    $sheet->setCellValue('F' . $ROW, $email);
    $sheet->setCellValue('G' . $ROW, $status);
    $ROW += 1;
}

$writer = new Xlsx($spreadsheet);
$writer->save($fpath);

$fname = 'クライネス最新名簿（' . date("Y年m月d日 H時i分s秒") . '時点）.xlsx';
header('Content-Type: application/force-download');
header('Content-Length: ' . filesize($fpath));
header('Content-disposition: attachment; filename="' . $fname . '"');
readfile($fpath);
/** ログファイル作成の処理 **/
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が団員名簿（メアドあり）をダウンロードしました。\n", 3, __DIR__ . "/../Core/download.log");
unlink($fpath);
