<?php
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Учет участия студентов в воспитательных мероприятиях</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <!-- Заголовок и навигация -->
    <header>
        <h1></h1>
        <nav>
            <ul>
                <!-- Кнопка личного кабинета -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    $profilePage = ($_SESSION['user_role'] === 'teacher') ? 'profile2.php' : 'profile.php';
                    ?>
                    <li><a href="<?= htmlspecialchars($profilePage) ?>"><img src="img/icon-user.png" alt="Личный кабинет" class="icon" />Личный кабинет</a></li>
                <?php else: ?>
                    <li><a href="#profile" id="profileBtn"><img src="img/icon-user.png" alt="Личный кабинет" class="icon" />Личный кабинет</a></li>
                <?php endif; ?>

                <li><a href="#about">О нас</a></li>
                <li><a href="#events">Мероприятия</a></li>
                <li><a href="#contact">Контакты</a></li>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="#login" id="loginBtn">Вход</a></li>
                    <li><a href="#register" id="registerBtn">Регистрация</a></li>
                <?php else: ?>
                    <li><a href="logout.php">Выйти</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Отладочное сообщение -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div style="background: green; color: white; padding: 10px; margin: 10px;">
            ✅ Вы авторизованы как <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?> (<?= $_SESSION['user_role'] ?>)
        </div>
    <?php else: ?>
        <div style="background: red; color: white; padding: 10px; margin: 10px;">
            ❌ Не авторизованы
        </div>
    <?php endif; ?>

    <!-- Остальной HTML без изменений... -->
    <!-- Раздел "О нас" -->
    <section id="about">
        <div class="content-card">
            <h2>О нас</h2>
            <p>Наша цель — учет участия студентов в воспитательных мероприятиях для повышения их активности и вовлеченности.</p>
        </div>
    </section>

    <!-- Раздел "Мероприятия" -->
    <section id="events">
        <h2>Ближайшие мероприятия</h2>
        <div class="events-list">
            <div class="event-item">
                <h3>Экологический квест: забота о природе</h3>
                <img src="img/1.png" alt="Экологический квест" class="event-image" />
                <p><strong>Дата:</strong> 25 мая 2025</p>
                <p><strong>Место:</strong> Центральный парк</p>
                <p>Квест-игра, в ходе которой студенты выполняют задания, связанные с экологией, уборкой территории и пропагандой экологической ответственности.</p>
                <a href="event-details.php" class="button">Узнать подробнее</a>
            </div>
            <div class="event-item">
                <h3>Мастер-класс ценностей: формируем моральные ориентиры</h3>
                <img src="img/2.png" alt="Мастер-класс ценностей" class="event-image" />
                <p><strong>Дата:</strong> 30 мая 2025</p>
                <p><strong>Место:</strong> Кабинет 207</p>
                <p>Лекции и практические занятия, направленные на формирование у студентов важных моральных ценностей и этических принципов.</p>
                <a href="event-details.php" class="button">Узнать подробнее</a>
            </div>
        </div>

        <h2>Прошедшие мероприятия</h2>
        <div class="events-list">
            <div class="event-item">
                <h3>Молодежная волонтерская акция: помощь ближнему</h3>
                <img src="img/3.png" alt="Волонтерская акция" class="event-image" />
                <p><strong>Дата:</strong> 15 апреля 2025</p>
                <p><strong>Место:</strong> Социальный центр</p>
                <p>Добровольческая деятельность, направленная на помощь пожилым, нуждающимся или участникам социальных программ.</p>
                <a href="event-details.php" class="button">Узнать подробнее</a>
            </div>
            <div class="event-item">
                <h3>День добрых дел: вместе создаем добрососедство</h3>
                <img src="img/4.png" alt="День добрых дел" class="event-image" />
                <p><strong>Дата:</strong> 20 марта 2025</p>
                <p><strong>Место:</strong> Местный парк</p>
                <p>Мероприятие, направленное на развитие у студентов навыков взаимопомощи и добрососедства через выполнение совместных добрых дел и благотворительных акций.</p>
                <a href="event-details.php" class="button">Узнать подробнее</a>
            </div>
        </div>
    </section>

    <!-- Контакты -->
    <section id="contact">
        <div class="content-card">
            <h2>Контакты</h2>
            <p><strong>Email:</strong> info@example.com</p>
            <p><strong>Телефон:</strong> +7 (123) 456-78-90</p>
        </div>
    </section>

    <footer>
        <p>© 2025 Учет участия студентов в воспитательных мероприятиях</p>
    </footer>

    <!-- Модальное окно входа -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeLogin">×</span>
            <h2>Вход</h2>
            <form class="modal-form" id="loginForm">
                <label for="loginIdentifier">Логин:</label>
                <input type="text" id="loginIdentifier" name="loginIdentifier" required placeholder="example@mail.com или +7 (___) ___-__-__" />
                <label for="loginPassword">Пароль:</label>
                <input type="password" id="loginPassword" name="loginPassword" required />
                <button type="submit">Войти</button>
            </form>
            <p>Нет аккаунта? <a href="#" id="openRegisterFromLogin">Зарегистрируйтесь</a></p>
        </div>
    </div>

 <!-- Модальное окно для регистрации -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeRegister">×</span>
        <h2>Регистрация</h2>
        <form class="modal-form" id="registerForm">
            <label for="role">Роль:</label>
            <select id="role" name="role" required>
                <option value="" disabled selected>Выберите роль</option>
                <option value="student">Студент</option>
                <option value="teacher">Преподаватель</option>
            </select>
            <label for="registerSurname">Фамилия:</label>
            <input type="text" id="registerSurname" name="registerSurname" required />
            <label for="registerName">Имя:</label>
            <input type="text" id="registerName" name="registerName" required />
            <label for="registerPatronymic">Отчество:</label>
            <input type="text" id="registerPatronymic" name="registerPatronymic" required />
            <label for="registerGroup">Группа:</label>
            <input type="text" id="registerGroup" name="registerGroup" required />
            
            <label for="registerPassword">Пароль:</label>
            <input type="password" id="registerPassword" name="registerPassword" minlength="6" required />
            <label for="registerPasswordConfirm">Подтвердите пароль:</label>
            <input type="password" id="registerPasswordConfirm" name="registerPasswordConfirm" minlength="6" required />
            
            <label for="registerPhone">Номер телефона:</label>
            <input
                type="tel"
                id="registerPhone"
                name="registerPhone"
                required
                pattern="\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}"
                placeholder="+7 (___) ___-__-__"
                value="+7 ("
            />
            
            <label for="registerEmail">Email:</label>
            <input
                type="email"
                id="registerEmail"
                name="registerEmail"
                required
                placeholder="example@mail.com"
            />
            <button type="submit">Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href="#" id="openLoginFromRegister">Войдите здесь</a></p>
    </div>
