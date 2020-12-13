<?php
require __DIR__ . '/../Common/init_page.php';

require __DIR__ . '/../vendor/autoload.php';
$fpath = './member_list.xlsx';
$book = new PHPExcel();
$sheet = $book->getActiveSheet();
$sheet->getColumnDimension('E')->setWidth('25');
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
$sheet->setCellValue('F1', "ステータス");
$ROW = 2;
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
    $sheet->setCellValue('F' . $ROW, $account->get_status());
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
error_log("[" . date('Y/m/d H:i:s') . "] " . $USER->name . "が団員名簿（メアドなし）をダウンロードしました。\n", 3, __DIR__ . "/../Core/download.log");
