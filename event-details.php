<?php
session_start();
require_once 'db.php';

// Получаем все мероприятия
$stmt = $pdo->query("
    SELECT e.*, u.surname AS org_surname, u.name AS org_name
    FROM events e
    JOIN users u ON e.organizer_id = u.id
    ORDER BY e.date DESC
");
$events = $stmt->fetchAll();

// Для авторизованного студента — получаем его записи
$registeredEvents = [];
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'student') {
    $stmt = $pdo->prepare("
        SELECT event_id FROM registrations WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $registeredEvents = array_column($stmt->fetchAll(), 'event_id');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мероприятия</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Мероприятия</h1>
        <nav>
            <ul>
                <li><a href="index.php">Назад на главную</a></li>
            </ul>
        </nav>
    </header>

    <section class="container">
        <?php
        $active = [];
        $completed = [];

        foreach ($events as $event) {
            if ($event['status'] === 'active') {
                $active[] = $event;
            } else {
                $completed[] = $event;
            }
        }
        ?>

        <?php if (!empty($active)): ?>
            <h2>Ближайшие мероприятия</h2>
            <div class="events-list">
                <?php foreach ($active as $event): ?>
                    <div class="event-item">
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <img src="img/<?= $event['id'] ?>.png" alt="<?= htmlspecialchars($event['title']) ?>" class="event-image">
                        <p><strong>Дата:</strong> <?= date('d.m.Y', strtotime($event['date'])) ?></p>
                        <p><strong>Место:</strong> <?= htmlspecialchars($event['location']) ?></p>
                        <p><?= htmlspecialchars($event['description']) ?></p>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'student'): ?>
                            <?php if (in_array($event['id'], $registeredEvents)): ?>
                                <button class="button cancel-register-btn" data-event-id="<?= $event['id'] ?>">Отменить участие</button>
                            <?php else: ?>
                                <button class="button register-btn" data-event-id="<?= $event['id'] ?>">Записаться</button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($completed)): ?>
    <h2>Прошедшие мероприятия</h2>
    <div class="events-list">
        <?php foreach ($completed as $event): ?>
            <div class="event-item">
                <h3><?= htmlspecialchars($event['title']) ?></h3>
                <img src="img/<?= $event['id'] ?>.png" alt="<?= htmlspecialchars($event['title']) ?>" class="event-image">
                <p><strong>Дата:</strong> <?= date('d.m.Y', strtotime($event['date'])) ?></p>
                <p><strong>Место:</strong> <?= htmlspecialchars($event['location']) ?></p>
                <p><?= htmlspecialchars($event['description']) ?></p>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'student'): ?>
                    <?php if (in_array($event['id'], $registeredEvents)): ?>
                        <button class="button" disabled>Участвовал</button>
                    <?php else: ?>
                        <p class="ended-message">Данное мероприятие закончилось</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2025 Мероприятия</p>
    </footer>

    <script>
        // Запись
        document.querySelectorAll('.register-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const eventId = this.dataset.eventId;
                const res = await fetch('register-event.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'event_id=' + eventId
                });
                const result = await res.json();
                if (result.success) {
                    alert(result.message);
                    this.textContent = 'Отменить участие';
                    this.classList.remove('register-btn');
                    this.classList.add('cancel-register-btn');
                    this.onclick = null; // удаляем старый обработчик
                    // Назначаем новый
                    this.addEventListener('click', handleCancel);
                } else {
                    alert('Ошибка: ' + result.error);
                }
            });
        });

        // Отмена
        function handleCancel() {
            const eventId = this.dataset.eventId;
            if (!confirm('Отменить участие?')) return;

            fetch('api/cancel-registration.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'event_id=' + eventId
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    this.textContent = 'Записаться';
                    this.classList.remove('cancel-register-btn');
                    this.classList.add('register-btn');
                    this.removeEventListener('click', handleCancel);
                    this.addEventListener('click', function() {
                        // Повторно назначаем обработчик записи
                        const eventId = this.dataset.eventId;
                        // ... (можно вынести в функцию, но для простоты оставим)
                        fetch('register-event.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'event_id=' + eventId
                        })
                        .then(res => res.json())
                        .then(result => {
                            if (result.success) {
                                alert(result.message);
                                this.textContent = 'Отменить участие';
                                this.classList.remove('register-btn');
                                this.classList.add('cancel-register-btn');
                                this.removeEventListener('click', arguments.callee);
                                this.addEventListener('click', handleCancel);
                            } else {
                                alert('Ошибка: ' + result.error);
                            }
                        });
                    });
                } else {
                    alert('Ошибка: ' + result.error);
                }
            });
        }

        // Назначаем обработчики для кнопок "Отменить" при загрузке
        document.querySelectorAll('.cancel-register-btn').forEach(btn => {
            btn.addEventListener('click', handleCancel);
        });
    </script>
</body>
</html>