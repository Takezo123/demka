<?php
include __DIR__ . '/header.php'; 
session_start();
unset($_SESSION['is_admin']);
header('Location: admin_login.php');
exit;
