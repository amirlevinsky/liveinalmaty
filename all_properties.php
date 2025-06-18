<?php
session_start();
require_once "config.php";

$stmt = $pdo->query("SELECT p.*, u.name AS owner_name FROM properties p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC");
$properties = $stmt->fetchAll();

$currentUserName = $_SESSION['user_name'] ?? null;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Все объявления</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
    nav a {
      margin-right: 15px;
      text-decoration: none;
      color: blue;
      font-weight: bold;
    }
    nav a.logout {
      color: red;
      float: right;
    }
    .header {
      height: 200px;
      background: url('header_building.jpg') center/cover no-repeat;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2em;
      font-weight: bold;
      margin-bottom: 20px;
      border-radius: 10px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }
    .cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .card {
      border: 1px solid #ddd;
      padding: 10px;
      width: calc(25% - 20px);
      box-sizing: border-box;
      border-radius: 6px;
      background: #fff;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
      transition: box-shadow 0.3s ease;
    }
    .card:hover {
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }
    .card img {
      max-width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 5px;
    }
    .card h3 {
      margin: 10px 0 5px 0;
      font-size: 1.1em;
    }
    .card p {
      margin: 4px 0;
      font-size: 0.9em;
      color: #333;
    }
    .btn-dashboard {
      display: inline-block;
      padding: 10px 15px;
      background-color: #004d99;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin-bottom: 15px;
    }
    .btn-dashboard:hover {
      background-color: #0066cc;
    }
  </style>
</head>
<body>

<nav>
  <?php if ($currentUserName): ?>
    <span>Добро пожаловать, <?=htmlspecialchars($currentUserName)?>!</span>
    <a href="dashboard.php" style="margin-left:20px; font-weight:bold; color:#007bff; text-decoration:none;">Личный кабинет</a>
    <a href="logout.php" class="logout">Выйти</a>
  <?php else: ?>
    <a href="login.php">Войти</a>
    <a href="register.php" style="margin-left:10px;">Регистрация</a>
  <?php endif; ?>
</nav>

<div class="header">
  Ваш надёжный портал недвижимости
</div>

<div class="cards">
  <?php if ($properties): ?>
    <?php foreach ($properties as $prop): ?>
      <div class="card">
        <?php if ($prop['photo']): ?>
          <img src="uploads/<?=htmlspecialchars($prop['photo'])?>" alt="Фото объекта">
        <?php else: ?>
          <div style="height:150px; background:#ccc; display:flex; align-items:center; justify-content:center; color:#666;">Нет фото</div>
        <?php endif; ?>
        <h3><?=htmlspecialchars($prop['title'])?></h3>
        <p><strong>Цена:</strong> <?=number_format($prop['price'], 0, '.', ' ')?> ₸</p>
        <p><strong>Адрес:</strong> <?=htmlspecialchars($prop['address'])?></p>
        <p><strong>Телефон:</strong> <?=htmlspecialchars($prop['phone'])?></p>
        <p><a href="property.php?id=<?= $prop['id'] ?>">Подробнее</a></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Объявлений пока нет.</p>
  <?php endif; ?>
</div>

</body>
</html>
