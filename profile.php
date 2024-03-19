<?php
session_start();
if (!$_SESSION['user']) {
    header('Location: /');
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Авторизация и регистрация</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>


    <form>      
        <h2 style="margin: 10px 0;"><?= $_SESSION['user']['full_name'] ?></h2>
        <a href="#"><?= $_SESSION['user']['email'] ?></a>
        <?php 
            print_r($_SESSION['user']['token']);
        ?>
        <a href="vendor/logout.php" class="logout">Выход</a>         
    </form>
    <?php
        include("weather/getWeather.php");
    ?>
    <form>
    <a style="margin: 10pxs 10px;" href ="weather/setWeather.php">Записать</a>
    <a style="margin: 10px 10px;" href ="weather/RestApi.php">RestApiWeather</a>
    <a style="margin: 10px 10px;" href ="weather/getAnalyzeWeather.php">RestApiAnalyze</a>
    <a style="margin: 10px 10px;" href ="weather/getExcelLastMonth.php">RestApiExcelLastMonth</a>
    </form>    
    <form  action="weather/getExcel.php" method="post">
        <label for="start_date">Начальная дата:</label>
        <input type="date" id="start_date" name="start_date" required>
        <label for="end_date">Конечная дата:</label>
        <input type="date" id="end_date" name="end_date" required>
        <button type="submit">Excel</button>
    </form> 
   

</body>
</html>