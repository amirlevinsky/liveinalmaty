<?php
session_start();
require_once "config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];
$targetUserId = $_GET['user'] ?? null;

if (!$targetUserId) {
    echo "Пользователь не выбран.";
    exit;
}

// Получаем имя получателя
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$targetUserId]);
$targetUser = $stmt->fetch();

if (!$targetUser) {
    echo "Пользователь не найден.";
    exit;
}

// Отправка сообщения
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$currentUserId, $targetUserId, $message]);
    header("Location: chat.php?user=" . $targetUserId);
    exit;
}

// Получение истории сообщений
$stmt = $pdo->prepare("
    SELECT * FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) 
       OR (sender_id = ? AND receiver_id = ?) 
    ORDER BY sent_at ASC
");
$stmt->execute([$currentUserId, $targetUserId, $targetUserId, $currentUserId]);
$messages = $stmt->fetchAll();
?>

<h2>Чат с <?= htmlspecialchars($targetUser['name']) ?></h2>

<div style="max-width:600px; border:1px solid #ccc; padding:10px; margin-bottom:10px;">
  <?php foreach ($messages as $msg): ?>
    <div><strong><?= $msg['sender_id'] == $currentUserId ? "Вы" : $targetUser['name'] ?>:</strong> <?= htmlspecialchars($msg['message']) ?></div>
  <?php endforeach; ?>
</div>

<form method="POST">
    <textarea name="message" rows="4" style="width:100%;" required></textarea><br>
    <button type="submit">Отправить</button>
</form>
