<?php
require 'config.php';

try {
    $stmt = $pdo->query("SHOW TABLES");

    // ВАЖНО: добавляем этот вариант, чтобы избежать ошибки "undefined array key"
    $tables = $stmt->fetchAll(PDO::FETCH_NUM);

    echo "<h2>Таблицы в базе данных liveinalmaty:</h2>";
    if ($tables) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table[0]) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "Таблиц нет или база пустая.";
    }
} catch (Exception $e) {
    echo "Ошибка при подключении к базе: " . htmlspecialchars($e->getMessage());
}
?>
