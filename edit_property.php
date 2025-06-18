<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: my_properties.php');
    exit;
}

$id = (int)$_GET['id'];

// Получаем объект
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$property = $stmt->fetch();

if (!$property) {
    echo "Объект не найден или у вас нет доступа.";
    exit;
}

// Обработка удаления отдельного фото
if (isset($_GET['delete_photo'])) {
    $deletePhoto = $_GET['delete_photo'];
    $photos = explode(',', $property['photos']);
    $photos = array_filter($photos, fn($p) => $p !== $deletePhoto);
    $newPhotoString = implode(',', $photos);

    // Обновляем фото в БД
    $stmt = $pdo->prepare("UPDATE properties SET photos = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$newPhotoString, $id, $_SESSION['user_id']]);

    // Удаляем файл с сервера
    if (file_exists("uploads/$deletePhoto")) {
        unlink("uploads/$deletePhoto");
    }

    header("Location: edit_property.php?id=$id");
    exit;
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $address = $_POST['address'];
    $rooms = $_POST['rooms'];
    $area = $_POST['area'];

    // Загружаем новые фото, если есть
    $photoString = $property['photos'];
    if (!empty($_FILES['photos']['name'][0])) {
        $uploaded = [];
        foreach ($_FILES['photos']['name'] as $key => $name) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir);

            $filename = time() . '_' . basename($name);
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($_FILES['photos']['tmp_name'][$key], $targetFile)) {
                $uploaded[] = $filename;
            }
        }

        $existing = explode(',', $property['photos']);
        $merged = array_merge($existing, $uploaded);
        $photoString = implode(',', $merged);
    }

    // Обновляем данные
    $stmt = $pdo->prepare("UPDATE properties SET title = ?, description = ?, price = ?, address = ?, rooms = ?, area = ?, photos = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $description, $price, $address, $rooms, $area, $photoString, $id, $_SESSION['user_id']]);

    header('Location: my_properties.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать объявление</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f7f7f7; }
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; background: #004d99; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        img { margin: 5px; border: 1px solid #ccc; }
        .photo-wrapper { display: inline-block; position: relative; }
        .photo-wrapper a { position: absolute; top: 0; right: 0; background: red; color: white; padding: 2px 5px; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Редактировать объявление</h2>

<form method="post" enctype="multipart/form-data">
    <label>Название:
        <input type="text" name="title" value="<?=htmlspecialchars($property['title'])?>" required>
    </label>

    <label>Описание:
        <textarea name="description" required><?=htmlspecialchars($property['description'])?></textarea>
    </label>

    <label>Цена:
        <input type="number" name="price" value="<?=htmlspecialchars($property['price'])?>" required>
    </label>

    <label>Адрес:
        <input type="text" name="address" value="<?=htmlspecialchars($property['address'])?>" required>
    </label>

    <label>Комнаты:
        <input type="number" name="rooms" value="<?=htmlspecialchars($property['rooms'])?>" required>
    </label>

    <label>Площадь (м²):
        <input type="number" name="area" value="<?=htmlspecialchars($property['area'])?>" required>
    </label>

    <label>Добавить новые фото:
        <input type="file" name="photos[]" multiple accept="image/*">
    </label>

    <?php if (!empty($property['photos'])): ?>
        <p>Текущие фото:</p>
        <?php
        $photos = explode(',', $property['photos']);
        foreach ($photos as $p):
        ?>
            <div class="photo-wrapper">
                <a href="?id=<?=$id?>&delete_photo=<?=$p?>" onclick="return confirm('Удалить фото?')">x</a>
                <img src="uploads/<?=htmlspecialchars($p)?>" width="120">
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <button type="submit">Сохранить изменения</button>
</form>

</body>
</html>
