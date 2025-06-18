<?php
session_start();
require_once "config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT id, name FROM users WHERE id != " . $_SESSION['user_id']);
$users = $stmt->fetchAll();
?>

<h2>Пользователи</h2>
<ul>
  <?php foreach ($users as $user): ?>
    <li><a href="chat.php?user=<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></a></li>
  <?php endforeach; ?>
</ul>
