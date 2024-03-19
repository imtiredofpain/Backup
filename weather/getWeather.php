<?php
include("vendor/connectPDO.php");

try {
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT city, description, temperature, humidity, recorded_at FROM weather ORDER BY recorded_at DESC LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute();
    
    $weatherData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($weatherData as $data) {
        echo "Город: " . $data['city'] . "<br>";
        echo "Описание: " . $data['description'] . "<br>";
        echo "Температура: " . $data['temperature'] . "°C<br>";
        echo "Влажность: " . $data['humidity'] . "%<br>";
        echo "Время: " . $data['recorded_at'] . "<br><br>";
    }
} catch (PDOException $e) {
    
    echo "Ошибка: " . $e->getMessage();
}
?>
