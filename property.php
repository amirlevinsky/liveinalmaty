<?php
session_start();
require_once "config.php";

if (empty($_GET['id'])) {
    exit("Объявление не найдено.");
}

$propertyId = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT p.*, u.name as owner_name, u.id as owner_id FROM properties p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$propertyId]);
$property = $stmt->fetch();

if (!$property) {
    exit("Объявление не найдено.");
}

$currentUserId = $_SESSION['user_id'] ?? null;
$isOwner = $currentUserId && $currentUserId == $property['owner_id'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($property['title']) ?></title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
    .container { max-width: 700px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
    img { max-width: 100%; border-radius: 8px; margin-bottom: 20px; }
    .info p { margin: 6px 0; }
    .chat-btn {
      display: inline-block;
      background: #007bff;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <?php if ($property['photo']): ?>
      <img src="uploads/<?= htmlspecialchars($property['photo']) ?>" alt="Фото">
    <?php endif; ?>
    <h2><?= htmlspecialchars($property['title']) ?></h2>
    <div class="info">
      <p><strong>Цена:</strong> <?= number_format($property['price'], 0, '.', ' ') ?> ₸</p>
      <p><strong>Адрес:</strong> <?= htmlspecialchars($property['address']) ?></p>
      <p><strong>Комнат:</strong> <?= (int)$property['rooms'] ?></p>
      <p><strong>Площадь:</strong> <?= (float)$property['area'] ?> м²</p>
      <p><strong>Телефон:</strong> <?= htmlspecialchars($property['phone']) ?></p>
      <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($property['description'])) ?></p>
      <p><strong>Продавец:</strong> <?= htmlspecialchars($property['owner_name']) ?></p>
    </div>

    <?php if (!$isOwner): ?>
      <p><a class="chat-btn" href="chat.php?to=<?= $property['owner_id'] ?>">Написать сообщение</a></p>
    <?php endif; ?>

    <p><a href="dashboard.php">← Назад в личный кабинет</a></p>
  </div>
</body>
</html>
