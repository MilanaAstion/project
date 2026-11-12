<?php
session_start();

// Якщо користувач не авторизований як адміністратор,
// перенаправляємо його на сторінку входу
if (!isset($_SESSION["admin"])) {

    header("Location: login.php");
    exit();
}

// ПІДКЛЮЧЕННЯ
$conn = new mysqli("MySQL-8.2", "root", "", "shop");
$conn->set_charset("utf8");

// ВИДАЛЕННЯ ТОВАРУ
if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"]; // (int) — захист від SQL-ін'єкцій
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: admin.php");
    exit();
}

// ДОДАВАННЯ КАТЕГОРІЇ
if (isset($_POST["add_category"])) {
    $catName = trim($_POST["category_name"]);

    if ($catName !== "") {
        // INSERT IGNORE — якщо така категорія вже є, нічого не станеться
        $stmt = $conn->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $catName);
        $stmt->execute();
    }

    header("Location: admin.php");
    exit();
}

// ВИДАЛЕННЯ КАТЕГОРІЇ
if (isset($_GET["delete_category"])) {
    $catId = (int) $_GET["delete_category"];

    // Товари цієї категорії залишаються, просто стають "Без категорії"
    $conn->query("UPDATE products SET category_id = NULL WHERE category_id = $catId");
    $conn->query("DELETE FROM categories WHERE id = $catId");

    header("Location: admin.php");
    exit();
}

// СОРТУВАННЯ ТОВАРІВ
// Ключ (зліва) — те, що приходить із випадаючого списку.
// Значення (справа) — те, що підставляється в SQL-запит.
// p. — псевдонім таблиці products (потрібен, бо тепер є JOIN з categories)
$sortOptions = [
    "id_asc"     => "p.id ASC",
    "name_asc"   => "p.name ASC",
    "name_desc"  => "p.name DESC",
    "price_asc"  => "p.price ASC",
    "price_desc" => "p.price DESC",
];

$selectedSort = $_GET["sort"] ?? "id_asc";

if (!isset($sortOptions[$selectedSort])) {
    $selectedSort = "id_asc";
}

// ОТРИМАННЯ ВСІХ ТОВАРІВ (з категорією та сортуванням)
$result = $conn->query(
    "SELECT p.*, c.name AS category_name
     FROM products p
     LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY " . $sortOptions[$selectedSort]
);
$products = $result->fetch_all(MYSQLI_ASSOC);

