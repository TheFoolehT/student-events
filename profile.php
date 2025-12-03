<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$full_name = htmlspecialchars($user['surname'] . ' ' . $user['name'] . ' ' . ($user['patronymic'] ?? ''));
$initials = mb_substr($user['surname'], 0, 1) . mb_substr($user['name'], 0, 1);
$reg_date = date('d.m.Y', strtotime($user['created_at']));

// Получаем записанные мероприятия с event_id
$stmt = $pdo->prepare("
    SELECT e.title, e.date, e.status, e.id as event_id
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    WHERE r.user_id = ?
    ORDER BY e.date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$registrations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет — Студент</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .container {
            flex: 1;
        }
        .avatar {
            width: 60px;
            height: 60px;
            background-color: #ffa500;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            margin-right: 15px;
        }
        .profile-info {
            display: flex;
            align-items: flex-start;
            margin: 20px 0;
        }
        .info-block p {
            margin: 5px 0;
        }
        .my-events ul {
            list-style: none;
            padding: 0;
        }
        .my-events li {
            padding: 8px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cancel-btn {
            padding: 4px 8px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.85em;
        }
        .cancel-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <header>
        <h1>Личный кабинет</h1>
        <nav>
            <ul>
                <li><a href="index.php">Вернуться на главную</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="section">
            <h2>Мой профиль</h2>
            <div class="profile-info">
                <div class="avatar"><?= $initials ?></div>
                <div class="info-block">
                    <p><strong>ФИО:</strong> <?= $full_name ?></p>
                    <p><strong>Группа:</strong> <?= htmlspecialchars($user['group'] ?? '—') ?></p>
                    <p><strong>Дата регистрации:</strong> <?= $reg_date ?></p>
                    <p><strong>Электронная почта:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                </div>
            </div>

            <div class="my-events">
                <h3>Мои мероприятия</h3>
                <?php if ($registrations): ?>
                    <ul>
                        <?php foreach ($registrations as $reg): ?>
                            <li>
                                <span>
                                    <strong><?= htmlspecialchars($reg['title']) ?></strong>
                                    (<?= date('d.m.Y', strtotime($reg['date'])) ?>)
                                    — <?= $reg['status'] === 'active' ? 'Активно' : 'Завершено' ?>
                                </span>
                                <?php if ($reg['status'] === 'active'): ?>
                                    <button class="cancel-btn" data-event-id="<?= $reg['event_id'] ?>">Отменить</button>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Вы ещё не записаны ни на одно мероприятие.</p>
                    <a href="event-details.php" class="btn">Посмотреть мероприятия</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        © 2025 Учет участия студентов в воспитательных мероприятиях
    </footer>

    <script>
        document.querySelectorAll('.cancel-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const eventId = this.dataset.eventId;
                if (!confirm('Отменить участие в мероприятии?')) return;
                
                const res = await fetch('api/cancel-registration.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'event_id=' + eventId
                });
                const result = await res.json();
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Ошибка: ' + result.error);
                }
            });
        });
    </script>
</body>
</html>