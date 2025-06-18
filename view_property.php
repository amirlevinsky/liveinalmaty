<?php
session_start();
require_once "config.php";

if (empty($_GET['id'])) {
    exit("Объявление не найдено.");
}

$property_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT p.*, u.name AS owner_name, u.id AS owner_id FROM properties p 
                       JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch();

if (!$property) {
    exit("Объявление не найдено.");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($property['title']); ?></title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
    .container { max-width: 700px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    img { max-width: 100%; border-radius: 5px; margin-bottom: 15px; }
    h1 { margin-top: 0; }
    .meta { margin: 10px 0; }
    .button {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 15px;
      background: #004d99;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .button:hover { background: #0066cc; }
  </style>
</head>
<body>

<div class="container">
  <?php if ($property['photo']): ?>
    <img src="uploads/<?php echo htmlspecialchars($property['photo']); ?>" alt="Фото">
  <?php endif; ?>

  <h1><?php echo htmlspecialchars($property['title']); ?></h1>
  <p class="meta"><strong>Цена:</strong> <?php echo htmlspecialchars($property['price']); ?> тг</p>
  <p class="meta"><strong>Адрес:</strong> <?php echo htmlspecialchars($property['address']); ?></p>
  <p class="meta"><strong>Комнат:</strong> <?php echo htmlspecialchars($property['rooms']); ?></p>
  <p class="meta"><strong>Площадь:</strong> <?php echo htmlspecialchars($property['area']); ?> м²</p>
  <p class="meta"><strong>Описание:</strong> <?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
  <p class="meta"><strong>Владелец:</strong> <?php echo htmlspecialchars($property['owner_name']); ?></p>

  <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $property['owner_id']): ?>
    <a class="button" href="send_message.php?to=<?php echo $property['owner_id']; ?>">Написать сообщение</a>
  <?php endif; ?>

  <p><a href="dashboard.php" class="button">Назад в кабинет</a></p>
</div>

</body>
</html>
