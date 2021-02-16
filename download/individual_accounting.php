<?php
require __DIR__ . '/../Common/init_page.php';

if (!($USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Style\Border;
use \PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use \PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;

$fpath = './member_list_email.xlsx';
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->getColumnDimension('A')->setWidth('12');
$sheet->getColumnDimension('F')->setWidth('25');
$sheet->getColumnDimension('G')->setWidth('40');
$sheet->getColumnDimension('H')->setWidth('12');
$sheet->getColumnDimension('I')->setWidth('25');
// $borderStyle = array(
//     'borders' => array(
//         'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
//     )
// );
// $sheet->getStyle('A1:F1')->applyFromArray($borderStyle);
$sheet->freezePane('A2');
$sheet->setCellValue('A1', "日付");
$sheet->setCellValue('B1', "学年");
$sheet->setCellValue('C1', "パート");
$sheet->setCellValue('D1', "姓");
$sheet->setCellValue('E1', "名");
$sheet->setCellValue('F1', "フリガナ");
$sheet->setCellValue('G1', "メールアドレス");
$sheet->setCellValue('H1', "ステータス");
$sheet->setCellValue('I1', "項目");
$sheet->setCellValue('J1', "金額");
$sheet->getStyle('A')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
$sheet->getStyle('J')->getNumberFormat()->setFormatCode('"¥"#,##0');
$number_conditional = new Conditional();
$number_conditional->setConditionType(Conditional::CONDITION_CELLIS);
$number_conditional->setOperatorType(Conditional::OPERATOR_LESSTHAN);
$number_conditional->addCondition('0');
$number_conditional->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);
$sheet->getStyle('J')->setConditionalStyles([$number_conditional]);
$ROW = 2;
$query = "SELECT individual_accounting_records.price, individual_accounting_records.datetime, individual_accounting_records.accounting_id, individual_accounting_records.list_id, CONCAT(IFNULL(individual_accounting_lists.name,''),IFNULL(accounting_lists.name,'')) AS name, profiles.grade, profiles.part, profiles.last_name, profiles.first_name, profiles.name_kana, users.status, users.email FROM individual_accounting_records LEFT OUTER JOIN individual_accounting_lists ON individual_accounting_records.list_id=individual_accounting_lists.list_id LEFT OUTER JOIN accounting_lists ON individual_accounting_records.accounting_id=accounting_lists.accounting_id INNER JOIN profiles ON individual_accounting_records.user_id=profiles.user_id INNER JOIN users ON profiles.user_id=users.user_id ORDER BY `datetime` ASC";
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
    } else if ($row['status'] == "RESIGNED") {
        $status = "退団";
    }
    $email = $row['email'];
    $date = $row['datetime'];
    $name = $row['name'];
    $price = $row['price'];
    $date = Date::PHPToExcel(date('Y/m/d', strtotime($date)));
    $sheet->setCellValue('A' . $ROW, $date);
    $sheet->setCellValue('B' . $ROW, $grade);
    $sheet->setCellValue('C' . $ROW, $part);
    $sheet->setCellValue('D' . $ROW, $last_name);
    $sheet->setCellValue('E' . $ROW, $first_name);
    $sheet->setCellValue('F' . $ROW, $kana);
    $sheet->setCellValue('G' . $ROW, $email);
    $sheet->setCellValue('H' . $ROW, $status);
    $sheet->setCellValue('I' . $ROW, $name);
    $sheet->setCellValue('J' . $ROW, $price);
    $ROW += 1;
}

$writer = new Xlsx($spreadsheet);
$writer->save($fpath);

$fname = 'クライネス個別会計データ（' . date("Y年m月d日 H時i分s秒") . '時点）.xlsx';
header('Content-Type: application/force-download');
header('Content-Length: ' . filesize($fpath));
header('Content-disposition: attachment; filename="' . $fname . '"');
readfile($fpath);
/** ログファイル作成の処理 **/
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->get_name() . "が個別会計データをダウンロードしました。\n", 3, __DIR__ . "/../Core/download.log");
unlink($fpath);
