<?php
session_start();
require_once "config.php";

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $address = trim($_POST['address'] ?? '');
    $rooms = (int)($_POST['rooms'] ?? 0);
    $area = floatval($_POST['area'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if (!$title || !$price || !$address) {
        $error = "Пожалуйста, заполните обязательные поля.";
    } else {
        $userId = $_SESSION['user_id'];

        // Обработка нескольких файлов
        $photos = [];
        if (!empty($_FILES['photos']['name'][0])) {
            $uploadDir = __DIR__ . "/uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['photos']['name'] as $key => $filename) {
                $tmpName = $_FILES['photos']['tmp_name'][$key];
                $errorFile = $_FILES['photos']['error'][$key];

                if ($errorFile === UPLOAD_ERR_OK) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $newFilename = uniqid() . "." . $ext;
                    $destination = $uploadDir . $newFilename;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $photos[] = $newFilename;
                    }
                }
            }
        }

        // Сохраняем данные в БД, сохраним фото как JSON-массив
        $photosJson = json_encode($photos);

        $stmt = $pdo->prepare("INSERT INTO properties 
          (user_id, title, price, address, rooms, area, description, photos) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $title, $price, $address, $rooms, $area, $description, $photosJson]);

        $_SESSION['success'] = "Объявление успешно добавлено!";
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Добавить объект</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
    form { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
    label { display: block; margin-top: 15px; font-weight: bold; }
    input, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
    button { margin-top: 20px; padding: 10px 15px; background-color: #004d99; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background-color: #0066cc; }
    .error { color: red; }
    .success { color: green; }
  </style>
</head>
<body>

<h1>Добавить объект недвижимости</h1>

<?php if (!empty($error)): ?>
    <p class="error"><?=htmlspecialchars($error)?></p>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data" novalidate>
    <label for="title">Название</label>
    <input type="text" id="title" name="title" required value="<?=htmlspecialchars($_POST['title'] ?? '')?>">

    <label for="price">Цена (₸)</label>
    <input type="number" step="0.01" id="price" name="price" required value="<?=htmlspecialchars($_POST['price'] ?? '')?>">

    <label for="address">Адрес</label>
    <input type="text" id="address" name="address" required value="<?=htmlspecialchars($_POST['address'] ?? '')?>">

    <label for="rooms">Комнаты</label>
    <input type="number" id="rooms" name="rooms" value="<?=htmlspecialchars($_POST['rooms'] ?? '')?>">

    <label for="area">Площадь (м²)</label>
    <input type="number" step="0.01" id="area" name="area" value="<?=htmlspecialchars($_POST['area'] ?? '')?>">

    <label for="description">Описание</label>
    <textarea id="description" name="description"><?=htmlspecialchars($_POST['description'] ?? '')?></textarea>

    <label for="photos">Фотографии (можно выбрать несколько)</label>
    <input type="file" id="photos" name="photos[]" multiple accept="image/*">

    <button type="submit">Добавить объект</button>
</form>

</body>
</html>
