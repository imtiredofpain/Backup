<?php
include("../config.php");
$api_key = 'c91a6a7808e32e9cf46648abce772f14'; 

$city = "" . CITY . ""; 
echo "Город: " . CITY . "<br>" ;

$url = "http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$api_key&units=metric";
$response = file_get_contents($url);
$data = json_decode($response, true);
$temperature = $data['main']['temp'];
$humidity = $data['main']['humidity'];
$description = $data['weather'][0]['description'];


require_once("../vendor/connect.php");


if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Шаг 4: Выполнение запроса INSERT
$sql = "INSERT INTO weather (city, temperature, humidity, description)
        VALUES ('$city', $temperature, $humidity, '$description')";

if ($connect->query($sql) === TRUE) {
    echo "Record added successfully";

} else {
    echo "Error: " . $sql . "<br>" . $connect->error;
}

$connect->close();
?>
