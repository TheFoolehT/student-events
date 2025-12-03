<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещён']);
    exit;
}

$surname = trim($_POST['surname'] ?? '');
$name = trim($_POST['name'] ?? '');
$patronymic = trim($_POST['patronymic'] ?? '');
$eventId = (int)($_POST['event_id'] ?? 0);

if (!$surname || !$name || !$patronymic || !$eventId) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны']);
    exit;
}

// Поиск по ФИО + роль = student
$stmt = $pdo->prepare("
    SELECT id FROM users 
    WHERE surname = ? AND name = ? AND patronymic = ? AND role = 'student'
");
$stmt->execute([$surname, $name, $patronymic]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Данный пользователь не существует']);
    exit;
}

$userId = $user['id'];

// Проверка, не записан ли уже
$stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->execute([$userId, $eventId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Данный участник уже записан на мероприятие']);
    exit;
}

// Запись
try {
    $stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
    $stmt->execute([$userId, $eventId]);
    echo json_encode(['success' => true, 'message' => 'Участник добавлен']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка при записи']);
}
?>