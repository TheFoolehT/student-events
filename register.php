<?php
error_log("=== Начало регистрации ===");
error_log("Данные: " . print_r($_POST, true));

session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешён']);
    exit;
}

// Получаем данные
$data = $_POST;
$role = trim($data['role'] ?? '');
$surname = trim($data['registerSurname'] ?? '');
$name = trim($data['registerName'] ?? '');
$patronymic = trim($data['registerPatronymic'] ?? ''); // ← Отчество
$group = $role === 'student' ? trim($data['registerGroup'] ?? '') : null;
$phone = trim($data['registerPhone'] ?? '');
$email = trim($data['registerEmail'] ?? '');
$password = $data['registerPassword'] ?? '';
$passwordConfirm = $data['registerPasswordConfirm'] ?? '';

// Валидация
if (!$role || !$surname || !$name || !$patronymic || !$phone || !$email || !$password) {
    echo json_encode(['error' => 'Все поля обязательны для заполнения']);
    exit;
}

if ($role === 'student' && empty($group)) {
    echo json_encode(['error' => 'Укажите группу']);
    exit;
}

if ($password !== $passwordConfirm) {
    echo json_encode(['error' => 'Пароли не совпадают']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['error' => 'Пароль должен быть не менее 6 символов']);
    exit;
}

// Проверка уникальности email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['error' => 'Пользователь с таким email уже существует']);
    exit;
}

// Очистка телефона — оставляем только цифры
$phone = preg_replace('/\D/', '', $phone);
if (strlen($phone) < 11) {
    echo json_encode(['error' => 'Неверный формат телефона']);
    exit;
}
if (substr($phone, 0, 1) !== '7') {
    $phone = '7' . substr($phone, 1);
}

// Хэшируем пароль
$hash = password_hash($password, PASSWORD_DEFAULT);

// Сохраняем в БД
try {
    $stmt = $pdo->prepare("
        INSERT INTO users (role, surname, name, patronymic, `group`, phone, email, password_hash)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$role, $surname, $name, $patronymic, $group, $phone, $email, $hash]);

    echo json_encode(['success' => true, 'message' => 'Регистрация прошла успешно!']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Ошибка при регистрации: ' . $e->getMessage()]);
}
?>