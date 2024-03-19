<?php
//require_once('../config.php');

$dsn = 'mysql:host=185.105.110.6;dbname=p540095_test';
$username = 'p540095_test';
$password = 'HxLiwc8ZtT';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>
