<?php

require '../PHPSpreadsheet/vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
session_start();
include_once("../vendor/connect.php");
include("../vendor/connectPDO.php");



$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (!isset($_GET['token']) && !isset($_SESSION['user']['token'])) {
    die("Требуется токен для доступа к данным погоды.");
}


if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
} elseif (isset($_SESSION['user']['token'])) {
    $token = $_SESSION['user']['token'];
    
}

$checkTokenQuery = "SELECT id, requests_count, last_request_time FROM users WHERE token = ?";
$checkTokenStmt = $pdo->prepare($checkTokenQuery);
$checkTokenStmt->execute([$token]);
$user = $checkTokenStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Неверный токен.");
}


$currentTime = time();
$lastRequestTime = strtotime($user['last_request_time']);
$timeDifference = $currentTime - $lastRequestTime;

if ($timeDifference < 3600 && $user['requests_count'] >= 2000) {
    die("Превышен лимит запросов. Попробуйте позже.");
}


$requestsCount = ($timeDifference < 3600) ? $user['requests_count'] + 1 : 1;
$updateTokenQuery = "UPDATE users SET requests_count = ?, last_request_time = NOW() WHERE token = ?";
$updateTokenStmt = $pdo->prepare($updateTokenQuery);
$updateTokenStmt->execute([$requestsCount, $token]);






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



$start_date =  date("Y-m-d", strtotime("-1 month"));
$end_date =  date("Y-m-d");

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
    $excel_file_name = 'WeatherLastMonth:'. $start_date .' - '. $end_date .'.xlsx';
    exportAndDownloadExcel($data, $excel_file_name);
} else {
    echo "Нет данных для экспорта.";
}

$connect->close();
?>
