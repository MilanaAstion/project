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
    $image       = $_POST["image"];

    // Порожня категорія -> NULL
    $category_id = $_POST["category_id"] !== "" ? (int) $_POST["category_id"] : null;

    // ДОДАВАННЯ (підготовлений запит замість підстановки змінних у SQL)
    $stmt = $conn->prepare(
        "INSERT INTO products(name, price, description, image, category_id)
         VALUES(?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sdssi", $name, $price, $description, $image, $category_id);
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

        body{
            font-family: Arial;
            background: #f5f5f5;
        }

        form{
            width: 400px;
            background: white;
            padding: 20px;
            margin: 50px auto;
            border-radius: 15px;
        }

        input, textarea, select{
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button{
            width: 100%;
            padding: 12px;
            border: none;
            background: green;
            color: white;
            border-radius: 10px;
        }

        .hint{
            font-size: 13px;
            color: #888;
            margin: -8px 0 15px;
        }

        .hint a{
            color: #007bff;
        }

    </style>

</head>
<body>

    <form method="POST">

        <h2>Додати товар</h2>

        <!-- НАЗВА -->
        <input type="text" name="name" placeholder="Назва" required>

        <!-- ЦІНА -->
        <input type="number" name="price" placeholder="Ціна" step="0.01" required>

        <!-- КАТЕГОРІЯ -->
        <select name="category_id">
            <option value="">Без категорії</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="hint">
            Немає потрібної категорії? <a href="admin.php">Додайте її в адмінці</a>
        </div>

        <!-- ОПИС -->
        <textarea name="description" placeholder="Опис"></textarea>

        <!-- ФОТО -->
        <input type="text" name="image" placeholder="Посилання на фото">

        <!-- КНОПКА -->
        <button type="submit" name="add">Додати</button>

    </form>

</body>
</html>