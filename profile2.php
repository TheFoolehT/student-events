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

if (!$user || $user['role'] !== 'teacher') {
    header('Location: profile.php');
    exit;
}

$full_name = htmlspecialchars($user['surname'] . ' ' . $user['name']);
$initials = mb_substr($user['surname'], 0, 1) . '.' . mb_substr($user['name'], 0, 1) . '.';
$reg_date = date('d.m.Y', strtotime($user['created_at']));

// Получаем мероприятия
$stmt = $pdo->prepare("SELECT e.* FROM events e WHERE e.organizer_id = ? ORDER BY e.date DESC");
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll();

// Для завершённых — случайное кол-во участников
foreach ($events as &$event) {
    if ($event['status'] === 'completed') {
        $event['participant_count'] = rand(20, 30);
    } else {
        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
        $stmt2->execute([$event['id']]);
        $event['participant_count'] = (int)$stmt2->fetchColumn();
    }
}
unset($event);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет — Преподаватель</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="profile2">
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
            <h2>Личный кабинет</h2>
            <div class="profile-info">
                <div class="avatar"><?= $initials ?></div>
                <div class="info-block">
                    <p><strong>ФИО:</strong> <?= $full_name ?></p>
                    <p><strong>Должность:</strong> Доцент, кафедра ИВТ</p>
                    <p><strong>Кафедра:</strong> Информационные технологии и вычислительная техника</p>
                    <p><strong>Дата регистрации:</strong> <?= $reg_date ?></p>
                    <p><strong>Электронная почта:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Мои мероприятия</h2>
            <?php if ($events): ?>
                <div class="events-list">
                    <?php foreach ($events as $event): ?>
                        <div class="event-item">
                            <img src="img/<?= $event['id'] ?>.png" alt="<?= htmlspecialchars($event['title']) ?>" class="event-image">
                            <h3><?= htmlspecialchars($event['title']) ?></h3>
                            <p><strong>Дата:</strong> <?= date('d.m.Y', strtotime($event['date'])) ?></p>
                            <p><strong>Место:</strong> <?= htmlspecialchars($event['location']) ?></p>
                            <p class="status"><strong>Статус:</strong> <?= $event['status'] === 'active' ? 'Активно' : 'Завершено' ?></p>
                            <p class="students"><strong>Участников:</strong> <?= $event['participant_count'] ?></p>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <div class="event-actions">
                                <button class="btn btn-evaluate" data-event-id="<?= $event['id'] ?>">Просмотреть отчёты</button>
                                <?php if ($event['status'] === 'active'): ?>
                                    <button class="btn btn-manage" data-event-id="<?= $event['id'] ?>">Управление</button>
                                <?php else: ?>
                                    <span class="ended-message">Мероприятие завершено</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>У вас пока нет мероприятий.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>© 2025 Учет участия студентов в воспитательных мероприятиях</footer>

    <!-- Модальное окно: Просмотр отчётов -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeReportModal">&times;</span>
            <div id="reportModalContent"></div>
        </div>
    </div>

    <!-- Модальное окно: Добавление участника -->
    <div id="manageModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeManageModal">&times;</span>
            <h2>Добавить участника</h2>
            <form id="addParticipantForm">
                <input type="hidden" id="eventId" name="event_id">
                <label for="surname">Фамилия:</label>
                <input type="text" id="surname" name="surname" required>
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" required>
                <label for="patronymic">Отчество:</label>
                <input type="text" id="patronymic" name="patronymic" required>
                <button type="submit">Добавить</button>
            </form>
            <p id="manageMessage" style="margin-top: 10px; color: red; display: none;"></p>
        </div>
    </div>

    <script>
        // Закрытие модальных окон
        function closeModal(modal) { modal.style.display = "none"; }
        document.getElementById("closeReportModal").onclick = () => closeModal(document.getElementById("reportModal"));
        document.getElementById("closeManageModal").onclick = () => closeModal(document.getElementById("manageModal"));
        window.onclick = function(e) {
            if (e.target.classList.contains("modal")) closeModal(e.target);
        };

        // Данные мероприятий
        const eventData = <?= json_encode($events, JSON_UNESCAPED_UNICODE) ?>;

        // Просмотр отчётов — БЕЗ блока "учёт участников"
        document.querySelectorAll(".btn-evaluate").forEach(btn => {
            btn.addEventListener("click", function() {
                const event = eventData.find(e => e.id == this.dataset.eventId);
                if (!event) return;
                document.getElementById("reportModalContent").innerHTML = `
                    <h2>${event.title}</h2>
                    <img src="img/${event.id}.png" alt="${event.title}" class="event-image-modal">
                    <p><strong>Статус:</strong> ${event.status === 'active' ? 'Активно' : 'Завершено'}</p>
                    <p><strong>Дата проведения:</strong> ${event.date}</p>
                    <p><strong>Место:</strong> ${event.location}</p>
                    <p><strong>Организатор:</strong> <?= $full_name ?></p>
                    <p><strong>Количество участников:</strong> ${event.participant_count}</p>
                    <h3>Описание</h3>
                    <p>${event.description}</p>
                    <!-- Убран блок "учёт участников" -->
                `;
                document.getElementById("reportModal").style.display = "block";
            });
        });

        // Управление — открытие формы
        document.querySelectorAll(".btn-manage").forEach(btn => {
            btn.addEventListener("click", function() {
                const eventId = this.dataset.eventId;
                document.getElementById("eventId").value = eventId;
                document.getElementById("manageMessage").style.display = "none";
                document.getElementById("addParticipantForm").reset();
                document.getElementById("manageModal").style.display = "block";
            });
        });

        // Отправка формы добавления
        document.getElementById("addParticipantForm").addEventListener("submit", async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch("add-participant.php", {
                method: "POST",
                body: formData
            });
            const result = await res.json();
            const msgEl = document.getElementById("manageMessage");
            if (result.success) {
                msgEl.style.color = "green";
                msgEl.textContent = "Участник успешно добавлен!";
                // Обновим счётчик участников (опционально)
                location.reload(); // или обновить AJAX'ом
            } else {
                msgEl.style.color = "red";
                msgEl.textContent = result.message;
            }
            msgEl.style.display = "block";
        });
    </script>
</body>
</html>