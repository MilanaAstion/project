<?php

// ПІДКЛЮЧЕННЯ
$conn = new mysqli("MySQL-8.2", "root", "", "shop");
$conn->set_charset("utf8");

// ID З URL
$id = (int) $_GET["id"];

// СПИСОК КАТЕГОРІЙ ДЛЯ ВИПАДАЮЧОГО СПИСКУ
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// ЯКЩО ФОРМА ВІДПРАВЛЕНА
if (isset($_POST["save"])) {

    $name        = $_POST["name"];
    $price       = $_POST["price"];
    $description = $_POST["description"];
    $image       = $_POST["image"];

    // Порожнє значення зі списку -> NULL (товар без категорії)
    $category_id = $_POST["category_id"] !== "" ? (int) $_POST["category_id"] : null;

    // SQL ОНОВЛЕННЯ (підготовлений запит)
    $stmt = $conn->prepare(
        "UPDATE products
         SET name = ?, price = ?, description = ?, image = ?, category_id = ?
         WHERE id = ?"
    );
    $stmt->bind_param("sdssii", $name, $price, $description, $image, $category_id, $id);
    $stmt->execute();

    // ПЕРЕХІД НАЗАД
    header("Location: admin.php");
    exit();
}

// ОТРИМАННЯ ТОВАРУ
$result = $conn->query("SELECT * FROM products WHERE id=$id");
$product = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="uk">
<head>

    <meta charset="UTF-8">
    <title>Редагування товару</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #222;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 15px;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #007bff;
        }

        textarea {
            height: 90px;
            resize: vertical;
        }

        .buttons {
            display: flex;
            gap: 10px;
        }

        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            background: #007bff;
            color: white;
            transition: 0.2s;
        }

        button:hover {
            background: #0056b3;
        }

        a.back {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            text-align: center;
            text-decoration: none;
            background: #eee;
            color: #555;
            transition: 0.2s;
        }

        a.back:hover {
            background: #ddd;
        }

    </style>

</head>
<body>

    <form method="POST">

        <h2>✏️ Редагування товару</h2>

        <!-- НАЗВА -->
        <label>Назва</label>
        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($product['name']) ?>"
        >

        <!-- ЦІНА -->
        <label>Ціна (грн)</label>
        <input
            type="number"
            name="price"
            step="0.01"
            value="<?= htmlspecialchars($product['price']) ?>"
        >

        <!-- КАТЕГОРІЯ -->
        <label>Категорія</label>
        <select name="category_id">

            <option value="">Без категорії</option>

            <?php foreach ($categories as $cat): ?>

                <!-- selected — якщо це поточна категорія товару -->
                <option
                    value="<?= $cat['id'] ?>"
                    <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($cat['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <!-- ФОТО -->
        <label>Посилання на фото</label>
        <input
            type="text"
            name="image"
            value="<?= htmlspecialchars($product['image']) ?>"
        >

        <!-- ОПИС -->
        <label>Опис</label>
        <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>

        <!-- КНОПКИ -->
        <div class="buttons">
            <a href="admin.php" class="back">← Назад</a>
            <button type="submit" name="save">Зберегти</button>
        </div>

    </form>

</body>
</html>