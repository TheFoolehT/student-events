<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешён']);
    exit;
}

$identifier = trim($_POST['loginIdentifier'] ?? ''); // email или телефон
$password = $_POST['loginPassword'] ?? '';

if (!$identifier || !$password) {
    echo json_encode(['error' => 'Введите логин и пароль']);
    exit;
}

// Ищем пользователя по email или телефону
$stmt = $pdo->prepare("
    SELECT id, role, surname, name, email, phone, password_hash 
    FROM users 
    WHERE email = ? OR phone = ?
    LIMIT 1
");
$stmt->execute([$identifier, $identifier]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['error' => 'Пользователь не найден']);
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    echo json_encode(['error' => 'Неверный пароль']);
    exit;
}

// Авторизуем пользователя
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_surname'] = $user['surname'];

echo json_encode([
    'success' => true,
    'message' => 'Вход выполнен',
    'redirect' => 'profile.php' // или куда перенаправлять
]);
?>