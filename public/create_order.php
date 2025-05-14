<?php
include __DIR__ . '/header.php'; 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<form action="create_order.php" method="post">
  <h2>Создать новую заявку</h2>

  <label for="address">Адрес:*</label><br>
  <input type="text" id="address" name="address" required><br><br>

  <label for="phone">Телефон (+7(XXX)-XXX-XX-XX):*</label><br>
  <input type="text" id="phone" name="phone" pattern="\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}" placeholder="+7(XXX)-XXX-XX-XX" required><br><br>

  <label for="service">Вид услуги:*</label><br>
  <select id="service" name="service_id" required>
    <option value="">-- Выберите услугу --</option>
    <option value="1">Общий клининг</option>
    <option value="2">Генеральная уборка</option>
    <option value="3">Послестроительная уборка</option>
    <option value="4">Химчистка ковров и мебели</option>
  </select><br><br>

  <input type="checkbox" id="other_service_checkbox" name="other_service_checkbox" onchange="toggleOtherService()">
  <label for="other_service_checkbox">Иная услуга</label><br>

  <textarea id="other_service" name="other_service_text" placeholder="Опишите необходимую услугу" style="display:none;"></textarea><br><br>

  <label for="datetime">Желаемая дата и время:*</label><br>
  <input type="datetime-local" id="datetime" name="date_time" required><br><br>

  <label>Тип оплаты:*</label><br>
  <input type="radio" id="cash" name="payment_type" value="cash" required>
  <label for="cash">Наличные</label><br>
  <input type="radio" id="card" name="payment_type" value="card" required>
  <label for="card">Банковская карта</label><br><br>

  <button type="submit">Отправить заявку</button>
</form>

<script>
  function toggleOtherService() {
    const checkbox = document.getElementById('other_service_checkbox');
    const textarea = document.getElementById('other_service');
    textarea.style.display = checkbox.checked ? 'block' : 'none';
    if (!checkbox.checked) {
      textarea.value = '';
    }
  }
</script>
<?php
session_start();
require_once __DIR__ . '/../config/db.php'; // путь к файлу подключения к БД

// Проверяем, что пользователь авторизован
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    // Получаем и очищаем данные из формы
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $service_id = $_POST['service_id'] ?? '';
    $other_service_text = trim($_POST['other_service_text'] ?? '');
    $date_time = $_POST['date_time'] ?? '';
    $payment_type = $_POST['payment_type'] ?? '';

    $errors = [];

    // Валидация
    if ($address === '') {
        $errors[] = 'Адрес обязателен';
    }
    if (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors[] = 'Телефон должен быть в формате +7(XXX)-XXX-XX-XX';
    }
    if ($service_id === '') {
        $errors[] = 'Выберите вид услуги';
    }
    if ($service_id === 'other' && $other_service_text === '') {
        $errors[] = 'Опишите необходимую иную услугу';
    }
    if ($date_time === '') {
        $errors[] = 'Укажите дату и время';
    } elseif (strtotime($date_time) === false || strtotime($date_time) < time()) {
        $errors[] = 'Дата и время должны быть корректными и не в прошлом';
    }
    if ($payment_type !== 'cash' && $payment_type !== 'card') {
        $errors[] = 'Выберите тип оплаты';
    }

    if (empty($errors)) {
        // Если выбрана "Иная услуга", сохраняем текст, иначе NULL
        $other_service = ($service_id === 'other') ? $other_service_text : null;

        // Если выбран конкретный сервис, приводим service_id к int, иначе NULL
        $service_id_db = ($service_id === 'other') ? null : (int)$service_id;

        // Вставляем заявку в базу
        $stmt = $pdo->prepare("INSERT INTO orders 
            (user_id, address, phone, service_id, other_service, date_time, payment_type, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'new')");
        $stmt->execute([
            $user_id,
            $address,
            $phone,
            $service_id_db,
            $other_service,
            $date_time,
            $payment_type
        ]);

        echo "<p style='color:green;'>Заявка успешно создана!</p>";
        echo '<p><a href="orders.php">Вернуться к списку заявок</a></p>';
        exit;
    } else {
        // Выводим ошибки
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        echo '<p><a href="javascript:history.back()">Вернуться назад</a></p>';
    }
}

