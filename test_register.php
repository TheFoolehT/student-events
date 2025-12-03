<?php
session_start();
require_once 'db.php';

$data = [
    'role' => 'student',
    'registerSurname' => 'Трубин',
    'registerName' => 'Вадим',
    'registerGroup' => 'ив-21-43',
    'registerPhone' => '+7 (777) 777-77-77',
    'registerEmail' => 'test@example.com',
    'registerPassword' => '123456',
    'registerPasswordConfirm' => '123456'
];

// Копируем код из register.php
$role = trim($data['role'] ?? '');
$surname = trim($data['registerSurname'] ?? '');
$name = trim($data['registerName'] ?? '');
$group = $role === 'student' ? trim($data['registerGroup'] ?? '') : null;
$phone = trim($data['registerPhone'] ?? '');
$email = trim($data['registerEmail'] ?? '');
$password = $data['registerPassword'] ?? '';
$passwordConfirm = $data['registerPasswordConfirm'] ?? '';

if (!$role || !$surname || !$name || !$phone || !$email || !$password) {
    die('Ошибка: все поля обязательны');
}

if ($role === 'student' && empty($group)) {
    die('Ошибка: укажите группу');
}

if ($password !== $passwordConfirm) {
    die('Ошибка: пароли не совпадают');
}

if (strlen($password) < 6) {
    die('Ошибка: пароль должен быть не менее 6 символов');
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die('Ошибка: email уже занят');
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (role, surname, name, `group`, phone, email, password_hash)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$role, $surname, $name, $group, $phone, $email, $hash]);

    echo "✅ Успешно добавлено!";
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
?>