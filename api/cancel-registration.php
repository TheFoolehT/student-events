<?php
session_start();
require_once '../db.php';

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

// Проверяем запись
$stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->execute([$userId, $eventId]);
if (!$stmt->fetch()) {
    echo json_encode(['error' => 'Вы не записаны на это мероприятие']);
    exit;
}

// Удаляем
try {
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$userId, $eventId]);
    echo json_encode(['success' => true, 'message' => 'Вы отменили участие']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ошибка при отмене']);
}
?>