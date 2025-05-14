<?php
include __DIR__ . '/header.php'; 
session_start();
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: admin_login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $cancel_reason = $_POST['cancel_reason'] ?? null;

    if ($status === 'cancelled' && empty($cancel_reason)) {
        $error = "Для отмены заявки обязательно укажите причину!";
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, cancel_reason = ? WHERE id = ?");
        $stmt->execute([$status, $status === 'cancelled' ? $cancel_reason : null, $order_id]);
        $success = "Статус заявки обновлён!";
    }
}
$stmt = $pdo->query("SELECT o.*, u.fio, u.phone AS user_phone, u.email, s.name AS service_name
                     FROM orders o
                     JOIN users u ON o.user_id = u.id
                     LEFT JOIN services s ON o.service_id = s.id
                     ORDER BY o.date_time DESC");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <style>
        table {border-collapse: collapse;}
        th, td {border: 1px solid #888; padding: 5px;}
    </style>
</head>
<body>
    <h2>Админ-панель: заявки</h2>
    <a href="admin_logout.php">Выйти из админ-панели</a>
    <?php
    if (!empty($error)) echo "<p style='color:red;'>$error</p>";
    if (!empty($success)) echo "<p style='color:green;'>$success</p>";
    ?>
    <table>
        <tr>
            <th>ID</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Email</th>
            <th>Адрес</th>
            <th>Услуга</th>
            <th>Иная услуга</th>
            <th>Дата и время</th>
            <th>Тип оплаты</th>
            <th>Статус</th>
            <th>Причина отмены</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['fio']) ?></td>
            <td><?= htmlspecialchars($order['user_phone']) ?></td>
            <td><?= htmlspecialchars($order['email']) ?></td>
            <td><?= htmlspecialchars($order['address']) ?></td>
            <td><?= htmlspecialchars($order['service_name']) ?></td>
            <td><?= htmlspecialchars($order['other_service']) ?></td>
            <td><?= htmlspecialchars($order['date_time']) ?></td>
            <td><?= $order['payment_type'] === 'cash' ? 'Наличные' : 'Карта' ?></td>
            <td><?= statusText($order['status']) ?></td>
            <td><?= htmlspecialchars($order['cancel_reason']) ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status" onchange="toggleCancelReason(this, <?= $order['id'] ?>)">
                        <option value="in_progress" <?= $order['status']=='in_progress'?'selected':'' ?>>В работе</option>
                        <option value="completed" <?= $order['status']=='completed'?'selected':'' ?>>Выполнено</option>
                        <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>Отменено</option>
                    </select>
                    <input type="text" name="cancel_reason" id="cancel_reason_<?= $order['id'] ?>"
                        placeholder="Причина отмены"
                        value="<?= htmlspecialchars($order['cancel_reason']) ?>"
                        style="display:<?= $order['status']=='cancelled'?'inline':'none' ?>;">
                    <button type="submit">Сохранить</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <script>
        function toggleCancelReason(select, id) {
            var input = document.getElementById('cancel_reason_' + id);
            if (select.value === 'cancelled') {
                input.style.display = 'inline';
            } else {
                input.style.display = 'none';
                input.value = '';
            }
        }
    </script>
</body>
</html>
<?php
function statusText($status) {
    switch ($status) {
        case 'new': return 'Новая';
        case 'in_progress': return 'В работе';
        case 'completed': return 'Выполнено';
        case 'cancelled': return 'Отменено';
        default: return $status;
    }
}
?>
