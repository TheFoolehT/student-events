// Получение элементов модальных окон и кнопок для их открытия/закрытия
var loginModal = document.getElementById("loginModal");
var registerModal = document.getElementById("registerModal");
var profileModal = document.getElementById("profileModal");

var loginBtn = document.getElementById("loginBtn");
var registerBtn = document.getElementById("registerBtn");
var profileBtn = document.getElementById("profileBtn");

var closeLogin = document.getElementById("closeLogin");
var closeRegister = document.getElementById("closeRegister");
var closeProfile = document.getElementById("closeProfile");

var openRegisterFromLogin = document.getElementById("openRegisterFromLogin");
var openLoginFromRegister = document.getElementById("openLoginFromRegister");

// Открытие модальных окон по клику
loginBtn.onclick = function() {
    loginModal.style.display = "block"; // Открыть окно входа
}
registerBtn.onclick = function() {
    registerModal.style.display = "block"; // Открыть окно регистрации
}
profileBtn.onclick = function() {
    profileModal.style.display = "block"; // Открыть окно профиля
}

// Закрытие модальных окон по клику на кнопку крестика
closeLogin.onclick = function() {
    loginModal.style.display = "none";
}
closeRegister.onclick = function() {
    registerModal.style.display = "none";
}
closeProfile.onclick = function() {
    profileModal.style.display = "none";
}

// Закрытие модальных окон при клике вне их области
window.onclick = function(event) {
    if (event.target == loginModal) {
        loginModal.style.display = "none";
    }
    if (event.target == registerModal) {
        registerModal.style.display = "none";
    }
    if (event.target == profileModal) {
        profileModal.style.display = "none";
    }
}

// Переключение между формами регистрации и входа
openRegisterFromLogin.onclick = function() {
    loginModal.style.display = "none";
    registerModal.style.display = "block";
}
openLoginFromRegister.onclick = function() {
    registerModal.style.display = "none";
    loginModal.style.display = "block";
}

// Переключение с профиля на регистрацию
document.getElementById("openRegisterFromProfile").onclick = function() {
    profileModal.style.display = "none";
    registerModal.style.display = "block";
}

// Закрытие окна профиля по кнопке
document.getElementById("closeProfileModal").onclick = function() {
    profileModal.style.display = "none";
}

// Обработка отправки формы регистрации
document.getElementById("registerForm").onsubmit = function(event) {
    event.preventDefault(); // Отменяем стандартную отправку
    var role = document.getElementById("role").value;
    var surname = document.getElementById("registerSurname").value;
    var name = document.getElementById("registerName").value;
    var group = document.getElementById("registerGroup").value;
    var phone = document.getElementById("registerPhone").value;
    var email = document.getElementById("registerEmail").value;

    // Проверка заполненности всех полей
    if (!role || !surname || !name || !group || !phone || !email) {
        alert("Пожалуйста, заполните все поля.");
        return;
    }

    // Проверка корректности номера телефона по шаблону
    var phonePattern = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
    if (!phonePattern.test(phone)) {
        alert("Номер телефона должен быть в формате: +7 (___) ___-__-__.");
        return;
    }

    alert("Регистрация выполнена!");
    registerModal.style.display = "none"; // Закрываем окно регистрации
};

// Обработка входа
document.getElementById("loginForm").onsubmit = function(event) {
    event.preventDefault(); // Отменяем стандартную отправку
    var identifier = document.getElementById("loginIdentifier").value;
    var password = document.getElementById("loginPassword").value;

    // Проверка, что введен корректный email или телефон
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var phonePattern = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;

    if (!emailPattern.test(identifier) && !phonePattern.test(identifier)) {
        alert("Пожалуйста, введите корректный email или номер телефона.");
        return;
    }

    alert("Вход выполнен!");
    loginModal.style.display = "none"; // Закрываем окно входа
};