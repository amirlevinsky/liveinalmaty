<?php
session_start();
require 'config.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Вход — Агентство недвижимости</title>
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
  <h1>Вход</h1>

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

  <form action="login_handler.php" method="POST" novalidate>
    <label for="phone">Телефон</label>
    <input type="text" id="phone" name="phone" required />

    <label for="password">Пароль</label>
    <input type="password" id="password" name="password" required />

    <button type="submit">Войти</button>
  </form>

  <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</body>
</html>
