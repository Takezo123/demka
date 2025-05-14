
<?php
include __DIR__ . '/header.php'; 
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($login === 'adminka' && $password === 'password') {
        $_SESSION['is_admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Неверный логин или пароль администратора";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход администратора</title>
</head>
<body>
    <h2>Вход администратора</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <label>Логин:</label><br>
        <input type="text" name="login" required><br>
        <label>Пароль:</label><br>
        <input type="password" name="password" required><br>
        <button type="submit">Войти</button>
    </form>
</body>
</html>