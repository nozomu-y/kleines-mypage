<?php

require __DIR__ . '/../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$fpath = './member_list_email.xlsx';
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->getColumnDimension('E')->setWidth('25');
$sheet->getColumnDimension('F')->setWidth('40');
$sheet->getColumnDimension('G')->setWidth('12');
$sheet->getColumnDimension('H')->setWidth('25');
$sheet->getColumnDimension('I')->setWidth('12');
$sheet->getColumnDimension('L')->setWidth('15');
$sheet->getColumnDimension('M')->setWidth('15');
$sheet->getColumnDimension('N')->setWidth('12');
$sheet->getColumnDimension('O')->setWidth('20');
$sheet->freezePane('A2');
$sheet->setCellValue('A1', "学年");
$sheet->setCellValue('B1', "パート");
$sheet->setCellValue('C1', "姓");
$sheet->setCellValue('D1', "名");
$sheet->setCellValue('E1', "フリガナ");
$sheet->setCellValue('F1', "メールアドレス");
$sheet->setCellValue('G1', "ステータス");
$sheet->setCellValue('H1', "項目");
$sheet->setCellValue('I1', "支払い期限");
$sheet->setCellValue('J1', "管轄");
$sheet->setCellValue('K1', "金額");
$sheet->setCellValue('L1', "現金利用額");
$sheet->setCellValue('M1', "個別会計利用額");
$sheet->setCellValue('N1', "支払い状況");
$sheet->setCellValue('O1', "支払い日時");
$sheet->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
$sheet->getStyle('K')->getNumberFormat()->setFormatCode('"¥"#,##0');
$sheet->getStyle('L')->getNumberFormat()->setFormatCode('"¥"#,##0');
$sheet->getStyle('M')->getNumberFormat()->setFormatCode('"¥"#,##0');
$sheet->getStyle('O')->getNumberFormat()->setFormatCode('yyyy/mm/dd h:mm:ss');
$ROW = 2;
$query = <<<QUERY
    SELECT 
        accounting_records.price, accounting_records.paid_cash, accounting_records.datetime, accounting_lists.name, accounting_lists.deadline, accounting_lists.admin, profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, users.status, users.email
    FROM 
        accounting_records 
    LEFT OUTER JOIN accounting_lists ON accounting_records.accounting_id=accounting_lists.accounting_id 
    LEFT OUTER JOIN profiles ON accounting_records.user_id=profiles.user_id 
    LEFT OUTER JOIN users ON profiles.user_id=users.user_id 
    ORDER BY profiles.grade ASC, CASE WHEN profiles.part LIKE 'S' THEN 1 WHEN profiles.part LIKE 'A' THEN 2 WHEN profiles.part LIKE 'T' THEN 3 WHEN profiles.part LIKE 'B' THEN 4 END ASC, deadline ASC
QUERY;
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
while ($row = $result->fetch_assoc()) {
    $grade = $row['grade'];
    if ($row['part'] == 'S') {
        $part = "Soprano";
    } elseif ($row['part'] == 'A') {
        $part = "Alto";
    } elseif ($row['part'] == 'T') {
        $part = "Tenor";
    } elseif ($row['part'] == 'B') {
        $part = "Bass";
    }
    $last_name = $row['last_name'];
    $first_name = $row['first_name'];
    $kana = $row['name_kana'];
    if ($row['status'] == "PRESENT") {
        $status = "在団";
    } elseif ($row['status'] == "ABSENT") {
        $status = "休団";
    } elseif ($row['status'] == "RESIGNED") {
        $status = "退団";
    }
    $email = $row['email'];
    $date = $row['datetime'];
    $name = $row['name'];
    $price = $row['price'];
    $paid_cash = $row['paid_cash'];
    $deadline = $row['deadline'];
    $admin = $row['admin'];
    if ($row['admin'] == 'GENERAL') {
        $admin = "会計";
    } elseif ($row['admin'] == 'CAMP') {
        $admin = "合宿";
    }
    if ($date == null) {
        $date = "";
        $payment_status = "未納";
        $paid_individual_accounting = null;
    } else {
        $date = Date::PHPToExcel(date('Y/m/d H:i:s', strtotime($date)));
        $payment_status = "済";
        $paid_individual_accounting = $price - $paid_cash;
    }
    $deadline = Date::PHPToExcel(date('Y/m/d', strtotime($deadline)));
    $sheet->setCellValue('A' . $ROW, $grade);
    $sheet->setCellValue('B' . $ROW, $part);
    $sheet->setCellValue('C' . $ROW, $last_name);
    $sheet->setCellValue('D' . $ROW, $first_name);
    $sheet->setCellValue('E' . $ROW, $kana);
    $sheet->setCellValue('F' . $ROW, $email);
    $sheet->setCellValue('G' . $ROW, $status);
    $sheet->setCellValue('H' . $ROW, $name);
    $sheet->setCellValue('I' . $ROW, $deadline);
    $sheet->setCellValue('J' . $ROW, $admin);
    $sheet->setCellValue('K' . $ROW, $price);
    $sheet->setCellValue('L' . $ROW, $paid_cash);
    $sheet->setCellValue('M' . $ROW, $paid_individual_accounting);
    $sheet->setCellValue('N' . $ROW, $payment_status);
    $sheet->setCellValue('O' . $ROW, $date);
    $ROW += 1;
}

$writer = new Xlsx($spreadsheet);
$writer->save($fpath);

$fname = 'クライネス集金データ（' . date("Y年m月d日 H時i分s秒") . '時点）.xlsx';
header('Content-Type: application/force-download');
header('Content-Length: ' . filesize($fpath));
header('Content-disposition: attachment; filename="' . $fname . '"');
readfile($fpath);
/** ログファイル作成の処理 **/
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が集金データをダウンロードしました。\n", 3, __DIR__ . "/../Core/download.log");
unlink($fpath);
