<?php

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



$current_date = date("Y-m-d");
$one_month_ago = date("Y-m-d", strtotime("-1 month"));


$sql = "SELECT AVG(temperature) AS avg_temperature, AVG(humidity) AS avg_humidity FROM weather WHERE recorded_at BETWEEN '$one_month_ago' AND '$current_date'";
$result = $connect->query($sql);

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();
    
    $avg_temp = round($row["avg_temperature"], 2);
    $avg_hum = round($row["avg_humidity"], 2);
} 


$sql_1 = "SELECT description, COUNT(*) AS count FROM weather WHERE recorded_at BETWEEN '$one_month_ago' AND '$current_date' GROUP BY description ORDER BY count DESC LIMIT 1";
$result_1 = $connect->query($sql_1);

if ($result_1->num_rows > 0) {
    
    $row = $result_1->fetch_assoc();
    $average_description = $row["description"];
    
}  

$data = array(
    "avg_temperature" => $avg_temp,
    "avg_hum" => $avg_hum,
    "average_description" => $average_description 
);

if (!empty($data)){
    echo json_encode($data);
} else{
    echo json_encode(array("error" => "Данные за последний месяц отсутствуют"));
}
    

$connect->close();

?>
