<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$user = $_SESSION['user'];
$role = $user['role'];

if ($role == 'doctor') {
    header('Location: dashboard_doctor.php');
} else {
    header('Location: dashboard_informatico.php');
}
?>
