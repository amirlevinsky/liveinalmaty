<?php
$host = 'localhost';     // Обычно локально именно так
$db   = 'liveinalmaty';  // Название твоей базы данных
$user = 'root';          // Имя пользователя MySQL (по умолчанию root)
$pass = '';              // Пароль MySQL (по умолчанию пустой, если не менял)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Варианты настроек PDO для ошибок и кодировки
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ошибки будут в исключениях
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Результат в виде ассоц. массива
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Отключаем эмуляцию подготовленных запросов
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // При ошибке подключения покажет сообщение
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
