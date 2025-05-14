<?php
session_start();
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
$is_user = isset($_SESSION['user_id']);
?>
<nav style="background:#f0f0f0;padding:10px;">
    <a href="/public/index.php">Главная</a>
    <?php if ($is_user): ?>
        | <a href="/public/orders.php">Мои заявки</a>
        | <a href="/public/create_order.php">Создать заявку</a>
        | <a href="/public/logout.php">Выйти</a>
    <?php elseif ($is_admin): ?>
        | <a href="/public/admin.php">Админ-панель</a>
        | <a href="/public/admin_logout.php">Выйти из админ-панели</a>
    <?php else: ?>
        | <a href="/public/register.php">Регистрация</a>
        | <a href="/public/login.php">Вход</a>
        | <a href="/public/admin_login.php">Вход для администратора</a>
    <?php endif; ?>
</nav>
