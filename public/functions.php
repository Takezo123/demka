## core/functions.php
function auth_check() {
    session_start();
    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function admin_check() {
    session_start();
    if(!isset($_SESSION['is_admin'])) {
        header('Location: admin_login.php');
        exit;
    }
}

function get_services() {
    global $pdo;
    return $pdo->query("SELECT * FROM services")->fetchAll();
}
