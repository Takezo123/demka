
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
</head>
<body>
<form action="login.php" method="POST">
  <label>Логин:</label>
  <input type="text" name="login" required>
  <label>Пароль:</label>
  <input type="password" name="password" required>
  <button type="submit">Войти</button>
</form>
</body>
</html>

<?php
include __DIR__ . '/header.php'; 
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Авторизация успешна
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fio'] = $user['fio'];
        header('Location: orders.php'); // Перенаправление на страницу с заявками
        exit;
    } else {
        echo "Неверный логин или пароль";
    }
}
?>
