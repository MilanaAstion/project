<?php
// Підключення до MySQL
$host = "MySQL-8.2";
$user = "root";
$password = "";
$dbname = "shop";

// Створення підключення
$conn = new mysqli($host, $user, $password, $dbname);

// Кодування UTF-8
$conn->set_charset("utf8");

// Перевірка підключення
if ($conn->connect_error) {
    die("Помилка підключення!");
}

// СОРТУВАННЯ ТОВАРІВ
// Ключ (зліва) — те, що приходить із випадаючого списку (?sort=...).
// Значення (справа) — те, що підставляється в SQL-запит ORDER BY.
// p. — псевдонім таблиці products (потрібен, бо тепер є JOIN з categories)
$sortOptions = [
    "id_asc"     => "products.id ASC",
    "name_asc"   => "products.name ASC",
    "name_desc"  => "products.name DESC",
    "price_asc"  => "products.price ASC",
    "price_desc" => "products.price DESC",
];

$selectedSort = $_GET["sort"] ?? "id_asc";

if (!isset($sortOptions[$selectedSort])) {
    $selectedSort = "id_asc";
}

$sql = "SELECT products.*, categories.name AS category_name
        FROM products
        LEFT JOIN categories ON categories.id = products.category_id
        ORDER BY " . $sortOptions[$selectedSort];

$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<title>Магазин</title>
<style>

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        background: #f4f5f7;
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #222;
    }

    /* ===== ХЕДЕР ===== */
    .topbar {
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        padding: 22px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
    }

    .topbar h1 {
        color: #fff;
        font-size: 24px;
        letter-spacing: 0.5px;
    }

    .topbar .admin-btn {
        background: #e94560;
        color: white;
        text-decoration: none;
        padding: 10px 22px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: bold;
        transition: 0.2s;
    }

    .topbar .admin-btn:hover {
        background: #c73652;
    }

    /* ===== ОБГОРТКА ===== */
    .wrap {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
    }

    /* ===== ПАНЕЛЬ СОРТУВАННЯ ===== */
    .sort-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 30px;
    }

    .sort-bar label {
        color: #666;
        font-size: 14px;
    }

    .sort-bar select {
        background: #fff;
        color: #222;
        border: 1px solid #ddd;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .sort-bar select:focus {
        outline: 2px solid #007bff;
    }

    /* ===== СІТКА ТОВАРІВ ===== */
    .products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }

    .card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transition: transform 0.25s, box-shadow 0.25s;
        display: flex;
        flex-direction: column;
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.14);
    }

    .card-img-link {
        display: block;
    }

    .card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .content {
        padding: 18px 20px 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    /* ===== КАТЕГОРІЯ ===== */
    .category {
        align-self: flex-start;
        background: #e8f1ff;
        color: #0056b3;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 20px;
        margin-bottom: 10px;
    }

    .category.none {
        background: #f0f0f0;
        color: #999;
    }

    .title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #1a1a2e;
    }

    .title a {
        color: inherit;
        text-decoration: none;
    }

    .title a:hover {
        color: #007bff;
    }

    .price {
        color: #16a34a;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .description {
        color: #777;
        font-size: 14px;
        margin-bottom: 18px;
        line-height: 1.4;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        flex-grow: 1;
    }

    .btn-row {
        display: flex;
        gap: 8px;
    }

    .view-link {
        flex: 1;
        text-align: center;
        padding: 11px;
        border-radius: 10px;
        border: 1px solid #ddd;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: 0.2s;
    }

    .view-link:hover {
        background: #f0f0f0;
    }

    button.buyBtn {
        flex: 1;
        border: none;
        padding: 11px;
        background: #007bff;
        color: white;
        border-radius: 10px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        transition: 0.2s;
    }

    button.buyBtn:hover {
        background: #0056b3;
    }

    /* ===== ПОРОЖНІЙ СТАН ===== */
    .empty {
        text-align: center;
        padding: 80px 20px;
        color: #999;
        font-size: 16px;
    }

</style>
</head>
<body>

    <div class="topbar">
        <h1>🛍️ Наші товари</h1>
        <a href="admin.php" class="admin-btn">⚙️ Адмінка</a>
    </div>

    <div class="wrap">

        <!-- ПАНЕЛЬ СОРТУВАННЯ -->
        <form method="GET" action="index.php" class="sort-bar">
            <label for="sort">Сортувати за:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="id_asc" <?= $selectedSort === 'id_asc' ? 'selected' : '' ?>>За замовчуванням</option>
                <option value="name_asc" <?= $selectedSort === 'name_asc' ? 'selected' : '' ?>>Назва (А-Я)</option>
                <option value="name_desc" <?= $selectedSort === 'name_desc' ? 'selected' : '' ?>>Назва (Я-А)</option>
                <option value="price_asc" <?= $selectedSort === 'price_asc' ? 'selected' : '' ?>>Ціна (дешевші спочатку)</option>
                <option value="price_desc" <?= $selectedSort === 'price_desc' ? 'selected' : '' ?>>Ціна (дорожчі спочатку)</option>
            </select>
        </form>

        <!-- СІТКА ТОВАРІВ -->
        <?php if (count($products) > 0): ?>

            <div class="products">

                <?php foreach ($products as $row): ?>

                    <div class="card">

                        <a href="item.php?id=<?= $row['id'] ?>" class="card-img-link">
                            <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        </a>

                        <div class="content">

                            <!-- КАТЕГОРІЯ ТОВАРУ -->
                            <?php if ($row['category_name']): ?>
                                <div class="category"><?= htmlspecialchars($row['category_name']) ?></div>
                            <?php else: ?>
                                <div class="category none">Без категорії</div>
                            <?php endif; ?>

                            <div class="title">
                                <a href="item.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
                            </div>

                            <div class="price"><?= number_format($row['price'], 0, '.', ' ') ?> грн</div>

                            <div class="description"><?= htmlspecialchars($row['description']) ?></div>

                            <div class="btn-row">
                                <a href="item.php?id=<?= $row['id'] ?>" class="view-link">Переглянути</a>
                                <button class="buyBtn">Купити</button>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty">Товарів немає</div>

        <?php endif; ?>

    </div>

    <script>
        // Усі кнопки
        let buttons = document.querySelectorAll(".buyBtn");

        // Перебір кнопок
        buttons.forEach(function(button) {
            // Клік
            button.addEventListener("click", function() {
                alert("Товар додано!");
            });
        });
    </script>

</body>
</html>