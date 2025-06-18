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
<title>Мои объекты</title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
  nav {
    margin-bottom: 20px;
  }
  nav a {
    margin-right: 15px;
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
  }
  nav a.logout {
    color: red;
    float: right;
  }
  .cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
  }
  .card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgb(0 0 0 / 0.15);
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }
  .card-content {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  .card-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
  }
  .card-desc {
    flex-grow: 1;
    font-size: 14px;
    margin-bottom: 10px;
    color: #555;
  }
  .card-info {
    font-size: 14px;
    margin-bottom: 5px;
    color: #666;
  }
  .btn-details {
    background-color: #007bff;
    color: white;
    padding: 8px 12px;
    text-align: center;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    margin-top: auto;
  }
  .btn-details:hover {
    background-color: #0056b3;
  }
</style>
</head>
<body>

<nav>
  <span>Добро пожаловать, <?=htmlspecialchars($user_name)?>!</span>
  <a href="dashboard.php" style="margin-left:20px;">Личный кабинет</a>
  <a href="all_properties.php" style="margin-left:20px;">Все объявления</a>
  <a href="logout.php" class="logout">Выйти</a>
</nav>

<h1>Мои объекты недвижимости</h1>

<?php if ($properties): ?>
  <div class="cards-container">
    <?php foreach ($properties as $prop): ?>
      <div class="card">
        <?php if ($prop['photo']): ?>
          <img src="uploads/<?=htmlspecialchars($prop['photo'])?>" alt="Фото объекта">
        <?php else: ?>
          <img src="https://via.placeholder.com/250x180?text=Нет+фото" alt="Нет фото">
        <?php endif; ?>
        <div class="card-content">
          <div class="card-title"><?=htmlspecialchars($prop['title'])?></div>
          <div class="card-desc"><?=htmlspecialchars(mb_strimwidth($prop['description'], 0, 80, '...'))?></div>
          <div class="card-info"><strong>Цена:</strong> <?=number_format($prop['price'], 0, '.', ' ')?> ₸</div>
          <div class="card-info"><strong>Адрес:</strong> <?=htmlspecialchars($prop['address'])?></div>
          <div class="card-info"><strong>Комнат:</strong> <?=htmlspecialchars($prop['rooms'])?></div>
          <div class="card-info"><strong>Площадь:</strong> <?=htmlspecialchars($prop['area'])?> м²</div>
          <a href="property.php?id=<?= (int)$prop['id'] ?>" class="btn-details">Подробнее</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <p>У вас пока нет добавленных объектов.</p>
<?php endif; ?>

</body>
</html>
