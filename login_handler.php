<?php
session_start();
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$phone || !$password) {
        $_SESSION['error'] = 'Пожалуйста, введите номер телефона и пароль.';
        header("Location: login.php");
        exit;
    }

    // Ищем пользователя по номеру телефона
    $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Успешный вход
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = 'Неверный номер телефона или пароль.';
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