// ОТРИМАННЯ ВСІХ КАТЕГОРІЙ
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="uk">
<head>

    <meta charset="UTF-8">
    <title>Адмін-панель</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #1a1a2e;
            color: #eee;
            min-height: 100vh;
        }

        /* ===== ХЕДЕР ===== */
        header {
            background: #16213e;
            padding: 18px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #e94560;
            box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        }

        header h1 {
            font-size: 22px;
            color: #e94560;
            letter-spacing: 1px;
        }

        header .nav-links a {
            color: #aaa;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
            transition: color 0.2s;
        }

        header .nav-links a:hover {
            color: #e94560;
        }

        /* ===== ОСНОВНИЙ КОНТЕНТ ===== */
        .container {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .top-bar h2 {
            font-size: 20px;
            color: #ccc;
        }

        .top-bar span {
            background: #e94560;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 13px;
            margin-left: 10px;
        }

        .btn-add {
            background: #e94560;
            color: white;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn-add:hover {
            background: #c73652;
        }

        /* ===== КАТЕГОРІЇ ===== */
        .categories-box {
            background: #16213e;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .categories-box h3 {
            font-size: 16px;
            color: #ccc;
            margin-bottom: 14px;
        }

        .category-form {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .category-form input {
            flex: 1;
            background: #1a1a2e;
            color: #eee;
            border: 1px solid #0f3460;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
        }

        .category-form input:focus {
            outline: 2px solid #e94560;
        }

        .category-form button {
            background: #e94560;
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .category-form button:hover {
            background: #c73652;
        }

        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .category-chip {
            background: #0f3460;
            color: #7ecfff;
            padding: 6px 8px 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .category-chip a {
            color: #e94560;
            text-decoration: none;
            font-weight: bold;
            padding: 0 6px;
            border-radius: 50%;
        }

        .category-chip a:hover {
            background: #e94560;
            color: white;
        }

        .no-categories {
            color: #666;
            font-size: 14px;
        }

        /* ===== ТАБЛИЦЯ ===== */
        .table-wrap {
            background: #16213e;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #0f3460;
        }

        thead th {
            padding: 16px 20px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #7ecfff;
        }

        tbody tr {
            border-bottom: 1px solid #0f3460;
            transition: background 0.2s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #0f3460;
        }

        tbody td {
            padding: 14px 20px;
            font-size: 14px;
            vertical-align: middle;
        }

        /* ID колонка */
        td.col-id {
            color: #7ecfff;
            font-weight: bold;
            width: 50px;
        }

        /* Фото */
        td.col-img img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #0f3460;
        }

        /* Назва */
        td.col-name {
            font-weight: bold;
            color: #fff;
        }

        /* Категорія */
        td.col-category {
            color: #7ecfff;
        }

        td.col-category.none {
            color: #666;
        }

        /* Ціна */
        td.col-price {
            color: #4ade80;
            font-weight: bold;
        }

        /* Опис */
        td.col-desc {
            color: #999;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Дії */
        td.col-actions {
            width: 160px;
        }

        .btn-edit, .btn-delete {
            display: inline-block;
            padding: 7px 16px;
            border-radius: 7px;
            font-size: 13px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: #0f3460;
            color: #7ecfff;
            margin-right: 8px;
        }

        .btn-edit:hover {
            background: #1a5299;
        }

        .btn-delete {
            background: rgba(233, 69, 96, 0.15);
            color: #e94560;
        }

        .btn-delete:hover {
            background: #e94560;
            color: white;
        }

        /* ===== ПОРОЖНІЙ СТАН ===== */
        .empty {
            text-align: center;
            padding: 60px;
            color: #555;
        }

        .empty p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* ===== ПАНЕЛЬ СОРТУВАННЯ ===== */
        .sort-bar {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sort-bar label {
            color: #999;
            font-size: 14px;
        }

        .sort-bar select {
            background: #16213e;
            color: #eee;
            border: 1px solid #0f3460;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .sort-bar select:focus {
            outline: 2px solid #e94560;
        }

        /* ===== ПОСИЛАННЯ НА ТОВАР ===== */
        .product-link {
            color: #fff;
            text-decoration: none;
        }

        .product-link:hover {
            color: #7ecfff;
            text-decoration: underline;
        }

        /* ===== ФУТЕР ===== */
        footer {
            text-align: center;
            padding: 20px;
            color: #444;
            font-size: 13px;
            margin-top: 40px;
        }

    </style>

</head>
<body>

    <!-- ХЕДЕР -->
    <header>
        <h1>⚙️ Адмін-панель</h1>
        <nav class="nav-links">
            <a href="index.php">← Магазин</a>
            <a href="add.php">+ Додати товар з посиланням на фото</a>
            <a href="add_photo.php">+ Додати товар з фото</a>
        </nav>
    </header>

    <div class="container">

        <!-- КАТЕГОРІЇ -->
        <div class="categories-box">

            <h3>Категорії</h3>

            <form method="POST" action="admin.php" class="category-form">
                <input type="text" name="category_name" placeholder="Назва нової категорії" required>
                <button type="submit" name="add_category">+ Додати категорію</button>
            </form>

            <?php if (count($categories) > 0): ?>

                <div class="category-list">
                    <?php foreach ($categories as $cat): ?>
                        <span class="category-chip">
                            <?= htmlspecialchars($cat['name']) ?>
                            <a
                                href="admin.php?delete_category=<?= $cat['id'] ?>"
                                title="Видалити категорію"
                                onclick="return confirm('Видалити категорію «<?= htmlspecialchars($cat['name']) ?>»? Товари залишаться без категорії.')"
                            >×</a>
                        </span>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>

                <div class="no-categories">Категорій поки немає</div>

            <?php endif; ?>

        </div>

        <!-- ВЕРХНЯ ПАНЕЛЬ -->
        <div class="top-bar">
            <h2>
                Усі товари
                <span><?= count($products) ?></span>
            </h2>
            <a href="add_photo.php" class="btn-add">+ Додати товар</a>
        </div>

        <!-- ПАНЕЛЬ СОРТУВАННЯ -->
        <form method="GET" action="admin.php" class="sort-bar">
            <label for="sort">Сортувати за:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="id_asc" <?= $selectedSort === 'id_asc' ? 'selected' : '' ?>>За замовчуванням</option>
                <option value="name_asc" <?= $selectedSort === 'name_asc' ? 'selected' : '' ?>>Назва (А-Я)</option>
                <option value="name_desc" <?= $selectedSort === 'name_desc' ? 'selected' : '' ?>>Назва (Я-А)</option>
                <option value="price_asc" <?= $selectedSort === 'price_asc' ? 'selected' : '' ?>>Ціна (дешевші спочатку)</option>
                <option value="price_desc" <?= $selectedSort === 'price_desc' ? 'selected' : '' ?>>Ціна (дорожчі спочатку)</option>
            </select>
        </form>

        <!-- ТАБЛИЦЯ -->
        <div class="table-wrap">

            <?php if (count($products) > 0): ?>

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Фото</th>
                            <th>Назва</th>
                            <th>Категорія</th>
                            <th>Ціна</th>
                            <th>Опис</th>
                            <th>Дії</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($products as $row): ?>

                            <tr>

                                <!-- ID -->
                                <td class="col-id">#<?= $row['id'] ?></td>

                                <!-- ФОТО -->
                                <td class="col-img">
                                    <img
                                        src="images/<?= htmlspecialchars($row['image']) ?>"
                                        alt="<?= htmlspecialchars($row['name']) ?>"
                                        onerror="this.src='https://via.placeholder.com/60x60?text=?'"
                                    >
                                </td>

                                <!-- НАЗВА (посилання на сторінку одного товару) -->
                                <td class="col-name">
                                    <a href="product.php?id=<?= $row['id'] ?>" class="product-link">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </a>
                                </td>

                                <!-- КАТЕГОРІЯ -->
                                <?php if ($row['category_name']): ?>
                                    <td class="col-category"><?= htmlspecialchars($row['category_name']) ?></td>
                                <?php else: ?>
                                    <td class="col-category none">—</td>
                                <?php endif; ?>

                                <!-- ЦІНА -->
                                <td class="col-price"><?= number_format($row['price'], 0, '.', ' ') ?> грн</td>

                                <!-- ОПИС -->
                                <td class="col-desc" title="<?= htmlspecialchars($row['description']) ?>">
                                    <?= htmlspecialchars($row['description']) ?>
                                </td>

                                <!-- ДІЇ -->
                                <td class="col-actions">

                                    <!-- РЕДАГУВАННЯ -->
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">
                                        ✏️ Змінити
                                    </a>

                                    <!-- ВИДАЛЕННЯ з підтвердженням -->
                                    <a
                                        href="admin.php?delete=<?= $row['id'] ?>"
                                        class="btn-delete"
                                        onclick="return confirm('Видалити товар «<?= htmlspecialchars($row['name']) ?>»?')"
                                    >
                                        🗑
                                    </a>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <!-- ЯКЩО ТОВАРІВ НЕМАЄ -->
                <div class="empty">
                    <p>Товарів поки немає</p>
                    <a href="add_category.php" class="btn-add">Додати перший товар</a>
                </div>

            <?php endif; ?>

        </div>

    </div>

    <footer>
        Адмін-панель магазину &copy; <?= date('Y') ?>
    </footer>

</body>
</html>