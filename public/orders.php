<?php
include __DIR__ . '/header.php'; 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT o.*, s.name AS service_name
    FROM orders o
    LEFT JOIN services s ON o.service_id = s.id
    WHERE o.user_id = ?
    ORDER BY o.date_time DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<h2>Мои заявки</h2>
<table border="1">
    <tr>
        <th>Адрес</th>
        <th>Услуга</th>
        <th>Дата и время</th>
        <th>Статус</th>
    </tr>
    <?php foreach ($orders as $order): ?>
    <tr>
        <td><?=htmlspecialchars($order['address'])?></td>
        <td>
            <?php
                if (!empty($order['service_name'])) {
                    echo htmlspecialchars($order['service_name']);
                } elseif (!empty($order['other_service'])) {
                    echo htmlspecialchars($order['other_service']);
                } else {
                    echo 'Не указано';
                }
            ?>
        </td>
        <td><?=htmlspecialchars($order['date_time'])?></td>
        <td><?=htmlspecialchars($order['status'])?></td>
    </tr>
    <?php endforeach; ?>
</table>
