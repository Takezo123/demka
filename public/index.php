
<?php
include __DIR__ . '/header.php'; 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<?php
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY date_time DESC");
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
            // Получить название услуги по service_id
            $stmt2 = $pdo->prepare("SELECT name FROM services WHERE id = ?");
            $stmt2->execute([$order['service_id']]);
            echo htmlspecialchars($stmt2->fetchColumn());
            ?>
        </td>
        <td><?=htmlspecialchars($order['date_time'])?></td>
        <td><?=htmlspecialchars($order['status'])?></td>
    </tr>
    <?php endforeach; ?>
</table>
