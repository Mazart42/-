<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'orders';

$cms = new mysqli($host, $user, $password, $database);

if ($cms->connect_error) {
    die("Ошибка подключения: " . $cms->connect_error);
}

$cms->set_charset("utf8mb4");
?>