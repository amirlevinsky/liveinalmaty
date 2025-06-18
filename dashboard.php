<?php
session_start();
require_once "config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Пользователь';

// Получаем объекты пользователя
$stmt = $pdo->prepare("SELECT * FROM properties WHERE user_id = ?");
$stmt->execute([$user_id]);
$properties = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Личный кабинет</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
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
    section {
      margin-top: 20px;
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
    .btn-add {
      display: inline-block;
      padding: 10px 15px;
      background-color: #004d99;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin-bottom: 15px;
    }
    .btn-add:hover {
      background-color: #0066cc;
    }
  </style>
</head>
<body>

<nav>
  <span>Добро пожаловать, <?=htmlspecialchars($user_name)?>!</span>
  <a href="all_properties.php" style="margin-left:20px; font-weight:bold; color:#007bff; text-decoration:none;">Все объявления</a>
  <a href="logout.php" class="logout">Выйти</a>
</nav>

<section>
  <a href="add_property.php" class="btn-add">Добавить объект</a>

  <?php if ($properties): ?>
    <h2>Мои объекты недвижимости</h2>
    <div class="cards">
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
    </div>
  <?php else: ?>
    <p>У вас пока нет добавленных объектов.</p>
  <?php endif; ?>
</section>

</body>
</html>