</div>

    <!-- Модальное окно профиля (для неавторизованных) -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeProfile">×</span>
            <p>Вы не зарегистрированы. Хотите зарегистрироваться?</p>
            <button id="openRegisterFromProfile">Зарегистрироваться</button>
            <button id="closeProfileModal">Закрыть</button>
        </div>
    </div>

    <!-- Скрипты -->
    <script>
        // Телефон
        const phoneInput = document.getElementById("registerPhone");
        phoneInput.value = "+7 (";
        phoneInput.addEventListener("keydown", function(e) {
            if ([46, 8, 9, 27, 13, 37, 38, 39, 40].indexOf(e.keyCode) !== -1) return;
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
                if (!this.dataset.alerted) {
                    alert("В поле телефона можно вводить только цифры.");
                    this.dataset.alerted = "true";
                    setTimeout(() => delete this.dataset.alerted, 2000);
                }
            }
        });
        phoneInput.addEventListener("input", function() {
            let digits = this.value.replace(/\D/g, "");
            if (digits.length > 10) digits = digits.slice(0, 10);
            let formatted = "+7 (";
            if (digits.length >= 1) formatted += digits.slice(0, 3);
            if (digits.length >= 4) formatted += ") " + digits.slice(3, 6);
            if (digits.length >= 7) formatted += "-" + digits.slice(6, 8);
            if (digits.length >= 9) formatted += "-" + digits.slice(8, 10);
            this.value = formatted;
        });
        phoneInput.addEventListener("paste", function(e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pasted.replace(/\D/g, '').slice(0, 10);
            let newValue = "+7 (";
            if (digits.length >= 1) newValue += digits.slice(0, 3);
            if (digits.length >= 4) newValue += ") " + digits.slice(3, 6);
            if (digits.length >= 7) newValue += "-" + digits.slice(6, 8);
            if (digits.length >= 9) newValue += "-" + digits.slice(8, 10);
            this.value = newValue;
        });

        // Модальные окна (простые)
        document.getElementById('profileBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('profileModal').style.display = 'block';
        });
        document.getElementById('loginBtn')?.addEventListener('click', () => document.getElementById('loginModal').style.display = 'block');
        document.getElementById('registerBtn')?.addEventListener('click', () => document.getElementById('registerModal').style.display = 'block');
        document.querySelectorAll('.close').forEach(el => el.onclick = function() {
            this.closest('.modal').style.display = 'none';
        });
        window.onclick = function(e) {
            if (e.target.classList.contains('modal')) e.target.style.display = 'none';
        };
        document.getElementById('openRegisterFromLogin')?.addEventListener('click', () => {
            document.getElementById('loginModal').style.display = 'none';
            document.getElementById('registerModal').style.display = 'block';
        });
        document.getElementById('openLoginFromRegister')?.addEventListener('click', () => {
            document.getElementById('registerModal').style.display = 'none';
            document.getElementById('loginModal').style.display = 'block';
        });
        document.getElementById('openRegisterFromProfile')?.addEventListener('click', () => {
            document.getElementById('profileModal').style.display = 'none';
            document.getElementById('registerModal').style.display = 'block';
        });
        document.getElementById('closeProfileModal')?.addEventListener('click', () => {
            document.getElementById('profileModal').style.display = 'none';
        });

        // AJAX: Регистрация
        document.getElementById('registerForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('register.php', { method: 'POST', body: formData });
            const result = await res.json();
            if (result.success) {
                alert(result.message);
                document.getElementById('registerModal').style.display = 'none';
                this.reset();
                document.getElementById('registerPhone').value = '+7 (';
            } else {
                alert('Ошибка: ' + result.error);
            }
        });

        // AJAX: Вход → перезагрузка страницы!
        document.getElementById('loginForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const res = await fetch('login.php', { method: 'POST', body: formData });
            const result = await res.json();
            if (result.success) {
                alert(result.message);
                window.location.reload(); // ← КЛЮЧЕВАЯ СТРОКА
            } else {
                alert('Ошибка: ' + result.error);
            }
        });
    </script>
</body>
</html>