<?php
session_start();
include("../vendor/connectPDO.php");

try {
 
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
    
    
    $query = "SELECT city, description, temperature, humidity, recorded_at FROM weather ORDER BY recorded_at DESC LIMIT 1";;
    
    $statement = $pdo->prepare($query);
    $statement->execute();
    $lastRecord = $statement->fetch(PDO::FETCH_ASSOC);
    


    if ($lastRecord === false) {
        die("Данные о погоде для этого пользователя не найдены.");
    }


    $weatherData = array(
        'city' => $lastRecord['city'],
        'weather_description' => $lastRecord['description'],
        'temperature' => $lastRecord['temperature'],
        'humidity' => $lastRecord['humidity'],
        'recorded_at' => $lastRecord['recorded_at']
    );
    
   
    $jsonWeatherData = json_encode($weatherData);
    
    
    header('Content-Type: application/json');
    echo $jsonWeatherData;
    
    } catch(PDOException $e) {
        die("Ошибка: " . $e->getMessage());
    }
?>
