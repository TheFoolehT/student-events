-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 03 2025 г., 12:38
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `student_events`
--
CREATE DATABASE IF NOT EXISTS `student_events` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `student_events`;

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` enum('active','completed') DEFAULT 'active',
  `date` date DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `organizer_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `status`, `date`, `location`, `organizer_id`) VALUES
(1, 'Экологический квест: забота о природе', 'Квест-игра, в ходе которой студенты выполняют задания, связанные с экологией, уборкой территории и пропагандой экологической ответственности.', 'active', '2025-05-25', 'Центральный парк', 3),
(2, 'Мастер-класс ценностей: формируем моральные ориентиры', 'Лекции и практические занятия, направленные на формирование у студентов важных моральных ценностей и этических принципов.', 'active', '2025-05-30', 'Кабинет 207', 3),
(3, 'Молодежная волонтерская акция: помощь ближнему', 'Добровольческая деятельность, направленная на помощь пожилым, нуждающимся или участникам социальных программ.', 'completed', '2025-04-15', 'Социальный центр', 3),
(4, 'День добрых дел: вместе создаем добрососедство', 'Мероприятие, направленное на развитие у студентов навыков взаимопомощи и добрососедства через выполнение совместных добрых дел и благотворительных акций.', 'completed', '2025-03-20', 'Местный парк', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `registrations`
--

DROP TABLE IF EXISTS `registrations`;
CREATE TABLE `registrations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `event_id` int NOT NULL,
  `registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `registrations`
--

INSERT INTO `registrations` (`id`, `user_id`, `event_id`, `registered_at`) VALUES
(13, 4, 2, '2025-12-03 09:31:02');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL,
  `role` enum('student','teacher') NOT NULL,
  `surname` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `patronymic` varchar(100) DEFAULT NULL,
  `group` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `role`, `surname`, `name`, `patronymic`, `group`, `phone`, `email`, `password_hash`, `created_at`) VALUES
(3, 'teacher', 'Иванов', 'Сергей', 'Петрович', NULL, '77777777777', 'testtset@gmail.com', '$2y$10$dWfcVnJOowd6qJIRKybS7O9XWkhArOhjuWUMC3AybgvrvTI.dTxxW', '2025-11-19 09:58:02'),
(4, 'student', 'Кузнецова', 'Анастасия', 'Владимировна', 'ИН-23-20', '77777777777', 'testloltset@gmail.com', '$2y$10$9wXKyUQG.7XKYfs4EECYXeCoUniZCwFBbHOvEuquIIC5vP0n/JF2u', '2025-11-24 08:45:02');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Индексы таблицы `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_registration` (`user_id`,`event_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
