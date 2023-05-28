<?php 
# Объявление параметров для подключения к базе данных
$server = 'localhost'; 
$username = 'root'; 
$password = ''; 
$dbname = 'webjox';
# Подключение к серверу mysql
$connect = mysqli_connect($server, $username, $password, $dbname);
# Выбор базы данных
mysqli_set_charset($connect, "utf8mb4");
mysqli_select_db($connect, $dbname);
# Установка русской локали для названий дней недели
mysqli_query($connect, "SET lc_time_names = 'ru_RU';" );
if(!$connect){
    die('Ошибка подключения к базе данных');
}
?>