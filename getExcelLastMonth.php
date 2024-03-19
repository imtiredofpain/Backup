<?php

require '../PHPSpreadsheet/vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include_once("../vendor/connect.php");


function exportAndDownloadExcel($data, $filename) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($data, null, 'A1');

    $writer = new Xlsx($spreadsheet);
    
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'. $filename .'"');
    header('Cache-Control: max-age=0');


    $writer->save('php://output');
    exit;
}

$current_date = date("Y-m-d");
$one_month_ago = date("Y-m-d", strtotime("-1 month"));

$start_date = date("Y-m-d");
$end_date = date("Y-m-d", strtotime("-1 month"));

$start_date = mysqli_real_escape_string($connect, $start_date);
$end_date = mysqli_real_escape_string($connect, $end_date);

$query = "SELECT city, temperature, description, humidity, recorded_at FROM weather WHERE recorded_at BETWEEN '$start_date' AND '$end_date'";

$result = $connect->query($query);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array_values($row);
    }
}

if (!empty($data)) {
    $excel_file_name = 'weather_data.xlsx';
    exportAndDownloadExcel($data, $excel_file_name);
} else {
    echo "Нет данных для экспорта.";
}

$connect->close();
?>
