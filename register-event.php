<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Требуется авторизация']);
    exit;
}

$eventId = (int)($_POST['event_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$eventId) {
    echo json_encode(['error' => 'Неверный ID мероприятия']);
    exit;
}

// Проверяем, существует ли мероприятие и активно ли оно
$stmt = $pdo->prepare("SELECT id, status FROM events WHERE id = ? AND status = 'active'");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    echo json_encode(['error' => 'Мероприятие не найдено или уже завершено']);
    exit;
}

// Проверяем, не записан ли уже студент
$stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->execute([$userId, $eventId]);
if ($stmt->fetch()) {
    echo json_encode(['error' => 'Вы уже записаны на это мероприятие']);
    exit;
}

// Записываем
try {
    $stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
    $stmt->execute([$userId, $eventId]);
    echo json_encode(['success' => true, 'message' => 'Вы успешно записаны!']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ошибка при записи: ' . $e->getMessage()]);
}
?>