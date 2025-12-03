<?php
session_start();

// Параметры подключения (по умолчанию в Open Server)
$host = 'localhost';       // Open Server использует localhost
$dbname = 'student_events'; // Название вашей БД (создайте в phpMyAdmin)
$username = 'root';        // По умолчанию в Open Server
$password = '';            // По умолчанию пустой пароль

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>