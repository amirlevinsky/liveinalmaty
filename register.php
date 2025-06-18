<?php
session_start();
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$name || !$phone || !$password || !$password_confirm) {
        $_SESSION['error'] = 'Пожалуйста, заполните все поля.';
        header("Location: register.php");
        exit;
    }

    if ($password !== $password_confirm) {
        $_SESSION['error'] = 'Пароли не совпадают.';
        header("Location: register.php");
        exit;
    }

    // Проверка уникальности телефона
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Пользователь с таким номером телефона уже существует.';
        header("Location: register.php");
        exit;
    }

    // Хэшируем пароль
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Вставляем нового пользователя
    $stmt = $pdo->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone, $password_hash]);

    $_SESSION['success'] = 'Регистрация прошла успешно. Теперь войдите в систему.';
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Регистрация — Агентство недвижимости</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
    form { background: #fff; padding: 20px; max-width: 400px; margin: auto; border-radius: 8px; }
    label { display: block; margin-top: 15px; font-weight: bold; }
    input { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
    button { margin-top: 20px; padding: 10px 15px; background-color: #004d99; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background-color: #0066cc; }
    .error { color: red; }
    .success { color: green; }
  </style>
</head>
<body>
  <h1>Регистрация</h1>

  <?php
  if (!empty($_SESSION['error'])) {
      echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
      unset($_SESSION['error']);
  }
  if (!empty($_SESSION['success'])) {
      echo '<p class="success">' . htmlspecialchars($_SESSION['success']) . '</p>';
      unset($_SESSION['success']);
  }
  ?>

  <form action="register.php" method="POST" novalidate>
    <label for="name">Имя</label>
    <input type="text" id="name" name="name" required />

    <label for="phone">Номер телефона</label>
    <input type="tel" id="phone" name="phone" required pattern="[0-9+\-\s]{7,15}" placeholder="+7 777 777 77 77" />

    <label for="password">Пароль</label>
    <input type="password" id="password" name="password" required />

    <label for="password_confirm">Подтвердите пароль</label>
    <input type="password" id="password_confirm" name="password_confirm" required />

    <button type="submit">Зарегистрироваться</button>
  </form>

  <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
</body>
</html>
