<?php

// ПІДКЛЮЧЕННЯ
$conn = new mysqli("MySQL-8.2", "root", "", "shop");
$conn->set_charset("utf8");

// СПИСОК КАТЕГОРІЙ ДЛЯ ВИПАДАЮЧОГО СПИСКУ
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// ЯКЩО ФОРМА ВІДПРАВЛЕНА
if (isset($_POST["add"])) {

    // ДАНІ З INPUT
    $name        = $_POST["name"];
    $price       = $_POST["price"];
    $description = $_POST["description"];

    // Порожнє значення зі списку -> NULL (товар без категорії)
    $category_id = $_POST["category_id"] !== "" ? (int) $_POST["category_id"] : null;

    // ОБРОБКА ЗАВАНТАЖЕНОГО ФАЙЛУ
    $imageName = "";

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {

        // Оригінальна назва файлу
        $originalName = basename($_FILES["image"]["name"]);

        // Розширення файлу (.jpg, .png тощо)
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);

        // Унікальна назва, щоб уникнути конфліктів (наприклад: 1717234567_назва.jpg)
        $imageName = time() . "_" . $originalName;

        // Шлях до папки images у проекті
        $uploadDir = __DIR__ . "/images/";

        // Якщо папки немає — створюємо
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Повний шлях до файлу
        $uploadPath = $uploadDir . $imageName;

        // Переміщуємо завантажений файл із тимчасової папки в images/
        move_uploaded_file($_FILES["image"]["tmp_name"], $uploadPath);
    }

    // SQL — зберігаємо тільки назву файлу + id категорії
    // (підготовлений запит замість підстановки змінних у SQL)
    $stmt = $conn->prepare(
        "INSERT INTO products(name, price, description, image, category_id)
         VALUES(?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sdssi", $name, $price, $description, $imageName, $category_id);
    $stmt->execute();

    // ПЕРЕХІД НАЗАД
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>

    <meta charset="UTF-8">
    <title>Додавання товару</title>

    <style>

        body {
            font-family: Arial;
            background: #f5f5f5;
        }

        form {
            width: 400px;
            background: white;
            padding: 20px;
            margin: 50px auto;
            border-radius: 15px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            background: green;
            color: white;
            border-radius: 10px;
            cursor: pointer;
        }

    </style>

</head>
<body>

    <!--
        enctype="multipart/form-data" — ОБОВ'ЯЗКОВО для форм із завантаженням файлів.
        Без цього $_FILES буде порожнім.
    -->
    <form method="POST" enctype="multipart/form-data">

        <h2>Додати товар</h2>

        <!-- НАЗВА -->
        <input
            type="text"
            name="name"
            placeholder="Назва"
        >

        <!-- ЦІНА -->
        <input
            type="number"
            name="price"
            placeholder="Ціна"
        >

        <!-- КАТЕГОРІЯ -->
        <select name="category_id">

            <option value="">Без категорії</option>

            <?php foreach ($categories as $cat): ?>

                <option value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <!-- ОПИС -->
        <textarea
            name="description"
            placeholder="Опис"
        ></textarea>

        <!--
            ФОТО — тип file замість text.
            name="image" — саме цей ключ буде в $_FILES["image"]
        -->
        <input
            type="file"
            name="image"
            accept="image/*"
        >

        <!-- КНОПКА -->
        <button type="submit" name="add">

            Додати

        </button>

    </form>

</body>
</html>