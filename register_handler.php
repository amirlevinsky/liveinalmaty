<?php
session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$name || !$email || !$password) {
        $_SESSION['error'] = "Пожалуйста, заполните все поля.";
        header("Location: register.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Введите корректный email.";
        header("Location: register.php");
        exit;
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = "Пароль должен содержать минимум 6 символов.";
        header("Location: register.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Пользователь с таким email уже существует.";
        header("Location: register.php");
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $passwordHash]);

    $_SESSION['success'] = "Регистрация успешна! Теперь войдите.";
    header("Location: login.php");
    exit;
} else {
    exit("Недопустимый метод запроса.");
}
?>
