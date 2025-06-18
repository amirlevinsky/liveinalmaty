<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: my_properties.php");
    exit;
}

require 'config.php';

// Получаем объект и проверяем, принадлежит ли он текущему пользователю
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$property = $stmt->fetch();

if (!$property) {
    // Либо объект не найден, либо чужой
    echo "Объект не найден или вы не можете его удалить.";
    exit;
}

// Удаляем
$stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
$stmt->execute([$_GET['id']]);

// Возвращаемся обратно
header("Location: my_properties.php");
exit;
