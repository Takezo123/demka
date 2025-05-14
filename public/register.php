<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
</head>
<body>
    <h2>Регистрация</h2>
    <form action="register.php" method="POST">
        <label>ФИО:</label><br>
        <input type="text" name="fio" pattern="[А-Яа-яЁё\s]+" required><br>

        <label>Телефон:</label><br>
        <input type="text" name="phone" pattern="\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}" placeholder="+7(XXX)-XXX-XX-XX" required><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br>

        <label>Логин:</label><br>
        <input type="text" name="login" required><br>

        <label>Пароль:</label><br>
        <input type="password" name="password" minlength="6" required><br>

        <button type="submit" name="register">Зарегистрироваться</button>
    </form>
</body>
</html>
<?php
include __DIR__ . '/header.php'; 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Подключение к базе данных
    require_once '../config/db.php';

    // Получаем и фильтруем данные
    $fio = trim($_POST['fio']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    // Простейшая серверная валидация
    $errors = [];
    if (!preg_match('/^[А-Яа-яЁё\s]+$/u', $fio)) $errors[] = "ФИО только кириллица и пробелы";
    if (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) $errors[] = "Телефон не соответствует формату";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Некорректный email";
    if (strlen($password) < 6) $errors[] = "Пароль должен быть не менее 6 символов";

    // Проверка уникальности логина
    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->execute([$login]);
    if ($stmt->fetch()) $errors[] = "Логин уже занят";

    if (empty($errors)) {
        // Хэшируем пароль
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Вставляем пользователя в базу
        $stmt = $pdo->prepare("INSERT INTO users (fio, phone, email, login, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fio, $phone, $email, $login, $hash]);
        echo "Регистрация успешна!";
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>
